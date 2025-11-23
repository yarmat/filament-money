<?php

namespace Ymsoft\FilamentMoney\Traits;

use Cknow\Money\Money;
use Webmozart\Assert\Assert;

trait HasDefaultState
{
    public function default(mixed $state): static
    {
        $moneyClass = Money::class;

        Assert::isInstanceOf($state, $moneyClass, "The default value must be an instance of $moneyClass.");

        return parent::default($state->toArray());
    }
}
