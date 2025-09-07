<?php

namespace Hanafalah\ModuleRegional\Models\Regional;

class Subdistrict extends Location
{
    protected $fillable   = [
        'province_id','district_id'
    ];

    public function viewUsingRelation(){
        return [
            'province','district'
        ];
    }

    public function village(){return $this->hasOneModel('Village');}
    public function villages(){return $this->hasManyModel('Village');}
    public function district(){return $this->belongsToModel('District');}
    public function province(){return $this->belongsToModel('Province');}
}
