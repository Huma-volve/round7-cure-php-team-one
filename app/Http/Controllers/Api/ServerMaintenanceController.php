<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ServerMaintenanceController extends Controller
{
    /**
     * Get the base path of the project
     */
    private function getBasePath()
    {
        return base_path();
    }

    /**
     * Execute shell command in project directory
     */
    private function executeCommand($command)
    {
        $basePath = $this->getBasePath();
        $originalDir = getcwd();
        
        try {
            // Change to project directory
            chdir($basePath);
            
            // Execute command
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);
            
            return [
                'output' => $output,
                'return_code' => $returnCode
            ];
        } finally {
            // Restore original directory
            if ($originalDir) {
                chdir($originalDir);
            }
        }
    }

    /**
     * Run composer update
     */
    public function composerUpdate(Request $request)
    {
        try {
            $result = $this->executeCommand('composer update --no-interaction');
            
            if ($result['return_code'] !== 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Composer update failed',
                    'output' => implode("\n", $result['output']),
                ], 500);
            }

            Log::info('Composer update executed via API', ['user_id' => auth()->id()]);

            return response()->json([
                'status' => true,
                'message' => 'Composer update completed successfully',
                'output' => implode("\n", $result['output']),
            ]);
        } catch (\Exception $e) {
            Log::error('Composer update error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error executing composer update',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run composer dumpautoload
     */
    public function composerDumpAutoload(Request $request)
    {
        try {
            $result = $this->executeCommand('composer dumpautoload --no-interaction');
            
            if ($result['return_code'] !== 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Composer dumpautoload failed',
                    'output' => implode("\n", $result['output']),
                ], 500);
            }

            Log::info('Composer dumpautoload executed via API', ['user_id' => auth()->id()]);

            return response()->json([
                'status' => true,
                'message' => 'Composer dumpautoload completed successfully',
                'output' => implode("\n", $result['output']),
            ]);
        } catch (\Exception $e) {
            Log::error('Composer dumpautoload error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error executing composer dumpautoload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all Laravel caches
     */
    public function optimizeClear(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            
            $output = Artisan::output();

            Log::info('Optimize clear executed via API', ['user_id' => auth()->id()]);

            return response()->json([
                'status' => true,
                'message' => 'Cache cleared successfully',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error('Optimize clear error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error clearing cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run fresh migrations with seeding
     */
    public function migrateFreshSeed(Request $request)
    {
        try {
            // Confirm before running (safety check)
            $confirm = $request->input('confirm', false);
            
            if (!$confirm) {
                return response()->json([
                    'status' => false,
                    'message' => 'This action requires confirmation. Please send confirm=true in the request body.',
                    'warning' => 'This will drop all tables and recreate them!',
                ], 400);
            }

            Artisan::call('migrate:fresh', ['--seed' => true]);
            
            $output = Artisan::output();

            Log::warning('Migrate fresh with seed executed via API', ['user_id' => auth()->id()]);

            return response()->json([
                'status' => true,
                'message' => 'Database migrated and seeded successfully',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error('Migrate fresh error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error running migrations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run all maintenance commands in sequence
     */
    public function runAll(Request $request)
    {
        try {
            $results = [];
            $confirm = $request->input('confirm', false);

            // Run composer update
            $results['composer_update'] = $this->composerUpdate($request)->getData(true);
            
            // Run composer dumpautoload
            $results['composer_dumpautoload'] = $this->composerDumpAutoload($request)->getData(true);
            
            // Clear cache
            $results['optimize_clear'] = $this->optimizeClear($request)->getData(true);

            // Only run migrate fresh if confirmed
            if ($confirm) {
                $migrateRequest = new Request(['confirm' => true]);
                $results['migrate_fresh_seed'] = $this->migrateFreshSeed($migrateRequest)->getData(true);
            } else {
                $results['migrate_fresh_seed'] = [
                    'status' => false,
                    'message' => 'Skipped. Send confirm=true to run migrations.',
                ];
            }

            $allSuccess = collect($results)->every(fn($result) => $result['status'] ?? false);

            return response()->json([
                'status' => $allSuccess,
                'message' => $allSuccess ? 'All commands executed successfully' : 'Some commands failed',
                'results' => $results,
            ], $allSuccess ? 200 : 500);
        } catch (\Exception $e) {
            Log::error('Run all maintenance error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error running maintenance commands',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

