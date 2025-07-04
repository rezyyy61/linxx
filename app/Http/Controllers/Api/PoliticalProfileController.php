<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePoliticalProfileRequest;
use App\Http\Requests\UpdatePoliticalProfileRequest;
use App\Http\Resources\PoliticalProfileResource;
use App\Models\PoliticalProfile;
use App\Services\PoliticalProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliticalProfileController extends Controller
{
    protected $service;

    public function __construct(PoliticalProfileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $profiles = PoliticalProfile::with(['links', 'ideologies', 'files'])->latest()->get();
        return PoliticalProfileResource::collection($profiles);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePoliticalProfileRequest $request): PoliticalProfileResource
    {
        $user = auth()->user();

        $existingProfile = $user->politicalProfile;

        if ($existingProfile) {
            $profile = $this->service->updateWithRelations(
                $request->validated(),
                $existingProfile
            );
        } else {
            $profile = $this->service->createWithRelations(
                $request->validated(),
                $user->id
            );
        }

        return new PoliticalProfileResource($profile);
    }


    /**
     * Display the specified resource.
     */
    public function show(PoliticalProfile $politicalProfile): PoliticalProfileResource
    {
        return new PoliticalProfileResource($politicalProfile->load(['links', 'ideologies', 'files']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePoliticalProfileRequest $request, PoliticalProfile $politicalProfile): PoliticalProfileResource
    {
        $profile = $this->service->updateWithRelations(
            $request->validated(),
            $politicalProfile
        );

        return new PoliticalProfileResource($profile);
    }

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PoliticalProfile $politicalProfile)
    {
        $politicalProfile->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

}
