<?php

namespace Hanafalah\ModuleRegional\Resources\Address;

use Illuminate\Http\Request;
use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewAddress extends ApiResource
{

    /**
     * Transform the resource into an array.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $arr = [
            'id'                => $this->id,
            'flag'              => $this->flag,
            'name'              => $this->name,
            'zip_code'          => $this->zip_code,
            'rt'                => $this->rt,
            'rw'                => $this->rw,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'province_id'       => $this->province_id,
            'district_id'       => $this->district_id,
            'subdistrict_id'    => $this->subdistrict_id,
            'village_id'        => $this->village_id,
            'province'          => $this->prop_province,
            'district'          => $this->prop_district,
            'subdistrict'       => $this->prop_subdistrict,
            'village'           => $this->prop_village
        ];
        return $arr;
    }
}
