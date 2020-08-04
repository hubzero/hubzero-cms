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
	 * Create a new badge
	 *
	 * @param   array    $data  badge info. Must have the following:
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
	 * @param   object  $badge  Badge info: ID, Evidence URL
	 * @param   mixed   $users  String (for single user) or array (for multiple users) of user email addresses
	 * @return  void
	 */
	public function grantBadge($badge, $users);
}
