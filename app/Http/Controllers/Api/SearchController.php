<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
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

        $doctors = $this->searchService->searchDoctorsNearby(
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
            'data' => $doctors
        ]);
    } // end storeSearch


    public function getSearchHistory(Request $request)
    {
        $user = $request->user();

        $history = $user->getSearchHistory()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $history
        ]);
    } // end getSearchHistory

       public function clearSearchHistory(Request $request)
    {
        $user = $request->user();
        
        $user->getSearchHistory()->delete();

        return response()->json([
            'message' => 'Search history cleared successfully.'
        ]);
    } // end clearSearchHistory



}
