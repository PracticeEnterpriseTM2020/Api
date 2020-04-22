<?php

namespace App\Http\Middleware;

use App\Employee;
use Closure;

class employeeAPIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization')) {
            return $next($request);
        }
        return response(['success' => false, 'message' => 'Not a valid request'], 401);
    }
}
