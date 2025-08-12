<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;
use App\Models\Contact;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'phone'   => 'nullable',
            'subject' => 'nullable',
            'message' => 'required',
        ]);

        Contact::create($data);

        // Gửi mail cho admin
        Mail::send('emails.contact', ['data' => $data], function ($message) use ($data) {
            $message->to('magstudiostore@gmail.com')
                    ->subject('Liên hệ từ khách hàng: ' . ($data['subject'] ?? 'Không có chủ đề'));
        });

        // Gửi auto mail cho khách hàng
        Mail::to($data['email'])->send(new ContactReplyMail($data['name']));

        return back()->with('success', 'Tin nhắn của bạn đã được gửi thành công!');
    }
}
