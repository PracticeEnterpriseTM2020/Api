<?php

namespace App\Http\Middleware;

use Closure;

class APIToken
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
        if($request->header('Authorization')){
            return $next($request);
        }
        else{
            return response(['success'=>false,'message'=>'Not a valid request'],401);
        }
    }
}
