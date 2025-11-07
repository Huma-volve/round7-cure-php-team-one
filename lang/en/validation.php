<?php

return [
    // Booking validation
    'doctor_id.required' => 'Doctor selection is required',
    'doctor_id.exists' => 'The selected doctor does not exist',
    'date_time.required' => 'Appointment date and time are required',
    'date_time.date' => 'Invalid appointment date and time format',
    'date_time.after' => 'Cannot book an appointment in the past',
    'payment_method.required' => 'Payment method selection is required',
    'payment_method.in' => 'Invalid payment method',

    // Reschedule validation
    'date_time.required' => 'New appointment date and time are required',
    'date_time.after' => 'Cannot schedule an appointment in the past',

    // Payment validation
    'booking_id.required' => 'Booking ID is required',
    'booking_id.exists' => 'The selected booking does not exist',
    'gateway.required' => 'Payment gateway selection is required',
    'gateway.in' => 'Invalid payment gateway',
    'currency.required' => 'Currency is required',
    'currency.size' => 'Currency code must be 3 characters',
    'amount.required' => 'Amount is required',
    'amount.numeric' => 'Amount must be a number',
    'amount.min' => 'Amount must be at least 0.50',
    'description.max' => 'Description must not exceed 255 characters',
    'return_url.url' => 'Invalid return URL format',
    'return_url.required_if' => 'Return URL is required when using PayPal',
    'cancel_url.url' => 'Invalid cancel URL format',
    'payment_id.required' => 'Payment ID is required',
    'payment_id.string' => 'Payment ID must be a string',

    // Standard Laravel validation messages
    'required' => 'The :attribute field is required',
    'exists' => 'The selected :attribute does not exist',
    'date' => 'The :attribute must be a valid date',
    'after' => 'The :attribute must be after :date',
    'in' => 'The selected :attribute is invalid',
    'numeric' => 'The :attribute must be a number',
    'min' => 'The :attribute must be at least :min',
    'max' => 'The :attribute must not exceed :max',
    'size' => 'The :attribute must be :size',
    'url' => 'The :attribute must be a valid URL',
    'string' => 'The :attribute must be a string',
    'required_if' => 'The :attribute field is required when :other is :value',
];

