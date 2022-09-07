<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Providers\UrlShortenerServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function createUrl(Request $request)
    {
        $this->validate($request, [
            'long_name' => 'required'
        ]);

        $postedValue = $request->all();
        $longName = $postedValue['long_name'];
        $url = Url::create($request->all());
        $url->short_name = $this->convertToShortName($longName, $url->id);
        $url->save();

        return response()->json($url, 201);
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
