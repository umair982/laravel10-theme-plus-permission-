<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Session\Session as SessionSession;

class AdminController extends Controller
{
    public function dashboard()
    {
        $title="Dashboard";
        return view('admin.dashboard.index',compact('title'));
    }
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.profile.edit', ['title' => 'Edit Admin'])->withAdmin($admin);
    }
    public function update(Request $request)
    {
        $admin = Auth::user();
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|unique:admins,email,'.$admin->id,
        ]);
        $input = $request->all();
        if (empty($input['password'])) {
            $input['password'] = $admin->password;
        } else {
            $input['password'] = bcrypt($input['password']);
        }
        $admin->fill($input)->save();
        Session::flash('success_message', 'Great! profile successfully updated!');
        return redirect()->back();
    }
}
