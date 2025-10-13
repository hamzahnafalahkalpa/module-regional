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

    #[MapName('village')]
    #[MapInputName('village')]
    public ?array $village = null;

    #[MapName('subdistrict')]
    #[MapInputName('subdistrict')]
    public ?array $subdistrict = null;

    #[MapName('village_model')]
    #[MapInputName('village_model')]
    public ?object $village_model = null;

    #[MapName('subdistrict_model')]
    #[MapInputName('subdistrict_model')]
    public ?object $subdistrict_model = null;

    #[MapName('props')]
    #[MapInputName('props')]
    public ?AddressPropsData $props = null;

    public static function after(self $data): self{
        $new = self::new();
        $props = &$data->props->props;

        $data->flag ??= Flag::OTHER->value;

        if (isset($data->village)) $data->village_id = $data->village['id'] ?? $data->village['village_id'] ?? null;
        if (isset($data->subdistrict)) $data->subdistrict_id = $data->subdistrict['id'] ?? $data->subdistrict['subdistrict_id'] ?? null;

        if (isset($data->subdistrict_id)){
            $data->subdistrict_model = $subdistrict_model = $new->SubdistrictModel()->findOrFail($data->subdistrict_id);
            $data->province_id ??= $subdistrict_model->province_id;
            $data->district_id ??= $subdistrict_model->district_id;
            $data->subdistrict_id ??= $subdistrict_model->subdistrict_id;
            $props['prop_subdistrict'] = $subdistrict_model->toViewApi()->resolve();
        }

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