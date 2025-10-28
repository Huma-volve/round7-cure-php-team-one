<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Cure Platform
|--------------------------------------------------------------------------
|
| All API routes are loaded via route service provider.
| Routes are organized in separate files for better maintainability:
| - routes/api/public.php   -> Public endpoints (no auth required)
| - routes/api/patient.php  -> Patient endpoints (role: patient)
| - routes/api/doctor.php   -> Doctor endpoints (role: doctor)
| - routes/api/admin.php    -> Admin endpoints (role: admin)
| - routes/api/shared.php   -> Shared endpoints (authenticated users)
|
*/

// Authentication routes in public.php

// Load route files
require __DIR__.'/api/public.php';
require __DIR__.'/api/shared.php';
require __DIR__.'/api/patient.php';
require __DIR__.'/api/doctor.php';
require __DIR__.'/api/admin.php';
