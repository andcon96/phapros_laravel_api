<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptKemasan extends Model
{
    use HasFactory;

    public $table = 'rcpt_kemasan';
    
    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptk_rcpt_id');
    }
}
