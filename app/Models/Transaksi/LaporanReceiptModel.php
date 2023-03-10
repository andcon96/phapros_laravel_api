<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanReceiptModel extends Model
{
    use HasFactory;
    public $table = 'laporan_receipt';

    // public function getMaster(){
    //     return $this->belongsTo(PurchaseOrderMaster::class, 'pod_po_id');
    // }
}
