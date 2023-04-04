<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailPOApproval implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $datareceipt;

    public function __construct($datareceipt)
    {
        $this->datareceipt = $datareceipt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $datareceipt = $this->datareceipt;
        
        $ponbr = $datareceipt->getpo->po_nbr ?? '';
        $receiptnbr = $datareceipt->rcpt_nbr ?? '';
        $nik = $datareceipt->getUser->nik ?? '';
        $nama = $datareceipt->getUser->nama ?? '';
        Mail::send(
            'email.poapproval',
            [
                'datareceipt' => $datareceipt,
                'detailreceipt' => $datareceipt->getDetail,
                'ponbr' => $ponbr,
                'receiptnbr' => $receiptnbr,
                'nik' => $nik,
                'nama' => $nama,
            ],
            function ($message) use($ponbr, $receiptnbr){
                $message->from('andrew@ptimi.co.id');
                $message->subject('Purchase Order .'.$ponbr.', Receipt Number.'.$receiptnbr.' Needs Your Approval');
                $message->to('michael@ptimi.co.id');
            }
        );
    }
}
