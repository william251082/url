<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function showUrls(): JsonResponse
    {
        return response()->json(Url::all());
    }

    public function createUrl(Request $request)
    {
        $url = Url::create($request->all());

        return response()->json($url, 201);
    }
}
