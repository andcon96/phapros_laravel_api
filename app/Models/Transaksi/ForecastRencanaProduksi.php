<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForecastRencanaProduksi extends Model
{
    use HasFactory;

    public $table = 'forecast_rencana_produksi';

    public $fillable = [
        'id_rp_item', 'isForecast', 'rp_mrp_nbr', 'rp_bulan', 'rp_tahun', 'rp_mrp_dataset',
        'rp_mrp_type', 'rp_mrp_due_date', 'rp_mrp_qty', 'created_at', 'updated_at'
    ];

    public function getItemMRP()
    {
        return $this->belongsTo(ItemMRP::class, 'id_rp_item');
    }
}
