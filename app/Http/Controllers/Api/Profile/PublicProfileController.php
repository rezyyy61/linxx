<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoliticalProfileResource;
use App\Http\Resources\PostResource;
use App\Services\PoliticalProfile\PublicProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    protected PublicProfileService $profileService;

    public function __construct(PublicProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function showProfile(string $slug): JsonResponse
    {
        $profile = $this->profileService->getProfileBySlug($slug);

        if (!$profile) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'data' => new PoliticalProfileResource($profile)
        ]);
    }

    public function getPosts(string $slug, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $posts = $this->profileService->getPaginatedPostsBySlug($slug, $perPage);

        return PostResource::collection($posts)->response();
    }

    public function getImagePosts(string $slug, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $posts = $this->profileService->getImagePostsBySlug($slug, $perPage);

        return PostResource::collection($posts)->response();
    }

    public function getVideoPosts(string $slug, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $posts = $this->profileService->getVideoPostsBySlug($slug, $perPage);

        return PostResource::collection($posts)->response();
    }

    public function getFilePosts(string $slug, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $posts = $this->profileService->getFilePostsBySlug($slug, $perPage);

        return PostResource::collection($posts)->response();
    }

}
