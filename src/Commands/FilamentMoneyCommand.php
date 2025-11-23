<?php

namespace Ymsoft\FilamentMoney\Commands;

use Illuminate\Console\Command;

class FilamentMoneyCommand extends Command
{
    public $signature = 'filament-money';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
