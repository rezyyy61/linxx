<?php

namespace App\Services\Admin\Users;

use App\Mail\EntityTypeApprovalDecision;
use App\Models\PoliticalProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserTypeRequestService
{
    public function index(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = PoliticalProfile::query()
            ->with('user')
            ->whereNotNull('pending_entity_type')
            ->where('entity_type_approved', false);

        if (!empty($filters['q'])) {
            $s = trim($filters['q']);
            $q->whereHas('user', function ($w) use ($s) {
                $w->where('name','like',"%{$s}%")
                    ->orWhere('email','like',"%{$s}%")
                    ->orWhere('slug','like',"%{$s}%");
            });
        }

        if (!empty($filters['requested'])) {
            $q->where('pending_entity_type', $filters['requested']);
        }

        return $q->latest('id')->paginate($perPage);
    }

    public function approveByUserId(int $userId): PoliticalProfile
    {
        $profile = PoliticalProfile::where('user_id', $userId)->with('user')->first();
        if (!$profile || !$profile->pending_entity_type) {
            throw new HttpResponseException(response()->json(['message' => 'No pending request'], 422));
        }

        $profile->entity_type = $profile->pending_entity_type;
        $profile->pending_entity_type = null;
        $profile->entity_type_approved = true;
        $profile->save();

        DB::afterCommit(function () use ($profile) {
            if ($profile->user?->email) {
                Mail::to($profile->user->email)
                    ->queue(new EntityTypeApprovalDecision($profile->fresh('user'), 'approved'));
            }
        });

        return $profile->load('user');
    }

    public function rejectByUserId(int $userId): PoliticalProfile
    {
        $profile = PoliticalProfile::where('user_id', $userId)->with('user')->first();
        if (!$profile || !$profile->pending_entity_type) {
            throw new HttpResponseException(response()->json(['message' => 'No pending request'], 422));
        }

        $profile->pending_entity_type = null;
        $profile->entity_type_approved = false;
        $profile->save();

        DB::afterCommit(function () use ($profile) {
            if ($profile->user?->email) {
                Mail::to($profile->user->email)
                    ->queue(new EntityTypeApprovalDecision($profile->fresh('user'), 'rejected'));
            }
        });

        return $profile->load('user');
    }
}
