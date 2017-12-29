<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('build:dry {model}', function ($model) {
    $builder = new App\Builder\Builder;
    $res = $builder->prepareClassBase($model);
    echo(implode('', $res));
})->describe('Build a component');

Artisan::command('build {filename} {target}', function ($filename, $target) {
    $builder = new App\Builder\Builder;
    $builder->build($filename, $target);
})->describe('Build a component');
