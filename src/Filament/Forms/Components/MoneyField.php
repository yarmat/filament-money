<?php

namespace Ymsoft\FilamentMoney\Filament\Forms\Components;

use Cknow\Money\CurrenciesTrait;
use Cknow\Money\Money;
use Closure;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
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
        $field->formatStateUsing(function ($state, $component): ?array {
            // Ensure we always return an array or null as declared
            if (! is_array($state)) {
                return null;
            }

            $amountKey = $this->getAmountKey();
            $currencyKey = $this->getCurrencyKey();

            // Resolve available currencies from component (if changeable) or defaults
            $availableCurrencies = $this->defaultAvailableCurrencies;
            if ($component instanceof MoneyChangeableCurrency) {
                $availableCurrencies = $component->getCurrencies();
            }

            // Normalize currency if a set of allowed currencies is provided
            if (! empty($availableCurrencies)) {
                $currentCurrency = $state[$currencyKey] ?? null;
                if ($currentCurrency === null || ! in_array($currentCurrency, $availableCurrencies, true)) {
                    $state[$currencyKey] = $availableCurrencies[0];
                }
            }

            // Format amount using DecimalMoneyFormatter when amount is present
            if (array_key_exists($amountKey, $state)) {
                $currencyCode = $state[$currencyKey] ?? null;
                if ($currencyCode !== null) {
                    $formatter = new DecimalMoneyFormatter(static::getCurrencies());
                    $state[$amountKey] = $formatter->format(
                        new \Money\Money($state[$amountKey], static::parseCurrency($currencyCode))
                    );
                }
            }

            return $state;
        });

        $field->dehydrateStateUsing(function ($state, Get $get): ?Money {
            $amountKey = $this->getAmountKey();
            $currencyKey = $this->getCurrencyKey();
            $currency = $state[$currencyKey] ?? $get($this->getCurrencyKey()) ?? $this->defaultCurrency;

            if (is_array($state) && array_key_exists($amountKey, $state)) {
                return new Money($state[$amountKey], $currency, true);
            }

            return null;
        });

        $field->default(new Money(currency: $this->defaultCurrency));
    }
}
