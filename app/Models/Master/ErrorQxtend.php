<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorQxtend extends Model
{
    use HasFactory;

    public $table = 'error_qxtend_approve';

    protected $fillable = [
        'id'
    ];
}
