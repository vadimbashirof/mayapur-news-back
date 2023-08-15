<?php

namespace App\Http\Controllers\Admin;

use App\Events\CreatePostEvent;
use App\Events\DeletePostEvent;
use App\Events\UpdatePostEvent;
use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Http\Requests\Admin\Post\DestroyRequest;
use App\Http\Requests\Admin\Post\IndexRequest;
use App\Http\Requests\Admin\Post\ShowRequest;
use App\Http\Requests\Admin\Post\StoreRequest;
use App\Http\Requests\Admin\Post\UpdateRequest;
use App\Models\PostImagesModel;
use App\Models\PostModel;
use Illuminate\Http\JsonResponse;
use App\Services\ImageService;

class PostController extends Controller
{

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $posts = PostModel::offset($validated['offset'])
            ->limit($validated['limit'])
            ->orderBy('created_at', 'desc')
            ->get();
        $count = PostModel::get()->count();
        $posts = $posts->toArray();
        $posts = ImageService::merge(PostModel::IMAGES_GROUP, $posts);

        return ResponseHelper::success([
            'posts' => $posts,
            'count' => $count,
        ]);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(ShowRequest $request, int $id): JsonResponse
    {
        $post = PostModel::where('id', $id)->first();
        $post = $post->toArray();
        $post['images'] = ImageService::getAll(PostModel::IMAGES_GROUP, $post['id']);

        return ResponseHelper::success($post);
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $post = new PostModel();
        $post->fill($request->validated());
        $post->saveOrFail();

        $images = [];
        if($request->hasFile('images')) {
            $images = ImageService::saveMultiple($request->file('images'), PostModel::IMAGES_GROUP, $post['id']);
        }

        $post = $post->toArray();
        $post['images'] = $images;

        event(new CreatePostEvent($post));

        return ResponseHelper::success($post);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $post = PostModel::find($id);
        $post->fill($request->validated());
        $post->saveOrFail();
        $post = $post->toArray();
        $post['images'] = ImageService::getAll(PostModel::IMAGES_GROUP, $post['id']);

        event(new UpdatePostEvent($post));

        return ResponseHelper::success($post);
    }

    /**
     * @param DestroyRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, int $id): JsonResponse
    {
        PostModel::where('id', $id)->delete();
        event(new DeletePostEvent(['id' => $id]));
        return ResponseHelper::ok();
    }
}
