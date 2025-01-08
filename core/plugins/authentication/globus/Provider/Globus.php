<?php

/**
 * This file is part of the cilogon/oauth2-cilogon library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Terry Fleury <tfleury@cilogon.org>
 * @copyright 2016 University of Illinois
 * @license   https://opensource.org/licenses/NCSA NCSA
 * @link      https://github.com/cilogon/oauth2-cilogon GitHub
 */

namespace Globus\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Globus extends AbstractProvider
{

    use BearerAuthorizationTrait;
    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return "https://auth.globus.org/v2/oauth2/authorize";
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return "https://auth.globus.org/v2/oauth2/token";
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://auth.globus.org/v2/oauth2/userinfo';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * Other available scopes include: email, profile
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [
            'openid',
        ];
    }

     /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator, defaults to space
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $error = false;
        $errcode = 0;
        $errmsg = '';

        if (!empty($data['error'])) {
            $error = true;
            $errmsg = $data['error'];
            if (!empty($data['error_description'])) {
                $errmsg .= ': ' . $data['error_description'];
            }
        } elseif ($response->getStatusCode() >= 400) {
            $error = true;
            $errcode = $response->getStatusCode();
            $errmsg = $response->getReasonPhrase();
        }

        if ($error) {
            throw new IdentityProviderException($errmsg, $errcode, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param object $response
     * @param AccessToken $token
     * @return GlobusResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GlobusResourceOwner($response);
    }
}
