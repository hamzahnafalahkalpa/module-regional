<?php

namespace Hanafalah\ModuleRegional\Schemas\Regional;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleRegional\Contracts\Schemas\Regional\Address as RegionalAddress;
use Hanafalah\ModuleRegional\Data\AddressData;
use Hanafalah\ModuleRegional\Enums\Address\Flag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Address extends PackageManagement implements RegionalAddress{
    protected string $__entity = 'Address';
    public $address_model;

    public function prepareStoreAddress(AddressData $address_dto): Model{
        $guard = isset($address_dto->id) 
                    ? ['id' => $address_dto->id] 
                    : [
                        'model_type' => $address_dto->model_type,
                        'model_id'   => $address_dto->model_id,
                        'flag'       => $address_dto->flag ?? Flag::OTHER->value
                    ];
        $address = $this->usingEntity()->updateOrCreate($guard,[
            'name'           => $address_dto->name,
            'province_id'    => $address_dto->province_id,
            'district_id'    => $address_dto->district_id,
            'subdistrict_id' => $address_dto->subdistrict_id,
            'village_id'     => $address_dto->village_id
        ]);

        if (isset($address_dto->props)){
            foreach ($address_dto->props as $key => $value) $address->{$key} = $value ?? null;
        }
        $address->save();

        $this->setRegional($address, 'province', $address_dto->province_id)
             ->setRegional($address, 'district', $address_dto->district_id)
             ->setRegional($address, 'subdistrict', $address_dto->subdistrict_id)
             ->setRegional($address, 'village', $address_dto->village_id);
        
        return $this->address_model = $address;
    }

    private function setRegional(Model &$address, string $type, ?int $id = null): self{
        if (isset($id)){
            $model = Str::ucfirst($type);
            $model = $this->{$model.'Model'}()->findOrFail($id);
            $address->sync($model,['id','name','code']);
        }else{
            $address->{'prop_'.$type} = [
                'id'   => null,
                'name' => null,
                'code' => null
            ];
            $address->save();
        }
        return $this;
    }
}
