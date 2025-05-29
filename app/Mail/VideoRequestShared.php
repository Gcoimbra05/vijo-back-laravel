<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VideoRequestShared extends Mailable
{
    use Queueable, SerializesModels;

    public $videoRequest;

    public function __construct($videoRequest)
    {
        $this->videoRequest = $videoRequest;
    }

    public function build()
    {
        return $this->subject('You have received a video request!')
            ->view('emails.video_request_shared')
            ->with(['videoRequest' => $this->videoRequest]);
    }
}
