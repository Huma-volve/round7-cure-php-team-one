<?php

return [
    // Booking messages
    'booking.created' => 'Booking created successfully',
    'booking.fetched' => 'Bookings fetched successfully',
    'booking.details_fetched' => 'Booking details fetched successfully',
    'booking.confirmed' => 'Booking confirmed successfully',
    'booking.cancelled' => 'Booking cancelled successfully',
    'booking.rescheduled' => 'Booking rescheduled successfully',
    'booking.not_found' => 'Booking not found',
    'booking.unauthorized' => 'You are not authorized to view this booking',
    'booking.not_yours' => 'This booking does not belong to you',
    'booking.conflict' => 'This time slot is not available, please choose another time',
    'booking.unavailable' => 'Time slot unavailable',

    // Payment messages
    'payment.created' => 'Payment created successfully',
    'payment.confirmed' => 'Payment confirmed successfully',
    'payment.fetched' => 'Payment details fetched successfully',

    // Status labels
    'status.pending' => 'Pending',
    'status.confirmed' => 'Confirmed',
    'status.cancelled' => 'Cancelled',
    'status.rescheduled' => 'Rescheduled',

    // Entity not found
    'patient.not_found' => 'Patient data not found',
    'doctor.not_found' => 'Doctor not found',

    // Payment method
    'payment_method.list' => 'Payment methods fetched successfully',
    'payment_method.created' => 'Payment method added successfully',
    'payment_method.deleted' => 'Payment method removed successfully',
    'payment_method.set_default' => 'Payment method set as default',
    'payment_method.cannot_set_default_deleted' => 'Cannot set a deleted payment method as default.',
    'payment_method.restored' => 'Payment method restored successfully',
    'payment_method.expiry_required' => 'Expiry month and year are required for card payments.',

    // Common messages
    'success' => 'Operation completed successfully',
    'error' => 'An error occurred',
    'validation_error' => 'The submitted data is invalid',
    'unauthorized' => 'You are not authorized to access this resource',
    'not_found' => 'Data not found',
    'server_error' => 'Server error occurred',
    'operation_error' => 'An error occurred during the operation',
];

