<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\SearchResource;
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

        public function index(Request $request)
    {

        $user = $request->user();

        $history = $this->searchService->SearchHistory($user);

         return response()->json([
            'data' =>  SearchResource::collection($history)
        ]) ;

    } // end getSearchHistory



    public function store(SearchRequest $request)
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
            'data' => DoctorResource::collection($doctors)

        ]) ;

    } // end storeSearch



    public function destroy($id){

   try{
        $deleted = $this->searchService->distorySearch($id);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'السجل غير موجود',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف السجل بنجاح',
        ]);

   }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء حذف السجل',
        ], 500);

    }

} // End destroy

       public function clear(Request $request)
    {
        $user = $request->user();

       $this->searchService->clearSearchHistory($user);

        return response()->json([
            'message' => 'Search history cleared successfully.'
        ]);
    } // end clearSearchHistory


}
