<?php

namespace Fuzz\SocialSauce;

use Fuzz\ApiClient\Client;
use Fuzz\ApiClient\OAuthClient;

class GooglePlusClient extends OAuthClient
{
	/**
	 * The base URL for Google API calls.
	 *
	 * @var string
	 */
	const GOOGLE_API_BASE_URL = 'https://www.googleapis.com';

	/**
	 * The base URI for Google+ API calls.
	 *
	 * @var string
	 */
	const GOOGLE_PLUS_URI = 'plus/v1';

	/**
	 * Create and return the HTTP client.
	 *
	 * @return Client
	 */
	protected function createHttpClient()
	{
		return new Client(self::GOOGLE_API_BASE_URL);
	}

	/**
	 * Verify the user's token.
	 *
	 * @return int|false
	 */
	protected function verifyUserToken()
	{
		if ($this->user_data = $this->callApi('people/me')) {
			return $this->access_id = $this->user_data['id'];
		}

		return false;
	}

	/**
	 * Invoke the social API.
	 *
	 * @param string $endpoint
	 * @param array  $params
	 * @param string $method
	 */
	public function callApi($endpoint, array $params = [], $method = 'get')
	{
		$params['access_token'] = $this->access_token;

		return $this->http_client->$method(self::GOOGLE_PLUS_URI . '/' . $endpoint, $params);
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
				'email'      => isset($raw['emails'][0]['value']) ? $raw['emails'][0]['value'] : null,
				'first_name' => $raw['name']['givenName'],
				'last_name'  => $raw['name']['familyName'],
				'gender'     => $raw['gender'],
			];
		}

		return null;
	}
}
