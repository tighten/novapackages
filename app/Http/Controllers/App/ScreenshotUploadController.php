<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScreenshotUploadController extends Controller
{
    public function store(): JsonResponse
    {
        request()->validate([
            'screenshot' => ['image', 'max:2048'],
        ]);

        $screenshot = Screenshot::create([
            'uploader_id' => auth()->id(),
            'path' => request('screenshot')->store('screenshots'),
        ]);

        return response()->json($screenshot, 201);
    }

    public function destroy(Screenshot $screenshot): JsonResponse
    {
        $screenshot->delete();

        return response()->json([], 200);
    }
}
