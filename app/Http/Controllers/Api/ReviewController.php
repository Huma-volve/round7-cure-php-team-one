<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $reviews = Review::with('booking.doctor','booking.patient')->get();
            return ApiResponse::success( ['reviews'=>$reviews] ,"All reviews fetched successfully",200); 

        }catch(\Throwable $e ){

            Log::error('Failed to fetch reviews', [
                'message' => $e->getMessage(),
            ]);
            return ApiResponse::error(null, 'Failed to fetch reviews', 500);

        }
    }
    

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreReviewRequest $request)
    {
        try {
            $data = $request->validated();
        
            $bookingExists = Booking::where('id', $data['booking_id'])
                        ->where('patient_id', $data['patient_id'])
                        ->exists();

            if (! $bookingExists) {
                return ApiResponse::error(null, 'Patient does not have this booking', 403);
            }

            $exists = Review::where('booking_id', $data['booking_id'])->exists();
            if ($exists) {
                return ApiResponse::error(null, 'Review for this booking already exists', 409);
            }

            $review = Review::create($data);
            return ApiResponse::success(['review' => $review], "Review created successfully", 201); 

        } catch (\Throwable $e) {
            Log::error('Failed to create review', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while creating the review.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $review = Review::with('booking.doctor', 'booking.patient')->findOrFail($id);

            return ApiResponse::success(['review' => $review], "Review fetched successfully", 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        
            return ApiResponse::error(null, 'Review not found', 404);

        } catch (\Throwable $e) {
        
            Log::error('Failed to get review', [
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while getting the review.', 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
   public function update(StoreReviewRequest $request, string $id)
    {
        try {
        $data = $request->validated();

        $review = Review::where('id', $id)
                        ->where('booking_id', $data['booking_id'])
                        ->first();

        if (! $review) {
            return ApiResponse::error(null, 'Review for this booking does not exist', 404);
        }

        $bookingExists = Booking::where('id', $data['booking_id'])
                        ->where('patient_id', $data['patient_id'])
                        ->exists();

        if (! $bookingExists) {
            return ApiResponse::error(null, 'Patient does not have this booking', 403);
        }

        $review->fill($data);

        if ($review->isDirty()) {
            $review->save();
        }

        return ApiResponse::success(['review' => $review], "Review updated successfully", 200);

    } catch (\Throwable $e) {
        Log::error('Failed to update review', [
            'message' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
        return ApiResponse::error(null, 'An unexpected error occurred while updating the review.', 500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{   
    try {
        $review = Review::findOrFail($id);
        $review->delete();

        return ApiResponse::success(null, "Review deleted successfully", 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return ApiResponse::error(null, 'Review not found', 404);

    } catch (\Throwable $e) {
        Log::error('Failed to delete review', [
            'message' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
        return ApiResponse::error(null, 'An unexpected error occurred while deleting the review.', 500);
    }
}

}
