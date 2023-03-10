<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptDocument extends Model
{
    use HasFactory;

    public $table = 'rcpt_document';

    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptdoc_rcpt_id');
    }
}
