<?php

namespace Hanafalah\ModuleRegional\Resources\Location;

use Illuminate\Http\Request;
use Hanafalah\LaravelSupport\Resources\ApiResource;

class ViewLocation extends ApiResource
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
            'id'   => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,
            'location'       => $this->getLocation($this->getMorphClass(),$this->name)
        ];
        switch ($this->getMorphClass()) {
            case 'Village':
                $arr['post_code']      = $this->post_code;
                $arr['province_id']    = $this->province_id;
                $arr['district_id']    = $this->district_id;
                $arr['subdistrict_id'] = $this->subdistrict_id;
                $arr['village_id']     = $this->id;
                $arr['village']        = $this->prop_village;
            break;
            case 'District':
                $arr['province_id']    = $this->province_id;
                $arr['district_id']    = $this->id;
            break;
            case 'Subdistrict':
                $arr['province_id']    = $this->province_id;
                $arr['district_id']    = $this->district_id;
                $arr['subdistrict_id'] = $this->id;
                $arr['subdistrict']    = $this->prop_subdistrict;
            break;
            case 'Province':
                $arr['province_id']    = $this->id;
            break;
        }
        return $arr;
    }
}
