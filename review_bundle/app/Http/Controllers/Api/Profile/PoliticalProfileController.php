<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePoliticalProfileRequest;
use App\Http\Resources\PoliticalProfileResource;
use App\Models\PoliticalProfile;
use App\Services\PoliticalProfile\PoliticalProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PoliticalProfileController extends Controller
{
    public function me(PoliticalProfileService $service): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $profile = $service->getForUser($userId);

        if (!$profile) {
            return response()->json(null, 204);
        }

        return (new PoliticalProfileResource($profile))
            ->response()
            ->setStatusCode(200);
    }

    public function storeOrUpdate(StorePoliticalProfileRequest $request, PoliticalProfileService $service): PoliticalProfileResource
    {
        $profile = $service->storeOrUpdate($request->validated(), Auth::id());

        return new PoliticalProfileResource($profile);
    }

    public function destroy(PoliticalProfile $profile): JsonResponse
    {
        $profile->delete();
        return response()->json(['message' => 'Profile deleted successfully']);
    }
}
