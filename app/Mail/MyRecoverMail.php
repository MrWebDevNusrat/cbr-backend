<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyRecoverMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message. it is for recover
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from unilibrarydev@gmail.com')
            ->view('email.reset');
    }

//    public function build()
//    {
//        $address = 'nusratakhmadjonovich@gmail.com';
//        $subject = 'This is a demo!';
//        $name = 'Jane Doe';
//
//        return $this->view('email.reset')
//            ->from($address, $name)
//            ->cc($address, $name)
//            ->bcc($address, $name)
//            ->replyTo($address, $name)
//            ->subject($subject)
//            ->with([ 'test_message' => $this->details['message'] ]);
//    }
}
