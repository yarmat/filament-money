<?php

namespace Ymsoft\FilamentMoney\Filament\Tables\Columns;

use Cknow\Money\CurrenciesTrait;
use Cknow\Money\Money;
use Filament\Tables\Columns\TextColumn;

class MoneyColumn extends TextColumn
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
