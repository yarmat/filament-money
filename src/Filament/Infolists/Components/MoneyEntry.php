<?php

namespace Ymsoft\FilamentMoney\Filament\Infolists\Components;

use Cknow\Money\CurrenciesTrait;
use Cknow\Money\Money;
use Filament\Infolists\Components\TextEntry;

class MoneyEntry extends TextEntry
{
    use CurrenciesTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(function (Money $state) {
            $subunitFor = self::getCurrencies()->subunitFor($state->getCurrency());

            if ($subunitFor <= 2) {
                return $state->format();
            }

            return (float) $state->formatByDecimal().' '.$state->getCurrency()->getCode();
        });
    }
}
