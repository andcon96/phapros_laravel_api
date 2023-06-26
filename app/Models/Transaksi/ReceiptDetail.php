<?php

namespace App\Models\Transaksi;

use App\Models\Master\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    use HasFactory;
    public $table = 'rcptd_det';

    public function getMaster(){
        return $this->hasOne(ReceiptMaster::class,'id','rcptd_rcpt_id');
    }
    public function getItem(){
        return $this->hasOne(Item::class,'item_code','rcptd_part');
    }
    public function getLaporan(){
        return $this->hasOne(LaporanReceiptModel::class,'laporan_rcptnbr','rcpt_nbr')->latest();
    }


}
