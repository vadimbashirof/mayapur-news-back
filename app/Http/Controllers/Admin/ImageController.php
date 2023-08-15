<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Admin\Image\DestroyRequest;
use App\Http\Requests\Admin\Image\ShowRequest;
use App\Http\Requests\Admin\Image\StoreMultipleRequest;
use App\Http\Requests\Admin\Image\StoreOneRequest;
use App\Http\Requests\Admin\Image\UpdateRequest;
use App\Models\ImageModel;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    /**
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function show(ShowRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $images = ImageService::getAll($validated['group_name'], $validated['group_id']);
        return ResponseHelper::success($images);
    }

    /**
     * @param StoreOneRequest $request
     * @return JsonResponse
     */
    public function storeOne(StoreOneRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $image = ImageService::saveOne($request->file('image'), $validated['group_name'], $validated['group_id']);

        return ResponseHelper::success($image);
    }

    /**
     * @param StoreMultipleRequest $request
     * @return JsonResponse
     */
    public function storeMultiple(StoreMultipleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $images = ImageService::saveMultiple($request->file('images'), $validated['group_name'], $validated['group_id']);

        return ResponseHelper::success($images);
    }

    /**
     * @param UpdateRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        ImageService::update($validated['data']);
        $images = ImageService::getAll($validated['group_name'], $validated['group_id']);

        return ResponseHelper::success($images);
    }

    /**
     * @param DestroyRequest $request
     * @return JsonResponse
     */
    public function destroyOne(DestroyRequest $request): JsonResponse
    {
        ImageModel::where($request->validated())->delete();
        return ResponseHelper::ok();
    }
}
