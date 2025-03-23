<?php

namespace Hanafalah\ModuleRegional\Models\Regional;

class District extends Location
{
    protected $fillable   = ['province_id'];

    public function district(){return $this->hasOneModel('District');}
    public function districts(){return $this->hasManyModel('District');}
}
