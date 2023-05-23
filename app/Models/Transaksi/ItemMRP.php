<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMRP extends Model
{
    use HasFactory;

    public $table = 'item_m_r_p';

    public $fillable = [
        'id', 'item_code', 'item_design_group', 'item_description',
        'item_pareto', 'item_make_to_type', 'item_ed', 'item_bv', 'item_last_stock_date',
        'item_stock_qty', 'created_at', 'updated_at'
    ];

    public function hasRencanaProduksi()
    {
        return $this->hasMany(ForecastRencanaProduksi::class, 'id_rp_item');
    }

    public function totalRencanaProduksi()
    {
        return $this->hasMany(ForecastRencanaProduksi::class, 'id_rp_item')
            ->select('id_rp_item', 'rp_mrp_tahun', 'rp_mrp_bulan', \DB::raw('SUM(rp_mrp_qty) as totalRencanaProduksi'))
            ->where('isForecast', 0)
            ->orderByRaw('YEAR(rp_mrp_due_date) ASC, MONTH(rp_mrp_due_date)')
            ->groupBy('id_rp_item', 'rp_mrp_tahun', 'rp_mrp_bulan');
    }

    public function totalForecast()
    {
        return $this->hasMany(ForecastRencanaProduksi::class, 'id_rp_item')
            ->select('id_rp_item', 'rp_mrp_tahun', 'rp_mrp_bulan', \DB::raw('SUM(rp_mrp_qty) as totalForecast'))
            ->where('isForecast', 1)
            ->orderByRaw('YEAR(rp_mrp_due_date) ASC, MONTH(rp_mrp_due_date)')
            ->groupBy('id_rp_item', 'rp_mrp_tahun', 'rp_mrp_bulan');
    }
}
