<?php

namespace App\Models\Transaksi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalHist extends Model
{
    use HasFactory;

    public $table = 'approval_hist';

    public function getUser(){
        return $this->hasOne(User::class,'id','apphist_user_id');
    }
}
