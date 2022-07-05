<?php
/**
 * @package EvolutionScript
 * @author: EvolutionScript S.A.C.
 * @Copyright (c) 2010 - 2020, EvolutionScript.com
 * @link http://www.evolutionscript.com
 */

namespace EvolutionScript\CurrencyAPI;


class Cache
{
	private $cache_dir= null;

	public function validateDirectory($cache_dir)
	{
		if(!is_dir($cache_dir)){
			throw new \Exception('Cache path is not a valid directory.');
		}
		if(!is_writable($cache_dir)){
			throw new \Exception('Cache directory is not writable.');
		}
		$this->cache_dir = $cache_dir;
	}


	public function getRate($base_currency, $currency)
	{
		if($currency == $base_currency){
			return 1;
		}
		if(is_null($this->cache_dir)){
			return null;
		}
		$file = $this->cache_dir.DIRECTORY_SEPARATOR.'evolutionscript_currency.json';
		if(!file_exists($file)){
			return null;
		}
		$content = file_get_contents($file);
		$json = json_decode($content);
		$quote = $base_currency.$currency;
		if(!isset($json->$quote)){
			return null;
		}
		if($json->$quote->date != date('Y-m-d')){
			unset($json->$quote);
			file_put_contents($file, json_encode($json));
			return null;
		}
		return $json->$quote->rate;
	}

	public function saveRate($base_currency, $currency, $rate)
	{
		if(is_null($this->cache_dir)){
			return false;
		}
		$file = $this->cache_dir.DIRECTORY_SEPARATOR.'evolutionscript_currency.json';
		$quote = $base_currency.$currency;
		if(!file_exists($file)){
			$json = (object) [
				$quote => [
					'rate' => $rate,
					'date' => date('Y-m-d')
				]
			];
		}else{
			$content = file_get_contents($file);
			$json = json_decode($content);
			$json->$quote = (object) [
				'rate' => $rate,
				'date' => date('Y-m-d')
			];
		}
		file_put_contents($file, json_encode($json));
		return true;
	}
}