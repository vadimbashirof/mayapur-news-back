<?php

namespace App\Http\Controllers\App;

use App\Helpers\ResponseHelper;
use App\Http\Requests\App\Post\IndexRequest;
use App\Models\PostModel;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $posts = PostModel::where('locale', $validated['lang'])
            ->offset($validated['offset'])
            ->limit($validated['limit'])
            ->orderBy('created_at', 'desc')
            ->get();
        $count = PostModel::where('locale', $validated['lang'])->get()->count();
        $posts = $posts->toArray();
        $posts = ImageService::flatMerge(PostModel::IMAGES_GROUP, $posts);

        return ResponseHelper::success([
            'posts' => $posts,
            'count' => $count,
        ]);
    }
}
