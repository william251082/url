<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Providers\UrlShortenerServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;

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

    public function redirectUrl($id): JsonResponse
    {
        $url = Url::findOrFail($id);
        header("location:".$url->long_name);
        exit;
    }

    public function createUrl(Request $request): JsonResponse
    {
        $url = Url::create($request->all());
        $redirectUrl = $request->getSchemeAndHttpHost().':80/url/'.$url->id;
        $currentUser = Auth::user();
        $postedValue = $request->all();
        $url->short_name = $redirectUrl;
        if ($currentUser) {
            $url->id = $currentUser->getAuthIdentifier();
        }

        $url->ip = $request->ip();

        $url->save();

        return response()->json($url, 201);
    }

    public function deleteUrl($id): JsonResponse
    {
        Url::findOrFail($id)->delete();
        return response()->json(['Deleted Successfully'], 200);
    }

    #[NoReturn]
    public function addVisit($id): void
    {
        $url = Url::findOrFail($id);
        $url->visit_count ++;
        $url->save();
    }

    public function exploitableUrls(): JsonResponse
    {
        $exploitableUrls = DB::table('url')
            ->orderBy('visit_count', 'desc')
            ->limit(5)
            ->get();
        return response()->json($exploitableUrls);
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
