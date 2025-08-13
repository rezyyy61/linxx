<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Users\UserTypeRequestResource;
use App\Services\Admin\Users\UserTypeRequestService;
use Illuminate\Http\Request;

class UserTypeRequestController extends Controller
{
    public function __construct(private UserTypeRequestService $svc) {}

    public function index(Request $request)
    {
        $perPage = (int)($request->integer('per_page') ?: 20);
        $filters = [
            'q' => $request->string('q')->toString(),
            'requested' => $request->string('requested')->toString(),
        ];
        $list = $this->svc->index($filters, $perPage);
        return UserTypeRequestResource::collection($list);
    }

    public function approve(int $id)
    {
        $profile = $this->svc->approveByUserId($id);
        return new UserTypeRequestResource($profile);
    }

    public function reject(Request $request, int $id)
    {
        $data = $request->validate([
            'reason' => ['sometimes','nullable','string','min:5','max:500'],
        ]);
        $profile = $this->svc->rejectByUserId($id, $data['reason'] ?? null);
        return new UserTypeRequestResource($profile);
    }
}
