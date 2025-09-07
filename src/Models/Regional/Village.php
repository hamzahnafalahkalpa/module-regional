<?php

namespace Hanafalah\ModuleRegional\Models\Regional;

class Village extends Location
{
    protected $fillable = [
        'province_id', 'district_id', 'subdistrict_id', 'post_code'
    ];

    protected $casts = [
        // 'name' => 'string',
        'code' => 'string',
        'post_code' => 'string'
    ];

    public function viewUsingRelation(){
        return [
            'province','district','subdistrict'
        ];
    }

    public function showUsingRelation(){
        return $this->viewUsingRelation();
    }

    public function province(){return $this->belongsToModel('Province');}
    public function district(){return $this->belongsToModel('District');}
    public function subdistrict(){return $this->belongsToModel('Subdistrict');}
}
