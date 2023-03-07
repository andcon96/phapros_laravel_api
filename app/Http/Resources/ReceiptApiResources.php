<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptApiResources extends JsonResource
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
            'rcpt_domain' => $this->rcpt_domain,
            'rcpt_nbr' => $this->rcpt_nbr,
            'rcpt_ship' => $this->rcpt_status,
            'rcpt_site' => $this->rcpt_site,
            'rcpt_details' => $this->getDetail,
            'rcpt_po' => $this->getpo,
            'rcpt_user' => $this->getuser
        ];
    }
}
