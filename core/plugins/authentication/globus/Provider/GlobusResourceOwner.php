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

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GlobusResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array  $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * Get resource owner id.
     *
     * @return string
     */
    public function getId()
    {
        return @$this->response['sub'] ?: null;
    }

    /**
     * Get resource owner display name.
     *
     * @return string
     */
    public function getName()
    {
        return @$this->response['name'] ?: null;
    }

    /**
     * Get resource owner given (first) name.
     *
     * @return string
     */
    public function getGivenName()
    {
        return @$this->response['given_name'] ?: null;
    }

    /**
     * Get resource owner given (first) name.
     * Alias for getGivenName().
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getGivenName();
    }

    /**
     * Get resource owner family (last) name.
     *
     * @return string
     */
    public function getFamilyName()
    {
        return @$this->response['family_name'] ?: null;
    }

    /**
     * Get resource owner family (last) name.
     * Alias for getFamilyName();
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getFamilyName();
    }

    /**
     * Get resource owner eduPersonPrincipalName.
     *
     * @return string
     */
    public function getEPPN()
    {
        return @$this->response['eppn'] ?: null;
    }

    /**
     * Get resource owner eduPersonTargetedID.
     *
     * @return string
     */
    public function getEPTID()
    {
        return @$this->response['eptid'] ?: null;
    }

    /**
     * Get resource owner email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return @$this->response['email'] ?: null;
    }

    /**
     * Get the Identity Provider entityId the resource owner used for
     * authentication.
     *
     * @return string
     */
    public function getIdP()
    {
        return @$this->response['idp'] ?: null;
    }

    /**
     * Get the Identity Provider display name the resource owner used
     * for authentication.
     *
     * @return string
     */
    public function getIdPName()
    {
        return @$this->response['idp_name'] ?: null;
    }

    /**
     * Get resource owner organizational unit.
     *
     * @return string
     */
    public function getOU()
    {
        return @$this->response['ou'] ?: null;
    }

    /**
     * Get resource owner (scoped) affiliation.
     *
     * @return string
     */
    public function getAffiliation()
    {
        return @$this->response['affiliation'] ?: null;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
