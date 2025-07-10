<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BetaWeek2 extends Mailable
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
                'title' => "Beta Week 2: Your Journey, Unlocked!",
                'contentView' => 'emails.beta_week_2',
                'contentData' => [
                    'recipientName' => $this->recipientName,
                    'pageUrl' => $this->pageUrl,
                ],
            ])
            ->subject("Beta Week 2: Your Journey, Unlocked!");
    }
}