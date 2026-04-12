<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || (! ($user->is_staff ?? false) && ! ($user->is_admin ?? false))) {
            abort(403, 'Bạn không có quyền truy cập khu vực nhân viên.');
        }

        if (($user->is_staff ?? false) && ! ($user->is_admin ?? false) && ! $user->staffHasAnyModulePermission()) {
            abort(403, 'Tài khoản nhân viên chưa được gán quyền module (đơn hàng / đánh giá / kho).');
        }

        return $next($request);
    }
}
