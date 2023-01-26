<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PoApiResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'po_domain' => $this->po_domain,
            'po_nbr' => $this->po_nbr,
            'po_ship' => $this->po_ship,
            'po_site' => $this->po_site,
            'po_vend' => $this->po_vend,
            'po_ord_date' => $this->po_ord_date,
            'po_due_date' => $this->po_due_date,  
            'po_curr' => $this->po_curr,
            'po_ppn' => $this->po_ppn,
            'po_status' => $this->po_status,
            'po_total' => number_format($this->getDetail->sum(function($order){
                return $order->pod_qty_ord * $order->pod_pur_cost;
            }),2),
            'po_details' => $this->getDetail,
        ];
    }
}
