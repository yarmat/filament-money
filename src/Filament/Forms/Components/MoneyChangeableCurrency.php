<?php

namespace Ymsoft\FilamentMoney\Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;
use Ymsoft\FilamentMoney\Traits\HasCurrencyPosition;
use Ymsoft\FilamentMoney\Traits\HasDefaultState;
use Ymsoft\FilamentMoney\Traits\HasInput;
use Ymsoft\FilamentMoney\Traits\HasSelect;

class MoneyChangeableCurrency extends FusedGroup
{
    use HasCurrencyPosition {
        currencyPosition as baseCurrencyPosition;
    }
    use HasDefaultState;
    use HasInput;
    use HasSelect;

    protected string|Closure $selectWidth = '80px';

    protected array|Closure $currencies;

    protected string $amountKey;

    protected string $currencyKey;

    public function getCurrencyKey(): string
    {
        return $this->currencyKey;
    }

    public function getAmountKey(): string
    {
        return $this->amountKey;
    }

    /**
     * @throws Exception
     */
    public static function make(array|Closure|null $schema = null): static
    {
        throw new Exception('Do not use static make method. Use MoneyField::make instead.');
    }

    public static function _make(
        string $name,
        array $currencies,
        string $amountKey,
        string $currencyKey,
    ): static {
        $static = app(static::class);
        $static->currencies = $currencies;
        $static->amountKey = $amountKey;
        $static->currencyKey = $currencyKey;

        $static
            ->configure()
            ->label($name)
            ->statePath($name);

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->input = TextInput::make($this->getAmountKey())
            ->rule('money')
            ->numeric();

        $this->select = Select::make($this->getCurrencyKey())
            ->options(fn () => $this->getOptions())
            ->selectablePlaceholder(false);

        $this->schema($this->getSchemaComponents());

        $this->extraFieldWrapperAttributes(fn () => [
            'class' => 'money-input-field',
            'style' => '--money-select-width: '.$this->getSelectWidth(),
            'data-select-position' => $this->getCurrencyPosition(),
        ]);

        /** @phpstan-ignore-next-line */
        $this->columns([
            'default' => '2',
        ]);
    }

    public function currencies(array|Closure $currencies): static
    {
        $this->currencies = $currencies;

        return $this;
    }

    public function getCurrencies(): array
    {
        return $this->evaluate($this->currencies);
    }

    public function currencyPosition(string|Closure $currencyPosition): static
    {
        $this->baseCurrencyPosition($currencyPosition);
        $this->schema($this->getSchemaComponents());

        return $this;
    }

    protected function getSchemaComponents(): array
    {
        if ($this->getCurrencyPosition() == 'left') {
            return [$this->select, $this->input];
        }

        return [$this->input, $this->select];
    }

    public function selectWidth(Closure|string $width): static
    {
        $this->selectWidth = $width;

        return $this;
    }

    public function getSelectWidth(): string
    {
        return $this->evaluate($this->selectWidth);
    }

    private function getOptions(): array
    {
        return array_combine($this->getCurrencies(), $this->getCurrencies());
    }
}
