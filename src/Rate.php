<?php
/**
 * @package EvolutionScript
 * @author: EvolutionScript S.A.C.
 * @Copyright (c) 2010 - 2020, EvolutionScript.com
 * @link http://www.evolutionscript.com
 */
namespace EvolutionScript\CurrencyAPI;

use EvolutionScript\CurrencyAPI\Providers\CurrencyLayer;
use EvolutionScript\CurrencyAPI\Providers\OpenExchangeRates;

class Rate
{
	private $service_provider;
	private $backup_provider = null;
	private $cache_class;
	public function __construct()
	{
		$this->cache_class = new Cache();
	}

	public function cacheDirectory($cache_dir)
	{
		$this->cache_class->validateDirectory($cache_dir);
	}

	/**
	 * @param $service_provider CurrencyLayer
	 * @param $backup_provider OpenExchangeRates
	 */
	public function provider($service_provider, $backup_provider)
	{
		$this->service_provider = $service_provider;
		$this->backup_provider = $backup_provider;
	}


	public function getRate($base_currency, $currency)
	{
		if($currency == $base_currency){
			return 1;
		}
		if(!$rate = $this->cache_class->getRate($base_currency, $currency)){
			$rate = $this->apiRate($base_currency, $currency);
			$this->cache_class->saveRate($base_currency, $currency, $rate);
		}
		return $rate;
	}

	private function apiRate($base_currency, $currency)
	{
		if($currency == $base_currency){
			return 1;
		}
		try{
			$this->service_provider->set_currency($currency, $base_currency);
			$rate = $this->service_provider->request();
			return $rate;
		}catch (\Exception $exception){
			if(is_null($this->backup_provider)) {
				throw new \Exception($exception->getMessage());
			}else{
				try {
					$this->backup_provider->set_currency($currency, $base_currency);
					$rate = $this->backup_provider->request();
					return $rate;
				}catch (\Exception $exception){
					throw new \Exception($exception->getMessage());
				}
			}
		}
	}

	public function exchangeFrom($amount, $rate, $decimals=8)
	{
		$result = $amount*$rate;
		return number_format($result, $decimals, '.','');
	}

	public function exchangeTo($rate, $amount, $decimals=8)
	{
		$unit_value = 1/$rate;
		return number_format($unit_value*$amount, $decimals, '.','');
	}
}