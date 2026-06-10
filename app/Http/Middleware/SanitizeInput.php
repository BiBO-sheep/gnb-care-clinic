<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * The attributes that should not be sanitized.
     */
    protected $except = [
        'password',
        'password_confirmation',
        'old_password',
        'new_password',
        'avatar',
        'foto',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value, $key) {
            if (!in_array($key, $this->except) && is_string($value)) {
                $value = strip_tags($value);
            }
        });
        
        $request->merge($input);

        return $next($request);
    }
}
