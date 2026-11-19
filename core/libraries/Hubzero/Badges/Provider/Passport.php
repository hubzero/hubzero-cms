<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Badges\Provider;

use Exception;

/**
 * Passport badges provider
 */
class Passport implements ProviderInterface
{
    /**
     * API endpoint
     *
     * @var  string
     */
    public const PASSPORT_API_ENDPOINT = 'https://api.openpassport.org/1.0.0/';

    /**
     * API claim URL
     *
     * @var  string
     */
    public const PASSPORT_CLAIM_URL    = 'https://www.openpassport.org/MyBadges/Pending';

    /**
     * API denied URL
     *
     * @var  string
     */
    public const PASSPORT_DENIED_URL   = 'https://www.openpassport.org/MyBadges/Denied';

    /**
     * API badges URL
     *
     * @var  string
     */
    public const PASSPORT_BADGES_URL   = 'https://www.openpassport.org/MyBadges';

    /**
     * Credentials
     *
     * @var  \stdClass|false
     */
    private $credentials = false;

    /**
     * Request connection
     *
     * @var  \OAuth|null
     */
    private $request = null;

    /**
     * Request type
     *
     * @var  string
     */
    private $request_type = 'oauth';

    /**
     * Constructor
     *
     * @param   string  $request_type  Request type
     * @return  void
     */
    public function __construct($request_type = 'oauth')
    {
        $this->request_type = $request_type;
    }

    /**
     * Set credentials
     *
     * @param   \stdClass  $passportCredentials
     * @return  void
     */
    public function setCredentials($passportCredentials)
    {
        $params = [];
        $this->credentials = $passportCredentials;
        $this->request = new \OAuth(
            $this->credentials->client_id,
            $this->credentials->client_secret,
            OAUTH_SIG_METHOD_HMACSHA1,
            OAUTH_AUTH_TYPE_FORM,
        );
        $params['username'] = $this->credentials->username;
        $params['password'] = $this->credentials->password;
        $params['client_id'] = $this->credentials->client_id;
        $params['client_secret'] = $this->credentials->client_secret;
        $params['grant_type'] = 'password';
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->request->fetch(
            'https://www.openpassport.org/oauth/token',
            $params,
            OAUTH_HTTP_METHOD_POST,
            ['user-agent' => $userAgent],
        );
        $access = json_decode($this->request->getLastResponse());
        $this->credentials->access_token = $access->access_token;
    }

    /**
     * Create a new badge
     *
     * @param   array<mixed>    $data  badge info. Must have the following:
     *                          $data['Name']          = 'Badge name';
     *                          $data['Description']   = 'Badge description';
     *                          $data['CriteriaUrl']   = 'Badge criteria URL';
     *                          $data['Version']       = 'Version';
     *                          $data['BadgeImageUrl'] = 'URL of the badge image: square at least 450px x 450px';
     * @return  integer  Freshly created badge ID
     */
    public function createBadge($data)
    {
        $credentials = $this->credentials;
        if (!$credentials) {
            throw new Exception('You need to set the credentials first.');
        }

        $data['IssuerId'] = $credentials->issuerId;
        $data = json_encode($data);
        $accessToken = $credentials->access_token;
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $headers = [
            'Cache-Control: no-cache',
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken",
            "user-agent: $userAgent",
        ];

        $request = curl_init();
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_URL, 'https://www.openpassport.org/1.0.0/badges');
        curl_setopt($request, CURLOPT_POSTFIELDS, (string) $data);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_VERBOSE, true);

        $response = curl_exec($request);
        $badge = json_decode((string) $response);

        if (empty($badge->Id)) {
            throw new Exception($badge->message);
        }

        return $badge->Id;
    }

    /**
     * Grant badges to users
     *
     * @param   \stdClass  $badge  Badge info: ID, Evidence URL
     * @param   mixed   $users  String (for single user) or array (for multiple users) of user email addresses
     * @return  void
     */
    public function grantBadge($badge, $users)
    {
        $credentials = $this->credentials;
        if (!$credentials) {
            throw new Exception('You need to set the credentials first.');
        }

        if (!is_array($users)) {
            $users = [$users];
        }

        $assertions = [];

        foreach ($users as $user) {
            $data = [];

            $data['BadgeId']      = $badge->id;
            $data['EvidenceUrl']  = $badge->evidenceUrl;
            $data['EmailAddress'] = $user;
            $data['ClientId']     = $credentials->clientId;

            $assertions[] = $data;
            unset($data);
        }

        $assertionsData = (string) json_encode($assertions);

        if ($this->request_type == 'oauth' && $this->request instanceof \OAuth) {
            $url = self::PASSPORT_API_ENDPOINT . 'assertions/';

            // OAuth signs the request; curl sends it, as OAuth::fetch() takes
            // only form parameters and the API wants a JSON body
            $this->request->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);
            try {
                $authorization = $this->request->getRequestHeader(OAUTH_HTTP_METHOD_POST, $url);
            } catch (Exception $e) {
                throw new Exception('Badge grant request failed.');
            }
            if ($authorization === false) {
                throw new Exception('Badge grant request failed.');
            }

            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $assertionsData,
                CURLOPT_HTTPHEADER     => ['Authorization: ' . $authorization, 'Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
            ]);
            $response = curl_exec($curl);
            $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // OAuth::fetch() failed on anything but a success, and so does this
            if (!is_string($response) || $status < 200 || $status >= 300) {
                throw new Exception('Badge grant request failed.');
            }

            $assertion = json_decode($response);
        } else {
            throw new Exception('Unsupported request type');
        }

        foreach ($assertion as $ass) {
            if (empty($ass->Id)) {
                throw new Exception($ass->message);
            }
        }
    }

    /**
     * Check if credentials are set
     *
     * @return  bool
     */
    private function credentialsSet()
    {
        if (empty($this->credentials)) {
            return false;
        }

        return true;
    }

    /**
     * Return a URL
     *
     * @param   string  $type
     * @return  string
     */
    public function getUrl($type = 'Claim')
    {
        switch ($type) {
            case 'Denied':
                return self::PASSPORT_DENIED_URL;

            case 'Badges':
                return self::PASSPORT_BADGES_URL;

            default:
                return self::PASSPORT_CLAIM_URL;
        }
    }

    /**
     * Get assertions by email address
     *
     * @param   mixed  $emailAddresses  String (for single user) or array (for multiple users) of user email addresses
     * @return  array<mixed>
     */
    public function getAssertionsByEmailAddress($emailAddresses)
    {
        if (!$this->credentialsSet()) {
            throw new Exception('You need to set the credentials first.');
        }

        if (!is_array($emailAddresses)) {
            $emailAddresses = [$emailAddresses];
        }

        $query_params = implode('%20', $emailAddresses);
        $url = self::PASSPORT_API_ENDPOINT . "assertions?emailAddresses=" . $query_params;

        if ($this->request_type == 'oauth' && $this->request instanceof \OAuth) {
            $this->request->setAuthType(OAUTH_AUTH_TYPE_URI);
            try {
                $this->request->fetch($url, [], OAUTH_HTTP_METHOD_GET, ['Content-Type' => 'application/json']);
            } catch (Exception $e) {
                throw new Exception('Assertations by email request failed.');
            }

            $response = json_decode($this->request->getLastResponse());
        } else {
            throw new Exception('Unsupported request type');
        }

        return $response;
    }
}
