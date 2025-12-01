# Filament Money

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ymsoft/filament-money.svg?style=flat-square)](https://packagist.org/packages/ymsoft/filament-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ymsoft/filament-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ymsoft/filament-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ymsoft/filament-money/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ymsoft/filament-money/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ymsoft/filament-money.svg?style=flat-square)](https://packagist.org/packages/ymsoft/filament-money)

A Filament plugin for convenient storage and management of monetary fields with built-in currency support. This package provides form components and table columns that handle money values with proper formatting, validation, and multi-currency support.

> **Bill Karwin:**
> *"If I had a dime for every time I've seen someone use FLOAT to store currency, I'd have $999.997634"*

This package is built on top of [cknow/laravel-money](https://github.com/cknow/laravel-money), which provides a robust foundation for handling monetary values in PHP. The `cknow/laravel-money` package wraps the powerful [moneyphp/money](https://github.com/moneyphp/money) library, giving us precise decimal arithmetic, currency conversion, formatting, and proper money object handling. By leveraging this proven library, we ensure that your financial calculations are accurate and your currency data is stored correctly - avoiding the common pitfalls of floating-point arithmetic.

## Installation

You can install the package via composer:

```bash
composer require ymsoft/filament-money
```

After installing, run the installation command:

```bash
php artisan filament-money:install
```

This command will publish the config file and set up the necessary assets.

Alternatively, you can manually publish the config file with:

```bash
php artisan vendor:publish --tag="filament-money-config"
```

This is the contents of the published config file:

```php
return [
    'locale' => config('app.locale', 'en_US'),
    'defaultCurrency' => config('app.currency', 'USD'),
    'defaultFormatter' => null,
    'defaultSerializer' => null,
    'isoCurrenciesPath' => is_dir(__DIR__.'/../vendor')
        ? __DIR__.'/../vendor/moneyphp/money/resources/currency.php'
        : __DIR__.'/../../../moneyphp/money/resources/currency.php',
    'currencies' => [
        'iso' => 'all',
        'bitcoin' => 'all',
        'custom' => [
            // 'MY1' => 2,
            // 'MY2' => 3
        ],
    ],
];
```

### Custom Theme Setup

To ensure proper styling, you need to use a custom theme and include the plugin's CSS:

**Step 1:** Make sure you have a custom theme configured in your Filament panel.

**Step 2:** Add the plugin's CSS import to your theme file (e.g., `resources/css/filament/admin/theme.css`):

```css
@import '../../../../vendor/ymsoft/filament-money/resources/css/styles.css';
```

**Step 3:** Recompile your theme:

```bash
npm run build
```

> **Note:** Make sure the vendor folder for this plugin is published so that it includes the Tailwind CSS classes.

## Usage

### Basic Setup

First, register the plugin in your Panel Service Provider:

```php
use Ymsoft\FilamentMoney\FilamentMoneyPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentMoneyPlugin::make()
                ->defaultCurrency('USD')
                ->availableCurrencies(['USD', 'EUR', 'GBP'])
                ->currencyPosition('left'), // or 'right'
        ]);
}
```

> **Note:** The plugin configuration provides default settings for all money fields in your panel. However, these settings can be overridden on a per-field basis when needed.

### Form Components

The package provides two types of money input fields:

#### MoneyField with Changeable Currency

Use `MoneyField::make()` to create a money input with a currency selector:

```php
use Ymsoft\FilamentMoney\Filament\Forms\Components\MoneyField;
use Cknow\Money\Money;

MoneyField::make('price')
```

This will create a fused group with:
- A text input for the amount
- A select dropdown for currency selection


**Setting Available Currencies:**

By default, currencies are inherited from the plugin configuration. You can override them per field:

```php
MoneyField::make('price')
    ->currencies(['USD', 'EUR', 'GBP', 'JPY'])
```

**Currency Position:**

By default, the currency position is inherited from the plugin configuration. You can override it per field:

```php
MoneyField::make('price')
    ->currencyPosition('right') // 'left' or 'right'
```

**Select Width:**

```php
MoneyField::make('price')
    ->selectWidth('100px')
```

**Default Value:**

```php
use Cknow\Money\Money;

MoneyField::make('price')
    ->default(new Money(100, 'USD'))
```

**Modifying Input Field:**

```php
MoneyField::make('price')
    ->input(fn ($input) => $input
        ->placeholder('0.00')
        ->step('0.01')
        ->minValue(0)
    )
```

**Modifying Currency Select:**

```php
MoneyField::make('price')
    ->select(fn ($select) => $select
        ->searchable()
    )
```

#### MoneyField with Fixed Currency

Use `MoneyField::fixed()` to create a money input with a fixed currency (shown as prefix/suffix):

```php
use Ymsoft\FilamentMoney\Filament\Forms\Components\MoneyField;
use Cknow\Money\Money;

MoneyField::fixed('salary')
```

This will create a single text input with the currency displayed as a prefix or suffix.

**Currency Position:**

By default, the currency position is inherited from the plugin configuration. You can override it per field:

```php
MoneyField::fixed('salary')
    ->currencyPosition('left') // shows currency as prefix
    // or
    ->currencyPosition('right') // shows currency as suffix
```

**Default Value:**

```php
MoneyField::fixed('salary')
    ->default(new Money(50000, 'USD'))
```

**Modifying Input Field:**

```php
MoneyField::fixed('salary')
    ->input(fn ($input) => $input
        ->placeholder('Enter amount')
        ->suffix('per month')
    )
```

**Label Customization:**

```php
MoneyField::fixed('salary')
    ->label('Monthly Salary')
    ->translateLabel()
    ->hiddenLabel() // if you want to hide the label
```

### Table Columns

Display money values in tables with automatic formatting:

```php
use Ymsoft\FilamentMoney\Filament\Tables\Columns\MoneyColumn;

MoneyColumn::make('price')
```

The column automatically:
- Formats the money value according to the currency
- Displays the currency symbol or code
- Handles different decimal precision based on currency (e.g., 2 decimals for USD, 0 for JPY)
- For currencies with more than 2 decimal places, displays the decimal format with currency code

**Example Output:**
- `$1,234.56` for USD
- `€1.234,56` for EUR
- `¥1,235` for JPY (no decimals)
- `0.00123456 BTC` for Bitcoin (high precision)

### Plugin Configuration

#### Global Currency Settings

Configure default currencies for all money fields in your panel:

```php
FilamentMoneyPlugin::make()
    ->defaultCurrency('EUR')
    ->availableCurrencies(['EUR', 'USD', 'GBP', 'CHF'])
```

#### Currency Position

Set the default position for currency display:

```php
FilamentMoneyPlugin::make()
    ->currencyPosition('right') // or 'left'
```

#### Global Input Modifier

Apply modifications to all money input fields:

```php
FilamentMoneyPlugin::make()
    ->input(fn ($input) => $input
        ->step('0.01')
        ->minValue(0)
        ->maxValue(999999.99)
    )
```

#### Global Select Modifier

Apply modifications to all currency select fields:

```php
FilamentMoneyPlugin::make()
    ->select(fn ($select) => $select
        ->searchable()
    )
```

### Data Storage

The money field stores data as a `Money` object from the `cknow/laravel-money` package. When saving to the database, it will be serialized. When loading from the database, it will be automatically converted back to a `Money` object.

#### Using Money Casts

The `cknow/laravel-money` package provides several cast options for storing money values in your database. These casts handle the conversion between database values and Money objects automatically.

**Available Casts:**

```php
use Cknow\Money\Casts\MoneyDecimalCast;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Casts\MoneyStringCast;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        // Cast money as decimal using the currency defined in the package config
        'price' => MoneyDecimalCast::class,

        // Cast money as integer using the defined currency (e.g., cents for USD)
        'cost' => MoneyIntegerCast::class . ':USD',

        // Cast money as string using the currency defined in the model attribute 'currency'
        'salary' => MoneyIntegerCast::class . ':currency',

        // Cast money as decimal using the defined currency and forcing decimals
        'budget' => MoneyDecimalCast::class . ':EUR,true',
    ];
}
```

**Cast Options Explained:**

1. **MoneyDecimalCast** - Stores money as decimal (e.g., `19.99`)
   - `MoneyDecimalCast::class` - Uses default currency from config
   - `MoneyDecimalCast::class . ':USD'` - Uses specified currency
   - `MoneyDecimalCast::class . ':USD,true'` - Forces decimal places

2. **MoneyIntegerCast** - Stores money as integer in smallest unit (e.g., `1999` cents)
   - `MoneyIntegerCast::class . ':USD'` - Stores as cents for USD
   - Best for exact calculations without floating-point errors

3. **MoneyStringCast** - Stores money as string with dynamic currency
   - `MoneyStringCast::class . ':currency'` - Reads currency from model's `currency` attribute
   - Useful when currency varies per record

**Migration Examples:**

```php
// For MoneyDecimalCast or MoneyStringCast
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 15, 2); // 15 digits total, 2 decimal places
    $table->string('currency', 3)->default('USD'); // If using MoneyStringCast
    $table->timestamps();
});

// For MoneyIntegerCast
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->bigInteger('price'); // Stores in smallest unit (cents, pence, etc.)
    $table->timestamps();
});
```

**Example Model with Multiple Money Fields:**

```php
use Cknow\Money\Casts\MoneyDecimalCast;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'cost', 'tax'];

    protected $casts = [
        'price' => MoneyIntegerCast::class . ':USD',  // stored as cents
        'cost' => MoneyDecimalCast::class . ':USD',    // stored as decimal
        'tax' => MoneyDecimalCast::class . ':USD,true', // forced decimals
    ];
}

// Usage in your code:
$product = new Product();
$product->price = new Money(1999, 'USD'); // $19.99
$product->save();

// Retrieving:
$product->price; // Returns Money object
$product->price->format(); // Returns "$19.99"
```

For more information about money casts and the underlying library, see the [cknow/laravel-money documentation](https://github.com/cknow/laravel-money).

### Advanced Usage

#### Custom Currency Lists

You can define custom currencies per field:

```php
MoneyField::make('price')
    ->currencies(['USD', 'EUR', 'CUSTOM'])
```

#### Validation

The package automatically adds the `money` validation rule to the input field:

```php
// The money validation ensures proper decimal format
MoneyField::make('price')
    ->input(fn ($input) => $input
        ->required()
        ->minValue(0)
        ->maxValue(1000000)
    )
```

### Complete Example

Here's a complete example of a product resource with money fields:

```php
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Ymsoft\FilamentMoney\Filament\Forms\Components\MoneyField;
use Ymsoft\FilamentMoney\Filament\Tables\Columns\MoneyColumn;
use Cknow\Money\Money;

class ProductResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

                MoneyField::make('price')
                    ->currencies(['USD', 'EUR', 'GBP'])
                    ->default(new Money(0, 'USD'))
                    ->required()
                    ->input(fn ($input) => $input
                        ->minValue(0)
                        ->step('0.01')
                    ),

                MoneyField::fixed('cost')
                    ->currencyPosition('left')
                    ->default(new Money(currency: 'USD'))
                    ->input(fn ($input) => $input
                        ->minValue(0)
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                MoneyColumn::make('price'),
                MoneyColumn::make('cost'),
            ]);
    }
}

```

### API Reference

#### MoneyField

##### Static Methods

- `MoneyField::make(string $name)` - Creates a money field with changeable currency
  - `MoneyField::fixed(string $name)` - Creates a money field with fixed currency

##### Methods (MoneyChangeableCurrency)

- `currencies(array|Closure $currencies)` - Set available currencies for the select dropdown
- `currencyPosition(string|Closure $position)` - Set currency position ('left' or 'right')
- `selectWidth(string|Closure $width)` - Set the width of the currency select (default: '80px')
- `input(Closure $callback)` - Modify the amount input field
- `select(Closure $callback)` - Modify the currency select field
- `default(Money $money)` - Set default money value

##### Methods (MoneyFixedCurrency)

- `currencyPosition(string|Closure $position)` - Set currency position ('left' or 'right')
- `input(Closure $callback)` - Modify the amount input field
- `default(Money $money)` - Set default money value
- `label(string|Htmlable|Closure|null $label)` - Set field label
- `translateLabel(bool $shouldTranslate = true)` - Enable label translation
- `hiddenLabel(bool|Closure $condition = true)` - Hide the field label

#### MoneyColumn

Extends `Filament\Tables\Columns\TextColumn` with automatic money formatting.

All standard TextColumn methods are available.

#### FilamentMoneyPlugin

##### Methods

- `make()` - Create plugin instance
- `defaultCurrency(string $currency)` - Set default currency
- `availableCurrencies(array $currencies)` - Set available currencies
- `currencyPosition(string $position)` - Set default currency position ('left' or 'right')
- `input(Closure $callback)` - Global input field modifier
- `select(Closure $callback)` - Global select field modifier

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [YarmaT](https://github.com/YarmaT)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
