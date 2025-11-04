<?php

namespace Hanafalah\ModuleRegional\Resources\Address;

use Illuminate\Http\Request;

class ShowAddress extends ViewAddress
{
    public function toArray(Request $request): array{
        $arr = [
            'province'    => $this->relationValidation('province',function(){
                return $this->province->toViewApi()->resolve();
            },$this->prop_province),
            'district'    => $this->relationValidation('district',function(){
                return $this->district->toViewApi()->resolve();
            },$this->prop_district),
            'subdistrict' => $this->relationValidation('subdistrict',function(){
                return $this->subdistrict->toViewApi()->resolve();
            },$this->prop_subdistrict),
            'village'     => $this->relationValidation('village',function(){
                return $this->village->toViewApi()->resolve();
            },$this->prop_village),
        ];
        return $this->mergeArray(parent::toArray($request),$arr);
    }
}
