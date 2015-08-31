<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
