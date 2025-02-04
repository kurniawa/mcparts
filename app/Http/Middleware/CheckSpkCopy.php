<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckSpkCopy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $spk = $request->route('spk'); // Ambil parameter spk dari route
        // dd($spk);
        // Pastikan parameter spk adalah model instance
        if (!$spk || !$spk->copy) {
            throw new NotFoundHttpException(); // Tampilkan 404 jika tidak sesuai
        }

        return $next($request);
    }
}
