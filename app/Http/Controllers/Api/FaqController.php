<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FaqController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 100));

        $locale = $request->header('Accept-Language', app()->getLocale());
        $faqs = Faq::active()->paginate($limit);
        $faqs->getCollection()->each(function (Faq $faq) use ($locale) {
            $faq->setAttribute('requested_locale', $locale);
        });

        return FaqResource::collection($faqs)->additional([
            'meta' => ['locale' => $locale],
        ]);
    }

    public function show(Faq $faq): FaqResource
    {
        abort_unless($faq->is_active, 404);

        return (new FaqResource($faq))
            ->additional(['meta' => ['locale' => request()->header('Accept-Language', app()->getLocale())]]);
    }
}

