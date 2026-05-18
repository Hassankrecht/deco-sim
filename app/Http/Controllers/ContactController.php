<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(ContactFormRequest $request)
    {
        // Validation is now handled by ContactFormRequest

        // Save in database first
        $contactMessage = ContactMessage::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'phone'   => $request->phone,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Send email notification
        try {
            Mail::to('you@example.com')->send(new ContactFormMail($request->all()));
        } catch (\Exception $e) {
            // Log email error but don't break the form submission
            Log::error('Contact form email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
