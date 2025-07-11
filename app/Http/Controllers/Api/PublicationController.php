<?php

namespace App\Http\Controllers\Api;

use App\Actions\StorePublication;
use App\Actions\SummarizeFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Resources\PublicationResource;
use App\Models\PoliticalProfile;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index()
    {
        $profile = PoliticalProfile::firstWhere('user_id', auth()->id());
        return PublicationResource::collection($profile->publications()->latest()->get());
    }

    public function byProfile(int $id)
    {
        $profile = PoliticalProfile::findOrFail($id);
        return PublicationResource::collection($profile->publications()->latest()->get());
    }

    public function store(StorePublicationRequest $req, StorePublication $action)
    {
        $profile = PoliticalProfile::firstWhere('user_id', auth()->id());
        $pub     = $action->handle($profile, $req->file('file'), $req->validated());

        return PublicationResource::make($pub)
            ->additional(['suggested_description' => $pub->description])
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Publication $publication)
    {
        $profile = PoliticalProfile::firstWhere('user_id', auth()->id());
        abort_unless($profile && $publication->political_profile_id === $profile->id, 403);

        Storage::disk('public')->delete($publication->file_path);
        $publication->delete();
        return response()->noContent();
    }

    public function suggestDescription(Request $request, SummarizeFile $summarizer)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        try {
            $description = $summarizer->handle($request->file('file'));

            return response()->json([
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Suggestion Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'خطا در دریافت توضیح از هوش مصنوعی',
            ], 500);
        }
    }
}
