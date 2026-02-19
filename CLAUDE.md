# CLAUDE.md - Module Regional

This file provides guidance to Claude Code when working with this module.

## Overview

`hanafalah/module-regional` is a Laravel package for managing Indonesian regional/location data and addresses. It provides models, schemas, and concerns for handling hierarchical location data (Country, Province, District, Subdistrict, Village) and polymorphic addresses.

**Package:** `hanafalah/module-regional`
**Namespace:** `Hanafalah\ModuleRegional`
**Depends on:** `hanafalah/laravel-support`

## CRITICAL: BaseServiceProvider Warning

**The `ModuleRegionalServiceProvider` extends `BaseServiceProvider` from `laravel-support`.**

The current implementation uses wildcard registration which can cause memory issues:

```php
class ModuleRegionalServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerMainClass(ModuleRegional::class)
            ->registerCommandService(Providers\CommandServiceProvider::class)
            ->registers([
                '*',  // WARNING: Wildcard registration
                'Namespace' => function () {
                    $this->publishes([...], 'data');
                }
            ]);
    }
}
```

### Why This Is Important

The `registers(['*'])` method auto-loads multiple services. While the base `laravel-support` has been optimized to only register SAFE methods with `'*'` (Config, Model, Database, Migration, Route, Namespace, Provider), be aware:

1. Do NOT add `'Schema'` or `'Services'` to the registers array without careful consideration
2. Schema classes extend `PackageManagement` which uses `HasModelConfiguration` trait
3. Loading Schema classes can trigger memory exhaustion via config loading chains

### Safe Modifications

When modifying the service provider:
- Do NOT override `boot()` without calling `parent::boot()`
- The `dir()` method must return the correct base path for asset resolution
- Use explicit registration for any new services rather than relying on wildcards

## Directory Structure

```
module-regional/
├── assets/
│   ├── config/
│   │   └── config.php              # Module configuration
│   └── database/
│       └── migrations/
│           ├── data/               # Seed data files
│           │   ├── countries.php
│           │   ├── provinces.php
│           │   └── sql/            # SQL insert files for seeding
│           │       ├── countries.sql
│           │       ├── provinces.sql
│           │       ├── districts.sql
│           │       ├── subdistricts.sql
│           │       └── villages.sql
│           ├── 0001_01_01_000002_create_provinces_table.php
│           ├── 0001_01_01_000003_create_districts_table.php
│           ├── 0001_01_01_000003_create_subdistricts_table.php
│           ├── 0001_01_01_000004_create_villages_table.php
│           ├── 0001_01_01_000005_create_addresses_table.php
│           └── 0001_01_01_000005_create_countries_table.php
├── src/
│   ├── Commands/
│   │   ├── EnvironmentCommand.php
│   │   ├── InstallMakeCommand.php  # php artisan module-regional:install
│   │   └── SeedCommand.php         # php artisan module-regional:seed
│   ├── Concerns/
│   │   ├── HasAddress.php          # Trait for models that have addresses
│   │   ├── HasLocation.php         # Trait for location relationships
│   │   └── LocationHasAddress.php  # Trait for location models
│   ├── Contracts/
│   │   ├── ModuleRegional.php
│   │   ├── Data/
│   │   │   ├── AddressData.php
│   │   │   └── AddressPropsData.php
│   │   └── Schemas/
│   │       ├── Citizenship/
│   │       │   └── Country.php
│   │       └── Regional/
│   │           ├── Address.php
│   │           ├── District.php
│   │           ├── Province.php
│   │           ├── Subdistrict.php
│   │           └── Village.php
│   ├── Data/
│   │   ├── AddressData.php         # DTO for address operations
│   │   └── AddressPropsData.php    # DTO for address properties
│   ├── Database/
│   │   └── Seeders/
│   │       └── DatabaseSeeder.php  # Seeds regional data from SQL files
│   ├── Enums/
│   │   └── Address/
│   │       └── Flag.php            # Address types: KTP, RESIDENCE, OTHER
│   ├── Facades/
│   │   └── ModuleRegional.php
│   ├── Models/
│   │   ├── Citizenship/
│   │   │   └── Country.php
│   │   ├── Maps/
│   │   │   └── ModelHasCoordinate.php
│   │   └── Regional/
│   │       ├── Address.php         # Polymorphic address model
│   │       ├── District.php        # Kabupaten/Kota
│   │       ├── Location.php        # Base class for location models
│   │       ├── Province.php        # Provinsi
│   │       ├── Subdistrict.php     # Kecamatan
│   │       └── Village.php         # Kelurahan/Desa
│   ├── Providers/
│   │   └── CommandServiceProvider.php
│   ├── Resources/
│   │   ├── Address/
│   │   │   ├── ShowAddress.php
│   │   │   └── ViewAddress.php
│   │   ├── Country/
│   │   │   └── ViewCountry.php
│   │   └── Location/
│   │       ├── ShowLocation.php
│   │       └── ViewLocation.php
│   ├── Schemas/
│   │   ├── Citizenship/
│   │   │   └── Country.php
│   │   └── Regional/
│   │       ├── Address.php         # Address business logic
│   │       ├── District.php
│   │       ├── Province.php
│   │       ├── Subdistrict.php
│   │       └── Village.php
│   ├── Supports/
│   │   └── BaseModuleRegional.php
│   ├── ModuleRegional.php
│   └── ModuleRegionalServiceProvider.php
└── composer.json
```

