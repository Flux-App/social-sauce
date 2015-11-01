<?php

namespace Fuzz\SocialSauce;

use Fuzz\ApiClient\Client;
use Fuzz\ApiClient\OAuthClient;

class FacebookClient extends OAuthClient
{
	/**
	 * Client base url
	 *
	 * @var string
	 */
	const OPEN_GRAPH_BASE_URL = 'https://graph.facebook.com/';

	/**
	 * Client API version
	 *
	 * @var string
	 */
	const OPEN_GRAPH_API_VERSION_PREFIX = 'v2.2/';

	/**
	 * Create and return the HTTP client.
	 *
	 * @return Client
	 */
	protected function createHttpClient()
	{
		return new Client(self::OPEN_GRAPH_BASE_URL);
	}

	/**
	 * Verify the user's token.
	 *
	 * @return int|false
	 */
	protected function verifyUserToken()
	{
		if ($this->user_data = $this->callApi('me')) {
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
		$params['access_token'] = $this->access_token;

		return $this->http_client->$method(self::OPEN_GRAPH_API_VERSION_PREFIX . $endpoint, $params);
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
				'email'      => isset($raw['email']) ? $raw['email'] : null,
				'first_name' => $raw['first_name'],
				'last_name'  => $raw['last_name'],
				'gender'     => $raw['gender'],
			];
		}

		return null;
	}

	/**
	 * Get the user's friends
	 *
	 * @return array
	 */
	public function getFriends($fields = 'picture,name')
	{
		$response = $this->callApi('me/friends', compact('fields'));

		return isset($response['data']) ? $response['data'] : [];
	}
}
