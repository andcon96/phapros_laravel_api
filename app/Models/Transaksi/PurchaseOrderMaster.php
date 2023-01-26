<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderMaster extends Model
{
    use HasFactory;

    public $table = 'po_mstr';

    public function getDetail(){
        return $this->hasMany(PurchaseOrderDetail::class, 'pod_po_id');
    }
}
