<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Please login to access the dashboard.',
                ])->onlyInput('email');
        }

        $users = User::get();

        return view('users')->with('userss', $users);
    }

    public function show($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Please login to access the dashboard.',
                ])->onlyInput('email');
        }

        $user = User::find($id);

        return view('users')->with('userss', $user);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        $file = public_path() . '/storage/' . $user->photo;

        try {
            if (File::exists($file)) {
                File::delete($file);
                $user->delete();
            }
        } catch (\Throwable $th) {
            return redirect('users')->with('error', 'Gagal hapus data');
        }

        return redirect('users')->with('success', 'Berhasil hapus data');
    }
}
