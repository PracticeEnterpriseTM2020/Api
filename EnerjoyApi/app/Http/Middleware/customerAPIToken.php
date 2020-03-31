<?php

namespace App\Http\Middleware;

use App\customer;
use Closure;

class customerAPIToken
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
        else {
            return response()->json(['success' => false, 'message' => 'user is not logged in'], 401);
        }
        return response(['success' => false, 'message' => 'Not a valid request'], 401);
    }
}
