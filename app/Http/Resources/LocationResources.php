<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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

        $data = [
            't_domain' => $this['t_domain'],
            't_site_loc' => $this['t_loc'],
            't_loc_desc' => $this['t_loc'].' - '.$this['t_loc_desc'],
        ];
        Log::channel('loadRencanaProduksi')->info(collect($data));
        return $data;
    }

    // public function toResponse($request)
    // {
    //     return parent::toResponse($request)->withResponse(
    //         response()->json(['data' => $this->toArray($request)]),
    //         new Response('', 200)
    //     );
    // }
}
