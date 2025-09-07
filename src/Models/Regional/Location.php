<?php

namespace Hanafalah\ModuleRegional\Models\Regional;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleRegional\Concerns\LocationHasAddress;
use Hanafalah\ModuleRegional\Resources\Location\{ViewLocation, ShowLocation};

class Location extends BaseModel
{
    use HasProps, LocationHasAddress;

    public $timestamps  = false;

    protected $casts = [
        // 'name' => 'string',
        'code' => 'string',
    ];

    public function getViewResource(){
        return ViewLocation::class;
    }

    public function getShowResource(){
        return ShowLocation::class;
    }

    public function showUsingRelation(){
        return $this->viewUsingRelation();
    }

    public function getLocation(?string $type = null,?string $name = null){
        $type ??= $this->getMorphClass();
        switch ($type) {
            case 'Province':
                return $name ?? $this->name;
            break;
            case 'District':
                return 'Prov. '.$this->getLocation('Province',$this->province?->name).', '.$name ?? $this->name;
            break;
            case 'Subdistrict':
                return implode(', ',[
                    'Prov. '.$this->getLocation('Province',$this->province?->name),
                    'Kab. '.$this->getLocation('District',$this->district?->name),
                    $name ?? $this->name
                ]);
            break;
            case 'Village':
                return implode(', ',[
                    'Prov. '.$this->getLocation('Province',$this->province?->name),
                    'Kab. '.$this->getLocation('District',$this->district?->name),
                    'Kec. '.$this->getLocation('Subdistrict',$this->subdistrict?->name),
                    $name ?? $this->name
                ]);
            break;
        }
    }
}
