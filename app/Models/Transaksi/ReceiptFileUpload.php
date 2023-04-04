<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptFileUpload extends Model
{
    use HasFactory;

    public $table = 'rcpt_file_upload';
    
    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptfu_rcpt_id');
    }
}
