<?php

namespace App\Models\Transaksi;

use App\Models\Transaksi\LaporanReceiptModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanImageModel extends Model
{
    use HasFactory;
    public $table = 'laporan_images';
    public function getMaster(){
        return $this->hasOne(LaporanReceiptModel::class,'id','li_laporan_id');
    }
}
