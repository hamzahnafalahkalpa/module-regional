<?php

namespace Hanafalah\ModuleRegional\Models\Regional;

class District extends Location
{
    protected $fillable   = ['province_id','type'];


    public function viewUsingRelation(){
        return [
            'province'
        ];
    }

    public function province(){return $this->belongsToModel('Province');}
    public function subdistricts(){return $this->hasManyModel('Subdistrict');}
}
