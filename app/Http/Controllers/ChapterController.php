<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books/{bookId}/chapters",
     *     summary="Get all chapters of a book",
     *     tags={"Chapter"},
     *     security={{"bearer":{}}},
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
     *         description="Book not found"
     *     )
     * )
     */
    public function index($bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);
        }

        return response()->json($book->chapters);
    }

    /**
     * @OA\Post(
     *     path="/api/books/{bookId}/chapters",
     *     summary="Create a new chapter in a book",
     *     tags={"Chapter"},
     *     security={{"bearer":{}}},
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
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="chapter_number", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Chapter created successfully"
     *     ),
     * )
     */
    public function store(Request $request, $bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'chapter_number' => 'required|integer',
        ]);

        $chapter = $book->chapters()->create($validated);

        return response()->json([
            'message' => 'Chapter created successfully.',
            'data' => $chapter
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/books/{bookId}/chapters/{chapterId}",
     *     summary="Get a specific chapter of a book",
     *     tags={"Chapter"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     * )
     */
    public function show($bookId, $chapterId)
    {
        $book = Book::find($bookId);
        $chapter = Chapter::find($chapterId);

        if (!$book || !$chapter || $chapter->book_id !== $book->id) {
            return response()->json([
                'message' => 'Book or Chapter not found or mismatch.'
            ], 404);
        }

        return response()->json($chapter);
    }

    /**
     * @OA\Put(
     *     path="/api/books/{bookId}/chapters/{chapterId}",
     *     summary="Update a specific chapter of a book",
     *     tags={"Chapter"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="chapter_number", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chapter updated successfully"
     *     ),
     * )
     */
    public function update(Request $request, $bookId, $chapterId)
    {
        $book = Book::find($bookId);
        $chapter = Chapter::find($chapterId);

        if (!$book || !$chapter || $chapter->book_id !== $book->id) {
            return response()->json([
                'message' => 'Book or Chapter not found or mismatch.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'chapter_number' => 'sometimes|required|integer',
        ]);

        $chapter->update($validated);

        return response()->json([
            'message' => 'Chapter updated successfully.',
            'data' => $chapter
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{bookId}/chapters/{chapterId}",
     *     summary="Delete a specific chapter of a book",
     *     tags={"Chapter"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID of the book",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chapter deleted successfully"
     *     ),
     * )
     */
    public function destroy($bookId, $chapterId)
    {
        $book = Book::find($bookId);
        $chapter = Chapter::find($chapterId);

        if (!$book || !$chapter || $chapter->book_id !== $book->id) {
            return response()->json([
                'message' => 'Book or Chapter not found or mismatch.',
            ], 404);
        }

        $chapter->delete();

        return response()->json([
            'message' => 'Chapter deleted successfully.'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/chapters/{chapterId}/full-content",
     *     summary="Get full content of a chapter",
     *     tags={"Chapter"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     * )
     */
    public function fullContent($chapterId)
    {
        $chapter = Chapter::find($chapterId);

        if (!$chapter) {
            return response()->json([
                'message' => 'Chapter not found.',
            ], 404);
        }

        $content = $chapter->pages()->orderBy('page_number')->pluck('content')->implode("\n\n");

        return response()->json([
            'chapter_title' => $chapter->title,
            'content' => $content
        ]);
    }
}
