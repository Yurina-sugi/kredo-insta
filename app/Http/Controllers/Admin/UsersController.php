<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        }
        $all_users = $query->withTrashed()->latest()->paginate(5);
        return view('admin.users.index')->with('all_users', $all_users);
    }

    public function deactivate($id)
    {
        $this->user->destroy($id);
        return redirect()->back();
    }

    public function activate($id)
    {
        $this->user->onlyTrashed()->findOrFail($id)->restore();
        return redirect()->back();
    }
}
