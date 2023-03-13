<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalHist extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    public $table = 'approval_hist';
}
