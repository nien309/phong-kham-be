<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LichHenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lichhen;

    public function __construct($lichhen)
    {
        $this->lichhen = $lichhen;
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt lịch khám')
                    ->view('emails.lichhen'); // Tạo view resources/views/emails/lichhen.blade.php
    }
}
