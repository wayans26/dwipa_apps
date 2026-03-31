<?php

namespace App\Http\Middleware;

use App\Http\Utils\responseMessage;
use App\Models\menu_sub_group;
use App\Models\user;
use App\Models\user_has_menu_sub_group;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class permissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resource, string $action): Response
    {
        $map = [
            'view'   => 'allow_view',
            'create' => 'allow_create',
            'update' => 'allow_update',
            'delete' => 'allow_delete',
            'print'  => 'allow_print'
        ];

        $token = $request->header('token');

        if (!isset($map[$action])) {
            return response()->json(['message' => 'Invalid permission action.'], 400);
        }

        $column = $map[$action];

        $cacheKey = "perm:role:{$token}:{$resource}";

        $perm = Cache::remember($cacheKey, Carbon::now()->addMinutes(10), function () use ($token, $resource, $column) {
            return menu_sub_group::join('user_has_menu_sub_groups as permission', 'menu_sub_groups.id', '=', 'permission.menu_sub_group_id')
                ->join('users', 'users.role_id', '=', 'permission.role_id')
                ->join('personal_tokens', 'personal_tokens.iduser', '=', 'users.id')
                ->where([
                    'menu_sub_groups.page_name'     => $resource,
                    'personal_tokens.token'         => $token,
                ])->first();
        });

        $allow = empty($perm) ? false : ($perm->{$column} == 1 ? true : false);

        if (!$allow) {
            return responseMessage::responseMessage(0, "You don't have permission to perform this action", 200);
        }

        return $next($request);
    }
}
