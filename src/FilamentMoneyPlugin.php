<?php

namespace Ymsoft\FilamentMoney;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Webmozart\Assert\Assert;

class FilamentMoneyPlugin implements Plugin
{
    public const ID = 'filament-money';

    protected string $defaultCurrency = 'USD';

    protected array $availableCurrencies = ['USD'];

    protected ?Closure $inputModifier = null;

    protected ?Closure $selectModifier = null;

    protected string $currencyPosition = 'left';

    public function getId(): string
    {
        return self::ID;
    }

    public function register(Panel $panel): void
    {

        // TODO: Implement register() method.
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public function availableCurrencies(array $currencies): static
    {
        $this->availableCurrencies = $currencies;

        return $this;
    }

    public function getAvailableCurrencies(): array
    {
        return $this->availableCurrencies;
    }

    public function defaultCurrency(string $defaultCurrency): static
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    /**
     * Create a new plugin instance.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    public function input(Closure $input): static
    {
        $this->inputModifier = $input;

        return $this;
    }

    public function select(Closure $select): static
    {
        $this->selectModifier = $select;

        return $this;
    }

    public function getInputModifier(): ?Closure
    {
        return $this->inputModifier;
    }

    public function getSelectModifier(): ?Closure
    {
        return $this->selectModifier;
    }

    public function getCurrencyPosition(): string
    {
        return $this->currencyPosition;
    }

    public function currencyPosition(string $currencyPosition): static
    {
        Assert::inArray($currencyPosition, ['left', 'right']);

        $this->currencyPosition = $currencyPosition;

        return $this;
    }
}
