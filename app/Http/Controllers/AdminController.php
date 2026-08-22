<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login()
    {
        return Auth::check()
            ? redirect()->route('admin.dashboard')
            : view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email or password is incorrect.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        $enquiries = DB::table('enquiries')->latest()->paginate(25);

        $counts = [
            'total' => DB::table('enquiries')->count(),
            'new' => DB::table('enquiries')->where('status', 'new')->count(),
            'contacted' => DB::table('enquiries')->where('status', 'contacted')->count(),
            'completed' => DB::table('enquiries')->where('status', 'completed')->count(),
        ];

        return view('admin.dashboard', compact('enquiries', 'counts'));
    }

    public function changePassword()
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase and a number.',
        ]);

        $user = Auth::user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $request->session()->regenerate();

        return back()->with('success', 'Password changed successfully.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,in_progress,completed,closed'],
        ]);

        DB::table('enquiries')->where('id', $id)->update([
            'status' => $data['status'],
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Enquiry status updated.');
    }

    public function destroy(int $id)
    {
        DB::table('enquiries')->where('id', $id)->delete();
        return back()->with('success', 'Enquiry deleted.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
