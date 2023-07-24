<?php

namespace App\Models\Transaksi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanReceiptModel extends Model
{
    use HasFactory;
    public $table = 'laporan_receipt';

    public function getUserLaporan(){
        
        return $this->hasOne(User::class,'id_anggota','laporan_anggota');
    }
    public function getfoto(){
        return $this->hasMany(LaporanImageModel::class,'li_laporan_id','id');
    }
}
