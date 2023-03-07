<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    use HasFactory;
    public $table = 'rcptd_det';

    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptd_rcpt_id');
    }


}
