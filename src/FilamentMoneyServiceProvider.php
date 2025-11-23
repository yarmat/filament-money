<?php

namespace Ymsoft\FilamentMoney;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMoneyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-money')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->call('vendor:publish', [
                    '--provider' => 'Cknow\Money\MoneyServiceProvider',
                ]);

                $command->askToStarRepoOnGitHub('yarmat/filament-money');
            });
    }
}
