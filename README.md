# Build structured navigation menus in Filament.

> [!NOTE]
> Bu repo, [ryangjchandler/filament-navigation](https://github.com/ryangjchandler/filament-navigation) paketinin [tgedikli/filament-navigation-v2](https://github.com/tgedikli/filament-navigation-v2) fork’udur. Upstream artık bakımda değil; projelerimiz için gerekli düzeltmeler burada tutulur.

This plugin for Filament provides a `Navigation` resource that lets you build structural navigation menus with a clean drag-and-drop UI.

## Fork farkları (upstream’e göre)

Upstream, Filament panel henüz boot olmamışken (`Filament::getCurrentPanel() === null`) `FilamentNavigation::get()` çağırarak model/resource çözümlemeye çalışıyor. Bu durum panel boot / provider kayıt aşamasında hata üretebiliyor.

Bu fork’ta şu sınıflara null-panel fallback eklendi:

- `NavigationResource::getModel()`
- `CreateNavigation::getResource()`
- `EditNavigation::getResource()`
- `ListNavigations::getResource()`

Panel yoksa sırasıyla `Navigation::class` / `NavigationResource::class` döner; panel varsa mevcut plugin API’si (`FilamentNavigation::get()`) aynen kullanılır.

## Sürüm seçimi ve uyumluluk

Bu fork'un Composer paket adı **`ryangjchandler/filament-navigation`** olarak kalır. Paket ana sürümü Filament ana sürümüyle aynı değildir; Filament 4 için bu fork'un `^2.0` hattı seçilir.

| Filament | Laravel | En düşük PHP | Paket sürümü | Composer komutu |
| --- | --- | --- | --- | --- |
| 3.x | 10.x | 8.1 | `^1.0` | `composer require ryangjchandler/filament-navigation:"^1.0"` |
| 3.x | 11.x | 8.2 | `^1.0` | `composer require ryangjchandler/filament-navigation:"^1.0"` |
| 4.x | 11.28+ (11.x) | 8.3 | `^2.0` | `composer require ryangjchandler/filament-navigation:"^2.0"` |

- `main`, Filament 3 için **v1.0.0 / 1.x** hattıdır; Laravel 10 ve 11 bağımlılıklarını kabul eder.
- `2.x` bakım dalı, Filament 4 için **v2.0.0 ve sonrası** sürümleri içerir. `illuminate/contracts:^11.28` ve PHP `^8.3` gerektirir; Laravel 12 ve üzerini kapsamaz.
- Bu fork'un Filament 2 için yayımlanmış bir sürümü yoktur. Filament 4 hattı PHP 8.3 / Laravel 11 altında test edilmiştir; önceki hat geriye uyumluluk için korunur.

PHP alt sınırları paket ve framework gereksinimlerini birlikte gösterir; [Filament 3 gereksinimleri](https://filamentphp.com/docs/3.x/panels/installation) ve [Laravel sürüm tablosu](https://laravel.com/docs/11.x/releases#support-policy) de geçerlidir.

## Installation (bu fork)

Paket adı Packagist ile aynı kalır; Composer'ın bu fork'tan indirmesi için **require komutundan önce** VCS repository tanımı gerekir. Uygulamanızda henüz yoksa ekleyin:

```sh
composer config repositories.filament-navigation vcs https://github.com/tgedikli/filament-navigation-v2
```

Ardından tablodaki uygun komutlardan **yalnız birini** kullanın. Filament 3 + Laravel 10/11 için:

```sh
composer require ryangjchandler/filament-navigation:"^1.0"
```

Filament 4 + Laravel 11.28+ / PHP 8.3 için bunun yerine:

```sh
composer require ryangjchandler/filament-navigation:"^2.0"
```

Filament 4 kurulumunda oluşacak `composer.json` bölümü:

```json
{
    "require": {
        "ryangjchandler/filament-navigation": "^2.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/tgedikli/filament-navigation-v2"
        }
    ]
}
```

Filament 3 projesinde aynı VCS kaydıyla `require` aralığını `^1.0` seçin. `@dev` veya `dev-main` yerine etiketli sürümleri kullanın; `^1.0` ve `^2.0` kendi ana sürüm hatlarında kalır.

> VCS kaydı eklenmezse Composer aynı ada sahip upstream paketini çözümleyebilir. Filament ana sürüm geçişinde uygulama ve diğer eklentiler de uyarlanmalıdır; yalnız bu paketin sürümünü değiştirmek yeterli değildir.

Run migrations.

```sh
php artisan migrate
```

Publish the package's assets:

```sh
php artisan filament:assets
```

## Usage

You first need to register the plugin with Filament. This can be done inside of your `PanelProvider`, e.g. `AdminPanelProvider`.

```php
use RyanChandler\FilamentNavigation\FilamentNavigation;

return $panel
    ->plugin(FilamentNavigation::make());
```

If you wish to customise the navigation group, sort or icon, you can use the `NavigationResource::navigationGroup()`, `NavigationResource::navigationSort()` and `NavigationResource::navigationIcon()` methods.

### Data structure

Each navigation menu is required to have a `name` and `handle`. The `handle` should be unique and used to retrieve the menu.

Items are stored inside of a JSON column called `items`. This is a recursive data structure that looks like:

```json
[
    {
        "label": "Home",
        "type": "external-link",
        "data": {
            "url": "/",
            "target": "_blank",
        },
        "children": [
            // ...
        ],
    }
]
```

The recursive structure makes it really simple to render nested menus / dropdowns:

```blade
<ul>
    @foreach($menu->items as $item)
        <li>
            {{ $item['label'] }}

            @if($item['children'])
                <ul>
                    {{-- Render the item's children here... --}}
                </ul>
            @endif
        </li>
    @endforeach
</ul>
```

### Retrieving a navigation object

To retrieve a navigation object, provide the handle to the `RyanChandler\FilamentNavigation\Models\Navigation::fromHandle()` method.

```php
use RyanChandler\FilamentNavigation\Models\Navigation;

$menu = Navigation::fromHandle('main-menu');
```

### Custom item types

Out of the box, this plugin comes with a single "item type" called "External link". This item type expects a URL to be provided and an optional "target" (same tab or new tab).

It's possible to extend the plugin with custom item types. Custom item types have a name and an array of Filament field objects (or a `Closure` that produces an array) that will be displayed inside of the "Item" modal.

This API allows you to deeply integrate navigation menus with your application's own entities and models.

```php
return $panel
    ->plugin(
        FilamentNavigation::make()
            ->itemType('post', [
                Select::make('post_id')
                    ->//...
            ])
    );
```

All custom fields for the item type can be found inside of the `data` property on the item.

### Global custom fields

There might be cases where you want all item types to have an additional set of fields. This is useful for classes, custom IDs and more.

To register global custom fields, use the `withExtraFields()` method on the plugin object.

```php
return $panel
    ->plugin(
        FilamentNavigation::make()
            ->withExtraFields([
                TextInput::make('classes'),
            ]),
    );
```

### The `Navigation` field type

This plugin also provides a custom Filament field that can be used to search and select a navigation menu inside other forms and resources.

```php
use RyanChandler\FilamentNavigation\Filament\Fields\NavigationSelect;

->schema([
    NavigationSelect::make('navigation_id'),
])
```

By default, this field will not be searchable and the value for each option will be the menu `id`.

To make the field searchable, call the `->searchable()` method.

```php
->schema([
    NavigationSelect::make('navigation_id')
        ->searchable(),
])
```

If you wish to change the value for each option, call the `->optionValue()` method.

```php
->schema([
    NavigationSelect::make('navigation_id')
        ->optionValue('handle'),
])
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ryan Chandler](https://github.com/ryangjchandler)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
