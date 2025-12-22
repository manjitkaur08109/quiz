<?php

namespace App\Http\Middleware;


use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class AdminMiddleware extends Controller
{
    public function handle(Request $request, Closure $next)
    {

        if (!Auth::check()) {
            return $this->unauthorized('Unauthorized');
        }

        if ($request->user()->account_type !== 'admin') {
            return $this->forbidden('Admin Access Only');
        }

        return $next($request);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   
}
