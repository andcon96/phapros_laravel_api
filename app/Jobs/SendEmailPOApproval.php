<?php

namespace App\Jobs;

use App\Models\User2;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

        $useremail = User2::query()
            ->with('connectingUser')
            ->where('can_receive_email', 1)
            ->get();
        $listemail = [];

        foreach ($useremail as $key => $datas) {
            if ($datas->connectingUser->email != null) {
                $listemail[] = $datas->connectingUser->email;
            }
        }

        if (count($listemail) > 0) {
            try {
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
                    function ($message) use ($ponbr, $receiptnbr,$listemail) {
                        $message->subject('Purchase Order .' . $ponbr . ', Receipt Number.' . $receiptnbr . ' Needs Your Approval');
                        $message->to($listemail);
                    }
                );
            } catch (Exception $e) {
                Log::channel('savepo')->info('Error Email : ' . $e);
            }
        }
    }
}
