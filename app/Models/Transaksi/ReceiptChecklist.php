<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptChecklist extends Model
{
    use HasFactory;

    public $table = 'rcpt_checklist';

    
    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptc_rcpt_id');
    }
}
