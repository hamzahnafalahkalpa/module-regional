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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => $this->getLocation($this->getMorphClass(),$this->name)
        ];
        if ($this->getMorphClass() == 'Village'){
            $arr['post_code'] = $this->post_code;
        }
        return $arr;
    }
}
