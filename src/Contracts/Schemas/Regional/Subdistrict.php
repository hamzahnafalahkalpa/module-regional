<?php

namespace Hanafalah\ModuleRegional\Contracts\Schemas\Regional;

use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\LaravelSupport\Data\PaginateData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface Subdistrict extends DataManagement{
    public function subdistrict(mixed $conditionals = null): Builder;
    public function prepareViewSubdistrictPaginate(PaginateData $paginate_dto): LengthAwarePaginator;
    public function viewSubdistrictPaginate(?PaginateData $paginate_dto = null);
}