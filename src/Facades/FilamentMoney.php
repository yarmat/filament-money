<?php

namespace Ymsoft\FilamentMoney\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ymsoft\FilamentMoney\FilamentMoney
 */
class FilamentMoney extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ymsoft\FilamentMoney\FilamentMoney::class;
    }
}
