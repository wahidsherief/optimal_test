<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookShelf;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bookshelves/{bookshelfId}/books",
     *     summary="Get all books in a bookshelf",
     *     tags={"Book"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookshelfId",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
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
    public function index($bookshelfId)
    {
        $bookshelf = BookShelf::find($bookshelfId);

        if (!$bookshelf) {
            return response()->json(['message' => 'Bookshelf not found.'], 404);
        }

        return response()->json($bookshelf->books);
    }

    /**
     * @OA\Post(
     *     path="/api/bookshelves/{bookshelfId}/books",
     *     summary="Create a new book in a bookshelf",
     *     tags={"Book"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookshelfId",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author", "published_year"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="published_year", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf not found"
     *     )
     * )
     */
    public function store(Request $request, $bookshelfId)
    {
        $bookshelf = BookShelf::find($bookshelfId);

        if (!$bookshelf) {
            return response()->json(['message' => 'Bookshelf not found.'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'required|integer',
        ]);

        $book = $bookshelf->books()->create($validated);

        return response()->json([
            'message' => 'Book created successfully.',
            'data' => $book
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/bookshelves/{bookshelfId}/books/{bookId}",
     *     summary="Get a specific book in a bookshelf",
     *     tags={"Book"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookshelfId",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf or Book not found"
     *     )
     * )
     */
    public function show($bookshelfId, $bookId)
    {
        $bookshelf = BookShelf::find($bookshelfId);
        $book = Book::find($bookId);

        if (!$bookshelf || !$book || $book->book_shelf_id !== $bookshelf->id) {
            return response()->json(['message' => 'Bookshelf or Book not found.'], 404);
        }

        return response()->json($book);
    }

    /**
     * @OA\Put(
     *     path="/api/bookshelves/{bookshelfId}/books/{bookId}",
     *     summary="Update a specific book in a bookshelf",
     *     tags={"Book"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookshelfId",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author", "published_year"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="published_year", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf or Book not found"
     *     )
     * )
     */
    public function update(Request $request, $bookshelfId, $bookId)
    {
        $bookshelf = BookShelf::find($bookshelfId);
        $book = Book::find($bookId);

        if (!$bookshelf || !$book || $book->book_shelf_id !== $bookshelf->id) {
            return response()->json(['message' => 'Bookshelf or Book not found.'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'published_year' => 'sometimes|required|integer',
        ]);

        $book->update($validated);

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => $book
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/bookshelves/{bookshelfId}/books/{bookId}",
     *     summary="Delete a specific book in a bookshelf",
     *     tags={"Book"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookshelfId",
     *         in="path",
     *         required=true,
     *         description="ID of the bookshelf",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookshelf or Book not found"
     *     )
     * )
     */
    public function destroy($bookshelfId, $bookId)
    {
        $bookshelf = BookShelf::find($bookshelfId);
        $book = Book::find($bookId);

        if (!$bookshelf || !$book || $book->book_shelf_id !== $bookshelf->id) {
            return response()->json(['message' => 'Bookshelf or Book not found.'], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully.'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/books/search",
     *     summary="Search for books by title or author",
     *     tags={"Book"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         description="Title of the book",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         required=false,
     *         description="Author of the book",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     * )
     */
    public function search(Request $request)
    {
        $query = Book::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }

        $books = $query->get();

        if ($books->isEmpty()) {
            return response()->json(['message' => 'No books found matching your search criteria.'], 404);
        }

        return response()->json($books);
    }
}
