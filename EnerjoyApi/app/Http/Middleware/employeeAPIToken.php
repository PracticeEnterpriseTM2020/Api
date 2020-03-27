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
            $token = $request->header('Authorization');
            $employee = employee::where('api_token', $token)->first();
            if ($employee) {
                return $next($request);
            } else {
                return response()->json(['success' => false, 'message' => 'user is not logged in'], 401);
            }
        }
        return response(['success' => false, 'message' => 'Not a valid request'], 401);
    }
}
