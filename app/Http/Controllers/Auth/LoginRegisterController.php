<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Intervention\Image\Facades\Image;

class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout',
            'dashboard'
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
            'photo' => 'required|image|nullable|max:1999'
        ]);

        if ($request->hasFile('photo')) {
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            // $filenameSimpan = $filename . '_' . time() . '.' . $extension;

            $basename = uniqid() . time();
            $smallFilename  = "small_{$basename}.{$extension}";
            $mediumFilename  = "medium_{$basename}.{$extension}";
            $largeFilename  = "large_{$basename}.{$extension}";

            $filenameSimpan = "{$basename}.{$extension}";
            $path = $request->file('photo')->storeAs('photos', $filenameSimpan);

            $request->file('photo')->storeAs("photos", $smallFilename);
            $request->file('photo')->storeAs("photos", $mediumFilename);
            $request->file('photo')->storeAs("photos", $largeFilename);

            // small
            $smallThumbnailPath = storage_path("app/public/photos/{$smallFilename}");
            $this->createThumbnail($smallThumbnailPath, 150, 93);
            //medium
            $mediumThumbnailPath = storage_path("app/public/photos/{$mediumFilename}");
            $this->createThumbnail($mediumThumbnailPath, 300, 185);
            //large
            $largeThumbnailPath = storage_path("app/public/photos/{$largeFilename}");
            $this->createThumbnail($largeThumbnailPath, 550, 340);
        } else {
            $path = 'noimage.png';
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => $path
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();

        // $data = $request->all();
        // $data['subject'] = "Pendaftaran Berhasil";
        // $data['body'] = "Hallo, pendaftaran anda di aplikasi " . env('APP_NAME') . " berhasil.";

        // dispatch(new SendMailJob($data));

        return redirect()->route('dashboard')
            ->withSuccess('You have successfully registered & logged in!');
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');
    }

    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }

        return redirect()->route('login')
            ->withErrors([
                'email' => 'Please login to access the dashboard.',
            ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }
}
