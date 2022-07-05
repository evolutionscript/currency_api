# Free Currency API

The Free Currency API connects with some exchangers to get the current currency rate.

This library works with cache to prevent exceeding the API usage limit.

**Installation:**

```bash
composer require evolutionscript/currency_api
```


**Usage:**

```php
use EvolutionScript\CurrencyAPI as CurrencyAPI;

$currencyAPI = new CurrencyAPI\Rate();
//Specify cache directory to save data and make a request once per day. It is optional but prevents exceeding the API usage limit.
$currencyAPI->cacheDirectory(__DIR__.'/cache');

//Connect with Currency Layer and as optional connect with OpenExchangeRates. The optional provider is useful if the primary provider fails.
$currencyAPI->provider(
	new CurrencyAPI\Providers\CurrencyLayer('CURRENCY_LAYER_API'),
	new CurrencyAPI\Providers\OpenExchangeRates('OPEN_EXCHANGE_RATES_API')
);
//Free Api accepts as base currency USD symbol, you can change it if you are using paid version
$rate = $currencyAPI->getRate('USD', 'PEN');
echo "The USDPEN rate is: ".$rate."<br>";

//Calculate how much is 10 USD to PEN and return result with 2 decimals
$result = $currencyAPI->exchangeFrom($rate, 10, 2);
echo "10 USD is equivalent to ".$result." PEN<br>";

//Calculate how much is 100 PEN to USD and result result with 2 decimals
$result = $currencyAPI->exchangeTo($rate, 100, 2);
echo "100 PEN is equivalent to ".$result." USD";
```
