<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResources extends JsonResource
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
            't_domain' => $this['t_domain'],
            't_site_loc' => $this['t_site'].' || '.$this['t_loc'],
            't_loc_desc' => $this['t_site'].' - '.$this['t_loc'].' - '.$this['t_loc_desc'],
        ];
    }
}
