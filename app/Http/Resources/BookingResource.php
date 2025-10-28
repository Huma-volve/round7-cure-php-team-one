<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_time' => $this->date_time->format('Y-m-d H:i:s'),
            'date_time_formatted' => $this->date_time->format('d M Y h:i A'),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'payment_method' => $this->payment_method,
            'price' => (float) $this->price,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'can_cancel' => $this->isCancellable(),
            'can_reschedule' => $this->isReschedulable(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'معلق',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            'rescheduled' => 'إعادة جدولة',
            default => $this->status,
        };
    }
}

