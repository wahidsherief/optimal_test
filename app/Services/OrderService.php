<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\PromotedJobOrder;
use App\Models\Purchase;
use App\Models\QamlaJob;
use App\Models\Usage;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private $service;

    public function __construct(BaseService $service)
    {
        $this->service = $service;
    }

    public function getUserType($userType)
    {
        $userTypes = [
            'employer' => 'App\\Models\\Employer',
            'candidate' => 'App\\Models\\Candidate',
            'admin' => 'App\\Models\\Admin',
        ];

        return $userTypes[$userType];
    }

    // get all orders of user where total_amount > 0
    public function orders()
    {
        $userId = auth()->user()->id;
        $userType = get_class(auth()->user());

        return Order::with(['product.productType', 'purchase:id,order_id,status'])
            ->where([
                'user_id' => $userId,
                'user_type' => $userType
            ])
            ->where('total_amount', '>', 0)
            ->latest()
            ->paginate(perPage(10));
    }

    // public function orders($product_type_id, $role)
    // {
    //     $userId = auth()->user()->id;
    //     $userType = $this->getUserType($role);

    //     $query = Order::with('product.productType')
    //         ->where([
    //             'user_id' => $userId,
    //             'user_type' => $userType,
    //             'payment_status' => 'processing',
    //         ])
    //         ->where('total_amount', '>', 0)->latest()
    //         ->when($product_type_id, fn($q) => $q->whereHas(
    //             'product',
    //             fn($q) => $q->where('product_type_id', $product_type_id)
    //         ))->latest();

    //     return isset($product_type_id) ?  $query->get() : $query->paginate(perPage(10));
    // }

    public function saveFreeProduct($userType, $userId)
    {
        $product = Product::where(['billing_for' => $userType, 'price' => 0])->first();

        $orderId = $this->saveOrderAndPurchase([
            'user_id' => $userId,
            'product_id' => $product->id,
            'user_type' => $userType
        ]);

        if (!$orderId) {
            return false;
        }

        return $this->completePurchase(Order::find($orderId), true);
    }

    public function saveOrderAndPurchase($orderData)
    {
        $product = Product::find($orderData['product_id']);

        $data = [
            'product_id' => $product->id,
            'currency' => strtolower($product->currency),
            'user_type' => $this->getUserType($orderData['user_type']),
            'user_id' => $orderData['user_id'],
            'price' => $product->regular_price ?? 0,
            'discount' => $product->discount ?? 0,
            'total_amount' => $product->price ?? 0
        ];

        $savedOrder = $this->service->saveOrUpdate(new Order(), $data, null, true);

        if (!$savedOrder) {
            return false;
        }

        return $savedOrder->id;
    }

    public function updateOrderAndPurchase($orderData, $orderId)
    {
        $order = $this->service->saveOrUpdate(new Order(), $orderData, $orderId, true);

        if (!$order) {
            return false;
        }

        $isPurchased = $this->completePurchase($order, false);

        if (!$isPurchased) {
            return false;
        }

        return $order;
    }

    public function show($orderId)
    {
        return Order::with('product.productType')->find($orderId);
    }

    private function completePurchase($order, $isFree = false)
    {
        if (!$isFree && $order->payment_status !== 'succeeded') {
            return false;
        }

        $purchaseData = $this->preparePurchaseData($order);

        return $this->service->saveOrUpdate(new Purchase(), $purchaseData);
    }

    public function checkActiveFeature($feature)
    {
        return (bool) $this->activeFeature($feature);
    }

    public function getActiveFeature($feature)
    {
        return $this->activeFeature($feature);
    }

    private function activeFeature($feature)
    {
        $user = auth()->user();
        $userType = get_class($user);

        return Purchase::where('status', 'active')
            ->whereHas(
                'order',
                fn($query) =>
                $query->where('user_id', $user->id)
                    ->where('user_type', $userType)
            )
            ->whereHas(
                'order.product.features',
                fn($query) =>
                $query->where('type', $feature)
            )
            ->where(function ($query) use ($feature) {
                $query->whereDoesntHave('usage')
                    ->orWhereHas('usage.productFeature', function ($subQuery) use ($feature) {
                        $subQuery->where('type', $feature)
                            ->whereColumn('usage_count', '<', 'product_features.limit');
                    });
            })
            ->latest()
            ->first();
    }

    public function countUsage($activePurchase, $feature)
    {
        $productFeature = $activePurchase?->order?->product?->features?->firstWhere('type', $feature);

        if (!$productFeature) {
            return false;
        }

        $usage = Usage::firstOrCreate(
            ['purchase_id' => $activePurchase->id, 'product_feature_id' => $productFeature->id]
        );

        if (!$usage->wasRecentlyCreated) {
            $usage->increment('usage_count');
        }

        return true;
    }

    private function preparePurchaseData($order)
    {
        $purchaseData['product_id'] = $order->product_id;
        $purchaseData['order_id'] = $order->id;
        $purchaseData['start_date'] = $order->created_at;

        $endDate = $this->calculateEndDate($purchaseData['start_date'], $order->product);
        $purchaseData['end_date'] = $endDate;
        $purchaseData['status'] = $this->purchaseStatus($endDate);

        return $purchaseData;
    }

    private function purchaseStatus($endDate)
    {
        return $endDate->isPast() ? 'expired' : 'active';
    }

    public function managePurchases()
    {
        try {
            $user = auth()->user();
            $userType = get_class($user);
            $paidPurchase = $this->getActivePaidPurchase($user, $userType);

            if ($paidPurchase) {
                if ($paidPurchase->end_date && $paidPurchase->end_date < now()) {
                    $this->expirePurchase($paidPurchase);
                }
            } else {
                $freePurchase = $this->getActiveFreePurchase($user, $userType);

                if ($freePurchase && $freePurchase->status !== 'expired') {
                    $this->resetPurchase($freePurchase);
                }
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get the active purchase with price > 0
     */
    public function getActivePaidPurchase($user, $userType)
    {
        return Purchase::where('status', 'active')
            ->whereHas(
                'order',
                fn($query) =>
                $query->where('user_id', $user->id)
                    ->where('user_type', $userType)
                    ->where('total_amount', '>', 0)
            )
            ->with(['product'])
            ->first();
    }

    /**
     * Get the free purchase (price = 0)
     */
    public function getActiveFreePurchase($user, $userType)
    {
        return Purchase::whereHas('product', fn($query) => $query->where('price', 0))
            ->whereHas('order', fn($query) => $query->where('user_id', $user->id)->where('user_type', $userType))
            // ->where(function ($q) {
            //     $q->whereNull('end_date') // No expiry date → valid
            //         ->orWhere('end_date', '>', now()); // Not expired → valid
            // })
            // ->with(['product', 'usage'])
            ->first();
    }

    /**
     * Update the free purchase with the new start & end date
     */
    private function updateFreePurchase($paidPurchase, $freePurchase)
    {
        $newStartDate = now()->greaterThan($paidPurchase->end_date) ? now() : $paidPurchase->end_date->copy()->addDay();
        $newEndDate = $this->calculateEndDate($newStartDate, $freePurchase->product);

        $freePurchase->update([
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
            'status' => 'active',
        ]);

        // Reset usage_count for all associated features
        $this->resetUsageCount($freePurchase);
    }

    /**
     * Expire the paid purchase
     */
    private function expirePurchase($purchase)
    {
        $purchase->update(['status' => 'expired']);
        if ($purchase->product_id == 10) {
            $jobId = PromotedJobOrder::where('order_id', $purchase->order_id)->value('job_id');
            QamlaJob::where('id', $jobId)->update(['is_promoted' => 0]);
        }
    }

    /**
     * Reset a purchase by updating start & end date and resetting usage count
     */
    private function resetPurchase($purchase)
    {
        $newStartDate = now();
        $newEndDate = $this->calculateEndDate($newStartDate, $purchase->product);

        $purchase->update([
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
            'status' => 'active',
        ]);

        // Reset usage_count for all associated features
        $this->resetUsageCount($purchase);
    }

    /**
     * Calculate the new end date based on the product's billing period
     */

    private function calculateEndDate($startDate, $product)
    {
        return match ($product->billing_period) {
            'day' => $startDate->copy()->addDays($product->billing_interval),
            'month' => $startDate->copy()->addMonths($product->billing_interval),
            'year' => $startDate->copy()->addYears($product->billing_interval),
            default => null,
        };
    }

    /**
     * Reset the usage count for all features of a purchase
     */
    private function resetUsageCount($purchase)
    {
        $purchase->usage()->update(['usage_count' => 0]);
    }
}
