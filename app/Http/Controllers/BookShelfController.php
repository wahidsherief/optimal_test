<?php

namespace App\Http\Controllers;

use App\Models\BookShelf;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookshelfController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bookshelves",
     *     summary="Get all bookshelves",
     *     tags={"Bookshelf"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No bookshelves found"
     *     )
     * )
     */

    public function index()
    {
        $bookshelves = BookShelf::with('books.chapters.pages')->get();

        if ($bookshelves->isEmpty()) {
            return response()->json([
                'message' => 'No bookshelves found.'
            ], 404);
        }

        return response()->json($bookshelves);
    }

    /**
     * @OA\Post(
     *     path="/api/bookshelves",
     *     summary="Create a new bookshelf",
     *     tags={"Bookshelf"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="location", type="string")
     *         )
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Bookshelf created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $bookshelf = BookShelf::create($validated);

        return response()->json([
            'message' => 'Bookshelf created successfully.',
            'data' => $bookshelf
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/bookshelves/{id}",
     *     summary="Get a specific bookshelf",
     *     tags={"Bookshelf"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf not found"
     *     )
     * )
     */
    public function show($id)
    {
        $bookshelf = BookShelf::with('books.chapters.pages')->find($id);

        if (!$bookshelf) {
            return response()->json([
                'message' => 'Bookshelf not found.'
            ], 404);
        }

        return response()->json($bookshelf);
    }


    /**
     * @OA\Put(
     *     path="/api/bookshelves/{id}",
     *     summary="Update a specific bookshelf",
     *     tags={"Bookshelf"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="location", type="string")
     *         )
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Bookshelf updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
        ]);

        $bookshelf = BookShelf::find($id);

        if (!$bookshelf) {
            return response()->json([
                'message' => 'Bookshelf not found.'
            ], 404);
        }

        $bookshelf->update($validated);

        return response()->json([
            'message' => 'Bookshelf updated successfully.',
            'data' => $bookshelf
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/bookshelves/{id}",
     *     summary="Delete a specific bookshelf",
     *     tags={"Bookshelf"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Bookshelf deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $bookshelf = BookShelf::find($id);

        if (!$bookshelf) {
            return response()->json([
                'message' => 'Bookshelf not found.'
            ], 404);
        }

        $bookshelf->delete();

        return response()->json([
            'message' => 'Bookshelf deleted successfully.'
        ], 200);
    }
}
