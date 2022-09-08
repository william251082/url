<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Providers\UrlShortenerServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{
    protected UrlShortenerServiceProvider $urlShortenerService;

    public function __construct(UrlShortenerServiceProvider $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }

    public function showUrls(): JsonResponse
    {
        return response()->json(Url::all());
    }

    public function createUrl(Request $request): JsonResponse
    {
        $currentUser = Auth::user();
        $postedValue = $request->all();
        $longName = $postedValue['long_name'];
        $url = Url::create($request->all());
        $url->short_name = $this->convertToShortName($longName, $url->id);
        if ($currentUser) {
            $url->id = $currentUser->getAuthIdentifier();
        }

        $url->save();

        return response()->json($url, 201);
    }

    public function deleteUrl($id): JsonResponse
    {
        Url::findOrFail($id)->delete();
        return response()->json(['Deleted Successfully'], 200);
    }

    private function convertToShortName(string $longName, int $idToEncode): string
    {
        $parsedUrl = parse_url($longName);
        $schemeAndHostFromInput = $parsedUrl['scheme'].'://'.$parsedUrl['host'];
        $this->urlShortenerService->setChars($longName);
        $this->urlShortenerService->setSalt($longName);

        return $this->urlShortenerService->shortenUrl($schemeAndHostFromInput, $longName, $idToEncode);
    }
}
