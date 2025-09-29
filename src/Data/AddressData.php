<?php

namespace Hanafalah\ModuleRegional\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleRegional\Contracts\Data\AddressData as DataAddressData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Hanafalah\ModuleRegional\Enums\Address\Flag;

class AddressData extends Data implements DataAddressData{
    #[MapName('id')]
    #[MapInputName('id')]
    public mixed $id = null;

    #[MapName('name')]
    #[MapInputName('name')]
    public string $name;

    #[MapName('model_type')]
    #[MapInputName('model_type')]
    public ?string $model_type = null;

    #[MapName('model_id')]
    #[MapInputName('model_id')]
    public mixed $model_id = null;

    #[MapName('flag')]
    #[MapInputName('flag')]
    public ?string $flag = Flag::OTHER->value;

    #[MapName('province_id')]
    #[MapInputName('province_id')]
    public ?int $province_id = null;

    #[MapName('district_id')]
    #[MapInputName('district_id')]
    public ?int $district_id = null;

    #[MapName('subdistrict_id')]
    #[MapInputName('subdistrict_id')]
    public ?int $subdistrict_id = null;

    #[MapName('village_id')]
    #[MapInputName('village_id')]
    public ?int $village_id = null;

    #[MapName('village_model')]
    #[MapInputName('village_model')]
    public ?object $village_model = null;

    #[MapName('props')]
    #[MapInputName('props')]
    public ?AddressPropsData $props = null;

    public static function after(self $data): self{
        $new = self::new();
        $props = &$data->props->props;

        if (isset($data->village_id)){
            $data->village_model = $village_model = $new->VillageModel()->findOrFail($data->village_id);
            $data->province_id ??= $village_model->province_id;
            $data->district_id ??= $village_model->district_id;
            $data->subdistrict_id ??= $village_model->subdistrict_id;
            $props['prop_village'] = $village_model->toViewApi()->resolve();
        }
        return $data;
    }
}