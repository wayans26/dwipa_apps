<?php

namespace App\Http\Middleware;

use App\Http\Utils\responseMessage;
use App\Models\tbusers;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class scheduleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cmd = $request->header('cmd');

        if (empty($cmd)) {
            return responseMessage::responseMessage(400, "Bad Request", 400);
        }
        $schedule = tbusers::join('tbexhibitors', 'tbexhibitors.iduser', '=', 'tbusers.iduser')
            ->join('tbschedules', 'tbschedules.idschedule', '=', 'tbexhibitors.idschedule')
            ->select('tbschedules.*')
            ->where('token', $request->header('token'))->first();

        if (empty($schedule)) {
            return responseMessage::responseMessage(400, "Bad Request SCHEDULE", 400);
        }

        if ($cmd === 'Form') {
            if (Carbon::parse($schedule->deadline)->endOfDay()->greaterThanOrEqualTo(Carbon::now()->endOfDay())) {
                return $next($request);
            } else {
                return responseMessage::responseMessage(400, "Bad Request", 400);
            }
        } else if ($cmd === 'Rent') {
            if (Carbon::parse($schedule->rent_deadline2)->endOfDay()->greaterThanOrEqualTo(Carbon::now()->endOfDay())) {
                return $next($request);
            } else {
                return responseMessage::responseMessage(400, "Bad Request", 400);
            }
        } else {
            return responseMessage::responseMessage(400, "Bad Request", 400);
        }
    }
}