## Key Components

### Location Hierarchy

Indonesian administrative divisions follow this hierarchy:

```
Country (Negara)
└── Province (Provinsi)
    └── District (Kabupaten/Kota)
        └── Subdistrict (Kecamatan)
            └── Village (Kelurahan/Desa)
```

### Models

#### Location (Base Class)
```php
// All location models extend this base class
class Location extends BaseModel
{
    use HasProps, LocationHasAddress;

    // Common fields: id, code, name, latitude, longitude, props
}
```

#### Address (Polymorphic)
```php
class Address extends BaseModel
{
    use HasUlids, HasProps, HasLocation, SoftDeletes;

    // Uses ULID as primary key
    // Polymorphic relationship via model_type and model_id
    // Supports flags: KTP, RESIDENCE, OTHER
}
```

### Concerns (Traits)

#### HasAddress
Use this trait on any model that can have addresses:

```php
use Hanafalah\ModuleRegional\Concerns\HasAddress;

class Patient extends Model
{
    use HasAddress;

    // Provides:
    // - address() - morphOne relationship
    // - addresses() - morphMany relationship
    // - setAddress($flag, $addressData) - create/update address
}
```

#### HasLocation
Use this trait for models that reference location data:

```php
use Hanafalah\ModuleRegional\Concerns\HasLocation;

class SomeModel extends Model
{
    use HasLocation;

    // Provides relationships:
    // - province()
    // - district()
    // - subdistrict()
    // - village()
}
```

### Address Flags

```php
use Hanafalah\ModuleRegional\Enums\Address\Flag;

Flag::KTP;       // Identity card address (alamat KTP)
Flag::RESIDENCE; // Current residence address (alamat domisili)
Flag::OTHER;     // Other address type
```

### Data Transfer Objects

#### AddressData
```php
use Hanafalah\ModuleRegional\Data\AddressData;

$addressData = AddressData::from([
    'name' => 'Jl. Sudirman No. 123',
    'flag' => Flag::RESIDENCE->value,
    'province_id' => 31,
    'district_id' => 3171,
    'subdistrict_id' => 317101,
    'village_id' => 31710101,
    'props' => [
        'zip_code' => '10110',
        'rt' => '001',
        'rw' => '002'
    ]
]);
```

## Artisan Commands

```bash
# Install the module (publish config and migrations)
php artisan module-regional:install

# Seed regional data (countries, provinces, districts, subdistricts, villages)
php artisan module-regional:seed

# Seed specific seeder class
php artisan module-regional:seed SomeSeeder
```

## Database Schema

