<?php

namespace Hanafalah\ModuleRegional\Concerns;

use Hanafalah\ModuleRegional\Data\AddressData;

trait HasAddress
{
  public function setAddress($flag, array|object $address){
    if (isset($flag)) {
        if (is_object($address) && !$address instanceof AddressData) {
          throw new \Exception('address must be an instance of AddressData');
        }elseif (is_array($address)){
          unset($address['village_model']);
          unset($address['subdistrict_model']);
          $address = AddressData::from($this->mergeArray($address,[
            'flag'       => $flag, 
            'model_id'   => $this->getKey(),
            'model_type' => $this->getMorphClass()
          ]));
        }

        $address = app(config('app.contracts.Address'))
                  ->prepareStoreAddress(
                    app(config('app.contracts.AddressData'))::from($address)
                    // AddressData::from($address)
                  );
        return $address;
    } else {
        return null;
    }
  }

  public function address(){return $this->morphOneModel('Address','model');}
  public function addresses(){return $this->morphManyModel('Address','model');}
}
