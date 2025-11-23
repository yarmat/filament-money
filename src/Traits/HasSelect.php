<?php

namespace Ymsoft\FilamentMoney\Traits;

use Closure;
use Filament\Forms\Components\Select;

trait HasSelect
{
    protected Select $select;

    /**
     * @param  Closure(Select): void  $callback
     */
    public function select(Closure $callback): static
    {
        $callback($this->select);

        return $this;
    }
}
