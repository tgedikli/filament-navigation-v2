<?php

use Livewire\Livewire;
use Pest\Expectation;
use RyanChandler\FilamentNavigation\Filament\Resources\NavigationResource\Pages\CreateNavigation;
use RyanChandler\FilamentNavigation\Filament\Resources\NavigationResource\Pages\EditNavigation;
use RyanChandler\FilamentNavigation\Models\Navigation;

use function Pest\Laravel\assertDatabaseHas;

it('can create a navigation menu', function () {
    Livewire::test(CreateNavigation::class)
        ->set('data.name', 'Foo')
        ->call('create')
        ->assertHasNoErrors();

    assertDatabaseHas(Navigation::class, [
        'name' => 'Foo',
        'handle' => 'foo',
    ]);
});

it('can create a navigation menu with items', function () {
    Livewire::test(CreateNavigation::class)
        ->set('data.name', 'Foo')
        ->call('mountAction', 'item')
        ->setActionData([
            'label' => 'Bar',
            'type' => 'external-link',
            'data' => [
                'url' => '/bar',
            ],
        ])
        ->call('callMountedAction')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSuccessful();

    expect(Navigation::first())
        ->toBeInstanceOf(Navigation::class)
        ->name->toBe('Foo')
        ->handle->toBe('foo')
        ->items
        ->toHaveLength(1)
        ->sequence(
            fn (Expectation $item) => $item
                ->toHaveKey('label', 'Bar')
                ->toHaveKey('type', 'external-link')
                ->data->toMatchArray([
                    'url' => '/bar',
                ])
        );
});

it('menu ogeleri duzenlenir siralanir ve alt ogeler korunur', function () {
    $component = Livewire::test(CreateNavigation::class)->set('data.name', 'Urunler')
        ->callAction('item', data: ['label' => 'Telefon', 'type' => 'external-link', 'data' => ['url' => '/telefon']])
        ->callAction('item', data: ['label' => 'Bilgisayar', 'type' => 'external-link', 'data' => ['url' => '/bilgisayar']]);
    $keys = array_keys($component->get('data.items'));
    $component->call('addChild', 'data.items.'.$keys[0])
        ->setActionData(['label' => 'Apple', 'type' => 'external-link', 'data' => ['url' => '/apple']])
        ->callMountedAction()->assertHasNoActionErrors()
        ->call('editItem', 'data.items.'.$keys[0])
        ->setActionData(['label' => 'Telefonlar'])
        ->callMountedAction()->assertHasNoActionErrors()
        ->call('sortNavigation', 'data.items', ['data.items.'.$keys[1], 'data.items.'.$keys[0]])
        ->call('create')->assertHasNoErrors();
    $items = array_values(Navigation::firstOrFail()->items);
    expect($items[0]['label'])->toBe('Bilgisayar')
        ->and($items[1]['label'])->toBe('Telefonlar')
        ->and(array_values($items[1]['children'])[0]['data']['url'])->toBe('/apple');
});

it('menu turu degisince yeni tur alanlari yuklenir', function () {
    Livewire::test(CreateNavigation::class)->call('createItem')
        ->set('mountedActions.0.data.type', 'external-link')
        ->assertSuccessful()->assertFormFieldExists('data.url', form: 'mountedActionSchema0');
});

it('kayitli menu tarih alanlariyla acilir ve handle degismeden kaydedilir', function () {
    $menu = Navigation::create(['name' => 'Alt menu', 'handle' => 'footer', 'items' => []]);
    Livewire::test(EditNavigation::class, ['record' => $menu->id])
        ->assertSuccessful()->fillForm(['name' => 'Yeni alt menu'])->call('save')->assertHasNoFormErrors();
    expect($menu->fresh()->name)->toBe('Yeni alt menu')->and($menu->fresh()->handle)->toBe('footer');
});
