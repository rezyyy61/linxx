<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Services\VideoPostService;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    protected $videoPostService;

    public function __construct(VideoPostService $videoPostService)
    {
        $this->videoPostService = $videoPostService;
    }

    public function index(Request $request)
    {
        $videos = $this->videoPostService->getPaginatedVideos($request->get('per_page', 12));
        return PostResource::collection($videos);
    }

    public function showVideoById($id)
    {
        $video = $this->videoPostService->getVideoById($id);
        return new PostResource($video);
    }

}
