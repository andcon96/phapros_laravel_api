<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptTransport extends Model
{
    use HasFactory;

    public $table = 'rcpt_transport';

    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptt_rcpt_id');
    }
}
