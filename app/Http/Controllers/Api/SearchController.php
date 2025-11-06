<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\DoctorResource;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService )
    {
        $this->searchService = $searchService;
    }

    public function storeSearch(SearchRequest $request)
    {

         $search = $request->input('search_query');
         
        $doctors = $this->searchService->searchDoctors(
            $request->input('latitude'),
            $request->input('longitude'),
            $search,
            $request->input('radius', 10),
            $request->user()
        );

         if($search  && $doctors->isNotEmpty()){
            $this->searchService->storeSearchHistory($request->user(), $request->all());
        }

        return response()->json([
            'data' => DoctorResource::collection($doctors)
        ]);
    }


}
