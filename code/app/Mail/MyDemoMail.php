<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyDemoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $userid;
    protected $token;
    protected $pdfname;
    public function __construct($userid,$token,$pdfname)
    {

         $this->userid = $userid;
        $this->token = $token;
        $this->pdfname = $pdfname;
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->from('shipjam.com')
                ->view('emails.myDemoMail')->withid($this->userid)->withtoken($this->token)->withimg($this->pdfname);

                    // ->attach(public_path('pdf/sample.pdf'), [
                    //      'as' => 'sample.pdf',
                    //      'mime' => 'application/pdf',
                    // ]);
    }
}
