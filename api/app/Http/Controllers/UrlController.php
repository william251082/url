<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\JsonResponse;

class UrlController extends Controller
{
    public function showUrls(): JsonResponse
    {
        return response()->json(Url::all());
    }
}
