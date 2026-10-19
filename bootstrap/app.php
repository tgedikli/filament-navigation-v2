<?php

use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application;
use RyanChandler\FilamentNavigation\FilamentNavigationServiceProvider;

$basePathLocator = new class
{
    use CreatesApplication;
};

$app = (new Application($basePathLocator::applicationBasePath()))
    ->configure([
        'enables_package_discoveries' => true,
    ])
    ->createApplication();

$app->register(LivewireServiceProvider::class);
$app->register(FormsServiceProvider::class);
$app->register(TablesServiceProvider::class);
$app->register(FilamentServiceProvider::class);
$app->register(SchemasServiceProvider::class);
$app->register(InfolistsServiceProvider::class);
$app->register(FilamentNavigationServiceProvider::class);

return $app;
