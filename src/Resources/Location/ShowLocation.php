<?php

namespace Hanafalah\ModuleRegional\Resources\Location;

use Illuminate\Http\Request;

class ShowLocation extends ViewLocation
{
    public function toArray(Request $request): array{
        $arr = [
        ];
        return $this->mergeArray(parent::toArray($request),$arr);
    }
}
