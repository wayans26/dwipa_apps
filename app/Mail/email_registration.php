<?php

namespace App\Mail;

use App\Models\exhibitions;
use App\Models\sub_exhibitions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class email_registration extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    public function build()
    {
        $exhibition = exhibitions::find($this->data['idexhibitions']);
        $subExhibitions = sub_exhibitions::find($this->data['idsubexhibitions']);
        if ($this->data['type'] == 'busworld') {
            return $this->subject($this->data['subject'])
                ->from($this->data['from'], $this->data['from_name'])
                ->view('Email.emilBusworld', [
                    'data'              => $this->data,
                ]);
        }

        return $this->subject($this->data['subject'])
            ->from($this->data['from'], $this->data['from_name'])
            ->view('Email.emailUndangan', [
                'data'              => $this->data,
            ]);
    }
}
