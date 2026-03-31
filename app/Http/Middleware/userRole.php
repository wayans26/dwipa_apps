<?php

namespace App\Http\Middleware;

use App\Http\Utils\responseMessage;
use App\Models\tbusers;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class userRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = request()->header('token');
        if (empty($token)) {
            return responseMessage::responseMessage(401, "Unauthorized", 401);
        }
        if (tbusers::where([
            'token' => $request->header('token'),
            'role'  => 'user'
        ])->exists()) {
            $exhibitor = tbusers::join('tbexhibitors', 'tbexhibitors.iduser', '=', 'tbusers.iduser')
                ->select('tbexhibitors.*')
                ->where('token', $token)
                ->first();
            $request->attributes->set('idexhibitor', $exhibitor->idexhibitor);
            return $next($request);
        }
        return responseMessage::responseMessage(401, "Unauthorized", 401);
    }
}
