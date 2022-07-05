<?php
/**
 * @package EvolutionScript
 * @author: EvolutionScript S.A.C.
 * @Copyright (c) 2010 - 2020, EvolutionScript.com
 * @link http://www.evolutionscript.com
 */

namespace EvolutionScript\CurrencyAPI\Providers;


use GuzzleHttp\Client;

class CurrencyLayer
{
	private $api_key;
	private $currency;
	private $base_currency;
	private $api_url = 'http://apilayer.net/api/live?access_key={api_key}&currencies={currency}&source={base_currency}&format=1';

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	public function set_currency($currency, $base_currency='USD')
	{
		$this->base_currency = $base_currency;
		$this->currency = $currency;
	}

	public function request()
	{
		$api_url = str_replace(['{api_key}', '{currency}','{base_currency}'], [$this->api_key, $this->currency, $this->base_currency], $this->api_url);
		$client = new Client();
		$request = $client->get($api_url);
		if($request->getStatusCode() != 200){
			throw new \Exception('Currency Layer: Could not connect with API server.');
		}
		$response = $request->getBody();
		$response = json_decode($response);
		if(!isset($response->quotes)){
			throw new \Exception('Currency Layer: Could not get currency quotes.');
		}
		$quote = $this->base_currency.$this->currency;
		return $response->quotes->$quote;
	}


}