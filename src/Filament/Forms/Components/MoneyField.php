<?php

namespace Ymsoft\FilamentMoney\Filament\Forms\Components;

use Cknow\Money\CurrenciesTrait;
use Cknow\Money\Money;
use Closure;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;
use Money\Formatter\DecimalMoneyFormatter;
use Ymsoft\FilamentMoney\FilamentMoneyPlugin;

class MoneyField
{
    use CurrenciesTrait;

    public function __construct(
        private readonly string $name,
        private readonly string $amountKey,
        private readonly string $currencyKey,
        private readonly string $defaultCurrency,
        private readonly array $defaultAvailableCurrencies,
        private readonly string $currencyPosition,
        private readonly ?Closure $inputModifier,
        private readonly ?Closure $selectModifier,
    ) {}

    public static function make(
        string $name, string $amountKey = 'amount', string $currencyKey = 'currency'
    ): MoneyChangeableCurrency {
        return self::_make($name, $amountKey, $currencyKey)->makeChangeable();
    }

    public static function fixed(
        string $name, string $amountKey = 'amount', string $currencyKey = 'currency'
    ): MoneyFixedCurrency {
        return self::_make($name, $amountKey, $currencyKey)->makeFixed();
    }

    private static function _make(string $name, string $amountKey, string $currencyKey): static
    {
        $defaultCurrency = config('money.defaultCurrency');
        $defaultAvailableCurrencies = [config('money.defaultCurrency')];
        $inputModifier = null;
        $selectModifier = null;
        $currencyPosition = 'left';

        if (Filament::getCurrentPanel()?->hasPlugin(FilamentMoneyPlugin::ID)) {
            /** @var FilamentMoneyPlugin $plugin */
            $plugin = Filament::getCurrentPanel()->getPlugin(FilamentMoneyPlugin::ID);

            $defaultCurrency = $plugin->getDefaultCurrency();
            $defaultAvailableCurrencies = $plugin->getAvailableCurrencies();
            $inputModifier = $plugin->getInputModifier();
            $selectModifier = $plugin->getSelectModifier();
            $currencyPosition = $plugin->getCurrencyPosition();
        }

        return app(static::class, compact('name', 'amountKey', 'currencyKey', 'defaultCurrency', 'defaultAvailableCurrencies', 'inputModifier', 'selectModifier', 'selectModifier', 'currencyPosition'));
    }

    private function makeChangeable(): MoneyChangeableCurrency
    {
        $field = MoneyChangeableCurrency::_make(
            name: $this->name,
            currencies: $this->defaultAvailableCurrencies,
            amountKey: $this->getAmountKey(),
            currencyKey: $this->getCurrencyKey(),
        );

        $this->setupField($field);

        if ($this->inputModifier) {
            $field->input($this->inputModifier);
        }

        if ($this->selectModifier) {
            $field->select($this->selectModifier);
        }

        $field->currencyPosition($this->currencyPosition);

        return $field;
    }

    private function makeFixed(): MoneyFixedCurrency
    {
        $field = MoneyFixedCurrency::_make(
            name: $this->name,
            amountKey: $this->getAmountKey(),
            currencyKey: $this->getCurrencyKey(),
        );

        $this->setupField($field);

        if ($this->inputModifier) {
            $field->input($this->inputModifier);
        }

        $field->currencyPosition($this->currencyPosition);

        return $field;
    }

    public function getCurrencyKey(): string
    {
        return $this->currencyKey;
    }

    public function getAmountKey(): string
    {
        return $this->amountKey;
    }

    private function setupField(Component $field): void
    {
        $field->formatStateUsing(function ($state): ?array {
            $amountKey = $this->getAmountKey();
            $currencyKey = $this->getCurrencyKey();

            if (is_array($state) && isset($state[$amountKey])) {
                $formatter = new DecimalMoneyFormatter(static::getCurrencies());
                $result = $formatter->format(new \Money\Money($state[$amountKey], static::parseCurrency($state[$currencyKey])));

                $state[$amountKey] = $result;
            }

            return $state;
        });

        $field->dehydrateStateUsing(function ($state): ?Money {
            $amountKey = $this->getAmountKey();
            $currencyKey = $this->getCurrencyKey();

            if (is_array($state) && array_key_exists($amountKey, $state) && array_key_exists($currencyKey, $state)) {
                return new Money($state[$amountKey], $state[$currencyKey], true);
            }

            return null;
        });

        $field->default(new Money(null, $this->defaultCurrency));
    }
}
