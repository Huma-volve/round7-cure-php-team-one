<?php

return [
    'default_log_name' => 'default',

    'activity_model' => \Spatie\Activitylog\Models\Activity::class,

    'table_name' => 'activity_log',

    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),

    'log_events' => [
        // we will log custom events from controllers
    ],

    'subject_returns_soft_deleted_models' => false,

    'causer_returns_soft_deleted_models' => false,

    'default_causer_resolver' => null,

    'default_auth_guard' => null,

    'logging_enabled' => env('ACTIVITY_LOGGER_ENABLED', true),
];


