<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Constantcontact\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Constant Contact V3 API client
 *
 * Lightweight wrapper around the V3 REST API using Guzzle.
 * Handles OAuth2 token refresh automatically.
 */
class ConstantContactClient
{
    /**
     * V3 API base URL
     *
     * @var string
     */
    const BASE_URL = 'https://api.cc.email/v3';

    /**
     * OAuth2 token endpoint
     *
     * @var string
     */
    const TOKEN_URL = 'https://authz.constantcontact.com/oauth2/default/v1/token';

    /**
     * Seconds before actual expiry to trigger a refresh
     *
     * @var int
     */
    const TOKEN_EXPIRY_BUFFER = 300;

    /**
     * OAuth2 client ID
     *
     * @var string
     */
    protected $clientId;

    /**
     * OAuth2 client secret
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Current access token
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Current refresh token
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * Unix timestamp when the access token expires
     *
     * @var int
     */
    protected $tokenExpires;

    /**
     * HTTP client for API requests
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * Callback to persist refreshed tokens
     *
     * @var callable|null
     */
    protected $tokenPersistCallback;

    /**
     * Constructor
     *
     * @param  string        $clientId
     * @param  string        $clientSecret
     * @param  string        $accessToken
     * @param  string        $refreshToken
     * @param  int           $tokenExpires         Unix timestamp
     * @param  callable|null $tokenPersistCallback  Called with ($accessToken, $refreshToken, $tokenExpires)
     * @param  Client|null   $httpClient            Optional Guzzle client for testing
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $accessToken,
        string $refreshToken,
        int $tokenExpires = 0,
        ?callable $tokenPersistCallback = null,
        ?Client $httpClient = null
    ) {
        $this->clientId             = $clientId;
        $this->clientSecret         = $clientSecret;
        $this->accessToken          = $accessToken;
        $this->refreshToken         = $refreshToken;
        $this->tokenExpires         = $tokenExpires;
        $this->tokenPersistCallback = $tokenPersistCallback;
        $this->httpClient           = $httpClient ?: new Client([
            'base_uri' => self::BASE_URL,
            'timeout'  => 30,
        ]);
    }

    /**
     * Check if the current access token has expired or is about to expire
     *
     * @return bool
     */
    protected function isTokenExpired(): bool
    {
        return time() >= ($this->tokenExpires - self::TOKEN_EXPIRY_BUFFER);
    }

    /**
     * Refresh the OAuth2 access token using the refresh token
     *
     * @throws \RuntimeException if refresh fails
     * @return void
     */
    protected function refreshAccessToken(): void
    {
        $tokenClient = new Client(['timeout' => 30]);

        try {
            $response = $tokenClient->post(self::TOKEN_URL, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Accept'        => 'application/json',
                ],
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $this->refreshToken,
                ],
            ]);
        } catch (RequestException $e) {
            $message = 'Token refresh failed';
            if ($e->hasResponse()) {
                $body = json_decode((string) $e->getResponse()->getBody(), true);
                $detail = $body['error_description']
                    ?? $body['error']
                    ?? (string) $e->getResponse()->getBody();
                $message .= ': ' . $detail;
            }
            throw new \RuntimeException($message, 0, $e);
        }

        $data = json_decode((string) $response->getBody(), true);

        if (empty($data['access_token']) || empty($data['refresh_token'])) {
            throw new \RuntimeException('Token refresh returned incomplete data');
        }

        $this->accessToken  = $data['access_token'];
        $this->refreshToken = $data['refresh_token'];
        $this->tokenExpires = time() + ($data['expires_in'] ?? 86400);

        if ($this->tokenPersistCallback) {
            call_user_func(
                $this->tokenPersistCallback,
                $this->accessToken,
                $this->refreshToken,
                $this->tokenExpires
            );
        }
    }

    /**
     * Ensure we have a valid token, refreshing if necessary
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function ensureValidToken(): void
    {
        if ($this->isTokenExpired()) {
            $this->refreshAccessToken();
        }
    }

    /**
     * Make an authenticated request to the Constant Contact V3 API
     *
     * @param  string $method   HTTP method
     * @param  string $uri      Relative URI path
     * @param  array  $options  Guzzle request options
     * @return array            Decoded JSON response body
     * @throws \RuntimeException
     */
    protected function request(string $method, string $uri, array $options = []): array
    {
        $this->ensureValidToken();

        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept'        => 'application/json',
        ]);

        try {
            $response = $this->httpClient->request($method, $uri, $options);
        } catch (RequestException $e) {
            $message = "CC API {$method} {$uri} failed";
            if ($e->hasResponse()) {
                $code = $e->getResponse()->getStatusCode();
                $body = json_decode((string) $e->getResponse()->getBody(), true);
                $errorBody = $body['error_message']
                    ?? $body[0]['error_message']
                    ?? (string) $e->getResponse()->getBody();
                $message .= " ({$code}): " . $errorBody;
            }
            throw new \RuntimeException($message, 0, $e);
        }

        $body = (string) $response->getBody();

        if ($body === '') {
            return [];
        }

        return json_decode($body, true) ?: [];
    }

    /**
     * Retrieve all contact lists
     *
     * @return array  Array of list objects
     * @throws \RuntimeException
     */
    public function getLists(): array
    {
        $data = $this->request('GET', '/v3/contact_lists');

        return $data['lists'] ?? [];
    }

    /**
     * Search for a contact by email address
     *
     * @param  string $email
     * @return array|null  Contact data or null if not found
     * @throws \RuntimeException
     */
    public function searchContactByEmail(string $email): ?array
    {
        $data = $this->request('GET', '/v3/contacts', [
            'query' => [
                'email'  => $email,
                'status' => 'all',
                'include' => 'list_memberships',
            ],
        ]);

        $contacts = $data['contacts'] ?? [];

        return !empty($contacts) ? $contacts[0] : null;
    }

    /**
     * Create a new contact with the given email on the specified list
     *
     * @param  string $email
     * @param  string $listId  Contact list UUID
     * @return array           Created contact data
     * @throws \RuntimeException
     */
    public function createContact(string $email, string $listId): array
    {
        return $this->request('POST', '/v3/contacts', [
            'json' => [
                'email_address' => [
                    'address'            => $email,
                    'permission_to_send' => 'implicit',
                ],
                'list_memberships' => [$listId],
                'create_source'    => 'Account',
            ],
        ]);
    }

    /**
     * Update an existing contact
     *
     * @param  string $contactId
     * @param  array  $data       Fields to update
     * @return array              Updated contact data
     * @throws \RuntimeException
     */
    public function updateContact(string $contactId, array $data): array
    {
        return $this->request('PUT', '/v3/contacts/' . urlencode($contactId), [
            'json' => $data,
        ]);
    }

    /**
     * Delete a contact (moves them to opt-out status)
     *
     * @param  string $contactId
     * @return void
     * @throws \RuntimeException
     */
    public function deleteContact(string $contactId): void
    {
        $this->ensureValidToken();

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept'        => 'application/json',
            ],
        ];

        try {
            $this->httpClient->request('DELETE', '/v3/contacts/' . urlencode($contactId), $options);
        } catch (RequestException $e) {
            $message = "CC API DELETE /v3/contacts/{$contactId} failed";
            if ($e->hasResponse()) {
                $code = $e->getResponse()->getStatusCode();
                $body = json_decode((string) $e->getResponse()->getBody(), true);
                $errorBody = $body['error_message']
                    ?? $body[0]['error_message']
                    ?? (string) $e->getResponse()->getBody();
                $message .= " ({$code}): " . $errorBody;
            }
            throw new \RuntimeException($message, 0, $e);
        }
    }
}
