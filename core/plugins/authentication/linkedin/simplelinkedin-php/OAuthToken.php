<?php

class OAuthToken
{
	// access tokens and request tokens
	public $key;
	public $secret;

	/**
	 * key = the token
	 * secret = the token secret
	 */
	public function __construct($key, $secret)
	{
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * generates the basic string serialization of a token that a server
	 * would respond to request_token and access_token calls with
	 */
	public function to_string()
	{
		return "oauth_token=" .
					 OAuthUtil::urlencode_rfc3986($this->key) .
					 "&oauth_token_secret=" .
					 OAuthUtil::urlencode_rfc3986($this->secret);
	}

	public function __toString()
	{
		return $this->to_string();
	}
}
