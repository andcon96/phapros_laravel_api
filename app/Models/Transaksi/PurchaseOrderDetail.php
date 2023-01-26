<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    public $table = 'pod_det';

    public function getMaster(){
        return $this->belongsTo(PurchaseOrderMaster::class, 'pod_po_id');
    }
}
