<?php

namespace Hanafalah\ModuleRegional\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleRegional\Contracts\Data\AddressPropsData as DataAddressPropsData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class AddressPropsData extends Data implements DataAddressPropsData{
    public function __construct(
        #[MapName('zip_code')]
        #[MapInputName('zip_code')]
        public ?string $zip_code = null, 

        #[MapName('rt')]
        #[MapInputName('rt')]
        public ?string $rt = null, 

        #[MapName('rw')]
        #[MapInputName('rw')]
        public ?string $rw = null, 
    ){}
}