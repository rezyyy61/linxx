<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UserIndexRequest;
use App\Http\Requests\Admin\Users\UserUpdateRequest;
use App\Http\Resources\Admin\Users\UserAdminResource;
use App\Models\User;
use App\Services\Admin\Users\UserAdminService;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(private UserAdminService $svc) {}

    public function index(UserIndexRequest $request)
    {
        $perPage = (int)($request->integer('per_page') ?: 20);
        $list = $this->svc->index($request->validated(), $perPage);
        return UserAdminResource::collection($list);
    }

    public function show(int $id)
    {
        $user = User::with('politicalProfile')->findOrFail($id);
        return new UserAdminResource($this->svc->show($user));
    }

    public function update(UserUpdateRequest $request, int $id)
    {
        $user = User::with('politicalProfile')->findOrFail($id);
        $data = $request->validated();
        $updated = $this->svc->update($user, Arr::except($data, ['profile']), $data['profile'] ?? []);
        return new UserAdminResource($updated);
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $this->svc->destroy($user);
        return response()->noContent();
    }
}