### addresses table
| Column | Type | Description |
|--------|------|-------------|
| id | ULID | Primary key |
| name | text | Full address text |
| model_type | string(50) | Polymorphic type |
| model_id | string(36) | Polymorphic ID |
| flag | string(50) | Address flag (KTP/RESIDENCE/OTHER) |
| province_id | FK | Reference to provinces |
| district_id | FK | Reference to districts |
| subdistrict_id | FK | Reference to subdistricts |
| village_id | FK | Reference to villages |
| latitude | string(50) | GPS latitude |
| longitude | string(50) | GPS longitude |
| props | JSON | Additional properties |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

### Location tables (provinces, districts, subdistricts, villages)
| Column | Type | Description |
|--------|------|-------------|
| id | ULID | Primary key |
| code | string(100) | Regional code |
| name | string(100) | Location name |
| latitude | string(50) | GPS latitude |
| longitude | string(50) | GPS longitude |
| props | JSON | Additional properties |

## Usage Examples

### Creating an Address for a Patient

```php
use Hanafalah\ModuleRegional\Enums\Address\Flag;

$patient = Patient::find($id);
$patient->setAddress(Flag::RESIDENCE->value, [
    'name' => 'Jl. Gatot Subroto No. 45, RT 03/RW 05',
    'province_id' => 31,        // DKI Jakarta
    'district_id' => 3171,      // Jakarta Pusat
    'subdistrict_id' => 317101, // Gambir
    'village_id' => 31710101,   // Cideng
    'props' => [
        'zip_code' => '10150',
        'rt' => '03',
        'rw' => '05'
    ]
]);
```

### Querying Locations

```php
// Get all provinces
$provinces = Province::all();

// Get districts in a province
$districts = District::where('province_id', $provinceId)->get();

// Get full location hierarchy for an address
$address = Address::with(['province', 'district', 'subdistrict', 'village'])->find($id);
```

### Using Address Props for Display

```php
// Address stores denormalized location data in props for faster access
$address = Address::find($id);

// Access via props (faster, no additional queries)
$provinceName = $address->props->prop_province['name'];
$districtName = $address->props->prop_district['name'];
$zipCode = $address->props->zip_code;
```

## Configuration

The module config is published to `config/module-regional.php`:

```php
return [
    'namespace' => 'Hanafalah\\ModuleRegional',
    'app' => [
        'contracts' => [
            // Contract => Implementation mappings
        ],
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations'
    ],
    'database' => [
        'models' => [
            // Model mappings
        ]
    ],
    'commands' => [
        Commands\InstallMakeCommand::class,
        Commands\SeedCommand::class
    ]
];
```

## Integration with Other Modules

This module is commonly used by:
- `module-patient` - Patient addresses
- `module-organization` - Organization/clinic addresses
- `module-user` - User addresses
- `ms-emr` - Medical record location data

## Common Patterns

### Overriding Models

To use custom models, update `config/database.php`:

```php
'models' => [
    'Province' => App\Models\Province::class,
    'District' => App\Models\District::class,
    'Address' => App\Models\Address::class,
    // ...
]
```

### Extending Address Schema

```php
use Hanafalah\ModuleRegional\Schemas\Regional\Address as BaseAddress;

class Address extends BaseAddress
{
    public function prepareStoreAddress(AddressData $dto): Model
    {
        // Custom logic before storing
        $address = parent::prepareStoreAddress($dto);
        // Custom logic after storing
        return $address;
    }
}
```

## Testing

When testing with this module:

```php
// Seed test data
$this->artisan('module-regional:seed');

// Or create test data manually
Province::factory()->create(['name' => 'Test Province']);
```

## Troubleshooting

### "Province/District/etc not found" error
Ensure regional data is seeded:
```bash
php artisan module-regional:seed
```

### Address not saving location props
Check that the village/subdistrict exists in the database and the ID is correct. The `AddressData::after()` method auto-populates province and district IDs from the village/subdistrict.

### Memory issues during boot
See the BaseServiceProvider warning above. Do not add Schema to wildcard registration.
