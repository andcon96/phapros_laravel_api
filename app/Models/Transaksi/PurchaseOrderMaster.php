<?php

namespace App\Models\Transaksi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Awobaz\Compoships\Compoships;

class PurchaseOrderMaster extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    public $table = 'po_mstr';

    public function getDetail(){
        return $this->hasMany(PurchaseOrderDetail::class, 'pod_po_id');
    }

    public function getHistoryReceipt(){
        return $this->hasMany(ReceiptMaster::class,'rcpt_po_id');
    }

    public function getApprovalHistReceiptByPO(){
        return $this->hasMany(ApprovalHist::class, ['apphist_po_domain','apphist_po_nbr'], ['po_domain','po_nbr'])
                    ->whereNotNull('apphist_approved_date');
    }

    public function getMstAnggota(){
        return $this->hasOne(User::class, 'apphist_user_id', 'id_anggota');
    }
}
