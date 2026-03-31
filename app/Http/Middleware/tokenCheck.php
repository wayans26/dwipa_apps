<?php

namespace App\Http\Middleware;

use App\Http\Utils\responseMessage;
use App\Models\personal_token;
use App\Models\user;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class tokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token');
        if (empty($token)) {
            return responseMessage::responseMessage(401, "Unauthorized", 401);
        }
        $pt = personal_token::select('iduser')->where('token', $token)->first();
        if (!empty($pt)) {
            $users = Cache::remember($token, Carbon::now()->addMinutes(120), function () use ($pt) {
                return user::select('id', 'username', 'full_name')->where('id', $pt->iduser)->first();
            });
            $request->merge(['users' => $users]);
            return $next($request);
        }
        return responseMessage::responseMessage(401, "Unauthorized", 401);
    }
}
