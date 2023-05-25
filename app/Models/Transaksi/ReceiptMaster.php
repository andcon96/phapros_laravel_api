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
        return $this->hasOne(User::class,'id_anggota','rcpt_user_id');
    }

    public function getChecklist(){
        return $this->hasOne(ReceiptChecklist::class,'rcptc_rcpt_id');
    }

    public function getDocument(){
        return $this->hasOne(ReceiptDocument::class,'rcptdoc_rcpt_id');
    }

    public function getKemasan(){
        return $this->hasOne(ReceiptKemasan::class,'rcptk_rcpt_id');
    }

    public function getTransport(){
        return $this->hasOne(ReceiptTransport::class,'rcptt_rcpt_id');
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
    public function getAppr(){
        return $this->hasOne(ApprovalHist::class,'apphist_receipt_id','id');
    }

    public function getDetailReject(){
        return $this->hasMany(ReceiptDetail::class,'rcptd_rcpt_id')->where('rcptd_qty_rej','>',0);
    }
    public function getFileUpload(){
        return $this->hasMany(ReceiptFileUpload::class,'rcptfu_rcpt_id');
    }
}
