<?php

namespace App\Models\Transaksi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptMaster extends Model
{
    use HasFactory;
    public $table = 'rcpt_mstr';

    public function getDetail(){
        return $this->hasMany(ReceiptDetail::class,'rcptd_rcpt_id');
    }

    public function getpo(){
        return $this->hasOne(PurchaseOrderMaster::class,'id','rcpt_po_id');
    }
    
    public function getUser(){
        return $this->hasOne(User::class,'id','rcpt_user_id');
    }
}
