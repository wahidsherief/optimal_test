<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Response::macro('api', function ($data = null) {
        //     $statusCode = isset($data['status_code']) ? $data['status_code'] : 200;
        //     $message = isset($data['message']) ? $data['message'] : 'result found';
        //     $type = ($data['type'] ?? null) === 'bool' ?? true;
        //     if (isset($data['status_code'])) {
        //         unset($data['status_code']);
        //     }
        //     if (isset($data['message'])) {
        //         unset($data['message']);
        //     }
        //     if (isset($data['type'])) {
        //         unset($data['type']);
        //     }
        //     if (isset($data['errors'])) {
        //         $message = $data['errors'];
        //         unset($data['errors']);
        //     }

        //     if ($statusCode === 200) {
        //         return $this->handleSuccess($data, $message, $type);
        //     } elseif ($statusCode === 422) {
        //         return $this->handleValidationError($data, $message);
        //     } else {
        //         return $this->handleError($statusCode, $message);
        //     }
        // });

        // // Success handler inside the macro closure
        // Response::macro('handleSuccess', function ($data, $message, $type = null) {
        //     if ($type === true || empty($data)) {
        //         return response()->json([
        //             'status' => 'success',
        //             'message' => $message
        //         ], 200);
        //     }

        //     if ($data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator || $data instanceof \Illuminate\Contracts\Pagination\Paginator) {
        //         return response()->json([
        //             'status' => 'success',
        //             'message' => $message,
        //             'meta' => [
        //                 'total' => $data->total(),
        //                 'count' => $data->count(),
        //                 'per_page' => $data->perPage(),
        //                 'current_page' => $data->currentPage(),
        //                 'total_pages' => $data->lastPage(),
        //             ],
        //             'data' => $data->items(),
        //         ], 200);
        //     }

        //     if (is_array($data) || $data instanceof \Illuminate\Support\Collection || is_string($data)) {
        //         return response()->json([
        //             'status' => 'success',
        //             'message' => $message,
        //             'data' => $data,
        //         ], 200);
        //     }

        //     if ($data instanceof \Illuminate\Database\Eloquent\Model) {
        //         return response()->json([
        //             'status' => 'success',
        //             'message' => $message,
        //             'data' => $data,
        //         ], 200);
        //     }
        // });

        // // Validation error handler inside the macro closure
        // Response::macro('handleValidationError', function ($data, $message = 'Validation failed.') {
        //     if ($data instanceof \Illuminate\Support\MessageBag) {
        //         return response()->json([
        //             'status' => 'fail',
        //             'message' => $message,
        //             'errors' => $data->toArray(),
        //         ], 422);
        //     }

        //     return response()->json([
        //         'status' => 'fail',
        //         'message' => 'An unexpected error occurred. Please try again later.',
        //     ], 500);
        // });

        // // Error handler inside the macro closure
        // Response::macro('handleError', function ($statusCode, $message) {
        //     return response()->json([
        //         'status' => 'fail',
        //         'message' => $message,
        //     ], $statusCode);
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
