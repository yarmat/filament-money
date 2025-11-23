<?php

namespace Ymsoft\FilamentMoney\Traits;

use Closure;
use Filament\Forms\Components\TextInput;

trait HasInput
{
    protected TextInput $input;

    /**
     * @param  Closure(TextInput): void  $callback
     */
    public function input(Closure $callback): static
    {
        $callback($this->input);

        return $this;
    }
}
