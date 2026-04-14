<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = trim((string) $request->query('action', ''));

        $query = StaffActivity::query()->with('user:id,name,email')->latest();

        if ($action !== '') {
            $query->where('action', 'like', '%'.$action.'%');
        }

        $activities = $query->paginate(40)->withQueryString();

        return view('staff.activity.index', compact('activities', 'action'));
    }
}
