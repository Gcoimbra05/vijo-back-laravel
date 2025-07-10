<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BetaWeek1 extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;
    public $pageUrl;

    public function __construct($recipientName, $pageUrl)
    {
        $this->recipientName = $recipientName;
        $this->pageUrl = $pageUrl;
    }

    public function build()
    {
        return $this->view('emails.template')
            ->with([
                'title' => "Beta Week 1: Let's Make Magic Happen!",
                'contentView' => 'emails.beta_week_1',
                'contentData' => [
                    'recipientName' => $this->recipientName,
                    'pageUrl' => $this->pageUrl,
                ],
            ])
            ->subject("Beta Week 1: Let's Make Magic Happen!");
    }
}