<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffModulePermissionMiddleware
{
    /**
     * @param  string  $module  orders|reviews|inventory
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ($user->is_admin) {
            return $next($request);
        }

        if (! $user->is_staff) {
            abort(403);
        }

        $allowed = match ($module) {
            'orders' => $user->staffCanOrders(),
            'reviews' => $user->staffCanReviews(),
            'inventory' => $user->staffCanInventory(),
            default => false,
        };

        if (! $allowed) {
            abort(403, 'Bạn không có quyền truy cập mục này.');
        }

        return $next($request);
    }
}
