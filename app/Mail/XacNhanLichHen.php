<?php


namespace App\Mail;

use App\Models\LichHen;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class XacNhanLichHen extends Mailable
{
    use Queueable, SerializesModels;

    public $lichhen;

    public function __construct(LichHen $lichhen)
    {
        $this->lichhen = $lichhen;
    }

    public function build()
    {
        return $this->subject('Xác nhận lịch hẹn')
                    ->view('emails.xacnhan_lichhen');
    }
}
