<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/chapters/{chapterId}/pages",
     *     summary="Get all pages of a chapter",
     *     tags={"Page"},
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
     *     @OA\Response(
     *         response=404,
     *         description="Chapter not found"
     *     )
     * )
     */
    public function index($chapterId)
    {
        $chapter = Chapter::find($chapterId);

        if (!$chapter) {
            return response()->json(['message' => 'Chapter not found.'], 404);
        }

        return response()->json($chapter->pages);
    }

    /**
     * @OA\Post(
     *     path="/api/chapters/{chapterId}/pages",
     *     summary="Create a new page in a chapter",
     *     tags={"Page"},
     *     security={{"bearer":{}}},
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
     *             required={"page_number","content"},
     *             @OA\Property(property="page_number", type="integer"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Page created successfully"
     *     ),
     * )
     */
    public function store(Request $request, $chapterId)
    {
        $chapter = Chapter::find($chapterId);

        if (!$chapter) {
            return response()->json(['message' => 'Chapter not found.'], 404);
        }

        $validated = $request->validate([
            'page_number' => 'required|integer',
            'content' => 'required|string'
        ]);

        $page = $chapter->pages()->create($validated);

        return response()->json([
            'message' => 'Page created successfully.',
            'data' => $page
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/chapters/{chapterId}/pages/{pageId}",
     *     summary="Get a specific page of a chapter",
     *     tags={"Page"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pageId",
     *         in="path",
     *         required=true,
     *         description="ID of the page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     * )
     */
    public function show($chapterId, $pageId)
    {
        $chapter = Chapter::find($chapterId);
        $page = Page::find($pageId);

        if (!$chapter || !$page || $page->chapter_id !== $chapter->id) {
            return response()->json(['message' => 'Chapter or Page not found.'], 404);
        }

        return response()->json($page);
    }

    /**
     * @OA\Put(
     *     path="/api/chapters/{chapterId}/pages/{pageId}",
     *     summary="Update a specific page of a chapter",
     *     tags={"Page"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pageId",
     *         in="path",
     *         required=true,
     *         description="ID of the page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"page_number","content"},
     *             @OA\Property(property="page_number", type="integer"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page updated successfully"
     *     ),
     * )
     */
    public function update(Request $request, $chapterId, $pageId)
    {
        $chapter = Chapter::find($chapterId);
        $page = Page::find($pageId);

        if (!$chapter || !$page || $page->chapter_id !== $chapter->id) {
            return response()->json(['message' => 'Chapter or Page not found.'], 404);
        }

        $validated = $request->validate([
            'page_number' => 'sometimes|required|integer',
            'content' => 'sometimes|required|string'
        ]);

        $page->update($validated);

        return response()->json([
            'message' => 'Page updated successfully.',
            'data' => $page
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/chapters/{chapterId}/pages/{pageId}",
     *     summary="Delete a specific page of a chapter",
     *     tags={"Page"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID of the chapter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pageId",
     *         in="path",
     *         required=true,
     *         description="ID of the page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page deleted successfully"
     *     ),
     * )
     */
    public function destroy($chapterId, $pageId)
    {
        $chapter = Chapter::find($chapterId);
        $page = Page::find($pageId);

        if (!$chapter || !$page || $page->chapter_id !== $chapter->id) {
            return response()->json(['message' => 'Chapter or Page not found.'], 404);
        }

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully.'
        ], 200);
    }
}
