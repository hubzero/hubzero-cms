<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Badges\Provider;

/**
 * Interface for badge provider
 */
interface ProviderInterface
{
    /**
     * Sign in to the provider
     *
     * @param   \stdClass  $credentials  What the provider signs in with
     * @return  void
     */
    public function setCredentials($credentials);

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
    public function createBadge($data);

    /**
     * Grant badges to users
     *
     * @param   \stdClass  $badge  Badge info: ID, Evidence URL
     * @param   mixed   $users  String (for single user) or array (for multiple users) of user email addresses
     * @return  void
     */
    public function grantBadge($badge, $users);

    /**
     * The address of one of the provider's pages, such as where badges are claimed
     *
     * @param   string  $type
     * @return  string
     */
    public function getUrl($type = 'Claim');

    /**
     * The badge assertions issued to some users
     *
     * @param   mixed  $emailAddresses  String (for single user) or array (for multiple users) of user email addresses
     * @return  array<mixed>
     */
    public function getAssertionsByEmailAddress($emailAddresses);
}
