<?php

namespace Ymsoft\FilamentMoney\Traits;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Webmozart\Assert\Assert;

/**
 * @mixin EvaluatesClosures
 */
trait HasCurrencyPosition
{
    protected string|Closure $currencyPosition = 'left';

    public function getCurrencyPosition(): Closure|string
    {
        return $this->evaluate($this->currencyPosition);
    }

    public function currencyPosition(Closure|string $currencyPosition): static
    {
        Assert::inArray($this->evaluate($currencyPosition), ['left', 'right']);

        $this->currencyPosition = $currencyPosition;

        return $this;
    }
}
