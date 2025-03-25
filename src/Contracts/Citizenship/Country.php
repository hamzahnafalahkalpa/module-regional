<?php

namespace Hanafalah\ModuleRegional\Contracts\Citizenship;

use Hanafalah\LaravelSupport\Contracts\DataManagement;
use Hanafalah\LaravelSupport\Data\PaginateData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface Country extends DataManagement{
    public function country(mixed $conditionals = null): Builder;
    public function prepareViewCountryPaginate(PaginateData $paginate_dto): LengthAwarePaginator;
    public function viewCountryPaginate(?PaginateData $paginate_dto = null);
}