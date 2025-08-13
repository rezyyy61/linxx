<?php

namespace App\Http\Controllers\Api\Share;

use App\Http\Controllers\Controller;
use App\Models\Share\Share;
use App\Services\Share\Contracts\ShareRevoker;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class RevokeShareController extends Controller
{
    public function __invoke(int $id, ShareRevoker $revoker): JsonResponse
    {
        $share = Share::query()->findOrFail($id);
        if ((int)$share->user_id !== (int)auth()->id()) {
            throw new AuthorizationException();
        }
        $revoker->revoke($share);

        return response()->json(['status' => 'revoked']);
    }
}
