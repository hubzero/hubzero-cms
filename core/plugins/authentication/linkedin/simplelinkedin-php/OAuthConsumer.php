<?php

class OAuthConsumer
{
	public $key;
	public $secret;

	public function __construct($key, $secret, $callback_url=null)
	{
		$this->key = $key;
		$this->secret = $secret;
		$this->callback_url = $callback_url;
	}

	public function __toString()
	{
		return "OAuthConsumer[key=$this->key,secret=$this->secret]";
	}
}
