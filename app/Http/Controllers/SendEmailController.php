<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class SendEmailController extends Controller
{
    public function index()
    {
        $content = [
            'name' => 'Ini Nama Pengirim',
            'subject' => 'Ini subject email',
            'body' => 'Ini adalah isi email yang dikirim dari laravel 10'
        ];

        // Mail::to('choirudin.emcha@gmail.com')->send(new SendEmail($content));
        Mail::to('choirudin.emcha@gmail.com')->send(new SendEmail($content));

        return "Email berhasil dikirim.";
    }
}
