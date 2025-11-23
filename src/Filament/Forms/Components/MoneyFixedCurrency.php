<?php

namespace Ymsoft\FilamentMoney\Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Illuminate\Contracts\Support\Htmlable;
use Ymsoft\FilamentMoney\Traits\HasCurrencyPosition;
use Ymsoft\FilamentMoney\Traits\HasDefaultState;
use Ymsoft\FilamentMoney\Traits\HasInput;

class MoneyFixedCurrency extends Group
{
    use HasCurrencyPosition;
    use HasDefaultState;
    use HasInput;

    protected TextInput $input;

    protected string $amountKey;

    protected string $currencyKey;

    /**
     * @throws Exception
     */
    public static function make(array|Closure $schema = []): static
    {
        throw new Exception('Do not use static make method. Use MoneyField::fixed instead.');
    }

    public static function _make(
        string $name,
        string $amountKey,
        string $currencyKey,
    ): static {
        $static = app(static::class);
        $static->statePath($name);

        $static->input = TextInput::make($amountKey)
            ->rule('money')
            ->numeric();

        $static->schema([
            $static->input,
        ]);

        $static->amountKey = $amountKey;
        $static->currencyKey = $currencyKey;

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->input->prefix(function () {
            if ($this->getCurrencyPosition() === 'left') {
                return $this->getState()[$this->getCurrencyKey()] ?? '';
            }

            return false;
        });

        $this->input->suffix(function () {
            if ($this->getCurrencyPosition() === 'right') {
                return $this->getState()[$this->getCurrencyKey()] ?? '';
            }

            return false;
        });
    }

    public function hiddenLabel(bool|Closure $condition = true): static
    {
        $this->input->hiddenLabel($condition);

        return $this;
    }

    public function label(string|Htmlable|Closure|null $label): static
    {
        $this->input->label($label);

        return $this;
    }

    public function translateLabel(bool $shouldTranslateLabel = true): static
    {
        $this->input->translateLabel($shouldTranslateLabel);

        return $this;
    }

    public function getAmountKey(): string
    {
        return $this->amountKey;
    }

    public function getCurrencyKey(): string
    {
        return $this->currencyKey;
    }
}
