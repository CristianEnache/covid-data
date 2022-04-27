<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CronJobFailed extends Mailable
{
    use Queueable, SerializesModels;

	public $reason;

    /**
     * Create a new reason instance.
     *
     * @return void
     */
    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.cronjob_failed')->with([
			'reason' => 'SOmething'
		]);
    }
}
