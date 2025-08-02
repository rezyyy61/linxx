<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserIndexRequest;
use App\Http\Resources\Admin\User\UserResource;
use App\Services\Admin\User\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(UserIndexRequest $request): JsonResponse
    {
        $users = $this->userService->getAll($request->validated());
        return response()->json(UserResource::collection($users));
    }
}
