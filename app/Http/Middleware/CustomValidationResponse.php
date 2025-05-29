<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class CustomValidationResponse
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}