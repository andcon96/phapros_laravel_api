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

    public function getChecklist(){
        return $this->hasMany(ReceiptChecklist::class,'rcptc_rcpt_id');
    }

    public function getDocument(){
        return $this->hasMany(ReceiptDocument::class,'rcptdoc_rcpt_id');
    }

    public function getKemasan(){
        return $this->hasMany(ReceiptKemasan::class,'rcptk_rcpt_id');
    }

    public function getTransport(){
        return $this->hasMany(ReceiptTransport::class,'rcptt_rcpt_id');
    }

    public function getHistoryApproval(){
        return $this->hasMany(ApprovalHist::class,'apphist_receipt_id');
    }

    public function getIsOngoinApproval(){
        return $this->hasMany(ApprovalHist::class,'apphist_receipt_id')->whereNotNull('apphist_approved_date');
    }
    public function getLaporan(){
        return $this->hasOne(LaporanReceiptModel::class,'laporan_rcptnbr','rcpt_nbr')->latest();
    }
    public function getApprHistlast(){
        return $this->hasOne(ApprovalHist::class,'apphist_receipt_nbr','id');
    }
    public function getApprHistfirst(){
        return $this->hasOne(ApprovalHist::class,'apphist_receipt_nbr','id');
    }

}
