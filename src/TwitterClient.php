<?php

namespace Fuzz\SocialSauce;

use Fuzz\ApiClient\Client;
use Fuzz\ApiClient\OAuthClient;

class TwitterClient extends OAuthClient
{
	/**
	 * Client base url
	 *
	 * @var string
	 */
	const TWITTER_BASE_URL = 'https://api.twitter.com/1.1/';

	/**
	 * Create and return the HTTP client.
	 *
	 * @return Client
	 */
	protected function createHttpClient()
	{
		return new Client(self::TWITTER_BASE_URL);
	}

	/**
	 * Verify the user's token.
	 *
	 * @return int|false
	 */
	protected function verifyUserToken()
	{
		if ($this->user_data = $this->callApi('account/verify_credentials.json')) {
			return $this->access_id = $this->user_data['id'];
		}

		return false;
	}

	/**
	 * Invoke the social API.
	 *
	 * @param string $endpoint
	 * @param array $params
	 * @param string $method
	 */
	public function callApi($endpoint, array $params = [], $method = 'get')
	{
		// Add in the OAuth 1.0 authorization header
		$this->http_client->addHeader('Authorization', $this->buildOAuth1Header(self::TWITTER_BASE_URL . $endpoint));
		return $this->http_client->$method($endpoint, $params);
	}

	/**
	 * Get the user data in an easily accessible format.
	 *
	 * @return array
	 */
	public function getUserData()
	{
		// First, get our raw data
		$raw = $this->getRawUserData();

		// If our raw data is populated
		if (! empty($raw)) {
			// Return the data in a more useful format/standardized way
			return [
				'name'   => $raw['name'],
				'handle' => $raw['screen_name'],
			];
		}

		return null;
	}

	/**
	 * Get the user's friends
	 *
	 * @return array
	 */
	public function getFriends()
	{
		$response = $this->callApi('friends/list.json');

		return array_map(
			function($item) {
				return [
					'id'     => $item['id'],
					'name'   => $item['name'],
					'handle' => $item['screen_name'],
					'image'  => $item['profile_image_url'],
				];
			},
			$response['users']
		);
	}
}
