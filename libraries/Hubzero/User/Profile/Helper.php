<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User\Profile;

use Hubzero\User\Profile;
use Hubzero\Image\Identicon;

/**
 * Profile helper class
 */
class Helper
{
	/**
	 * Short description for 'iterate_profiles'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $func Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function iterate_profiles($func)
	{
		$db = \JFactory::getDBO();
		$db->setQuery("SELECT uidNumber FROM `#__xprofiles`;");

		$result = $db->loadResultArray();

		if ($result === false)
		{
			JError::raiseError(500, 'Error retrieving data from xprofiles table: ' . $db->getErrorMsg());
			return false;
		}

		foreach ($result as $row)
		{
			$func($row);
		}

		return true;
	}

	/**
	 * Find a username by email address
	 *
	 * @param      string $email Email address to look up
	 * @return     mixed False if not found, string if found
	 */
	public static function find_by_email($email)
	{
		if (empty($email))
		{
			return false;
		}

		$db = \JFactory::getDBO();
		$db->setQuery("SELECT username FROM `#__xprofiles` WHERE `email`=" . $db->Quote($email));

		$result = $db->loadResultArray();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Get member picture
	 *
	 * @param      mixed $member Parameter description (if any) ...
	 * @param      integer $anonymous Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getMemberPhoto($member, $anonymous=0, $thumbit=true)
	{
		static $dfthumb;

		$config = \JComponentHelper::getParams('com_members');

		// Get the default picture
		// We need to do this here as it may be needed by the Gravatar service
		if (!$dfthumb)
		{
			$dfthumb = DS . ltrim($config->get('defaultpic', '/components/com_members/assets/img/profile.gif'), DS);
			if ($thumbit)
			{
				$dfthumb = self::thumbit($dfthumb);
			}
		}

		$paths = array();

		// If not anonymous
		if (!$anonymous)
		{
			if ($member instanceof \JUser)
			{
				$member = Profile::getInstance($member->get('id'));
			}
			else if (is_numeric($member) || is_string($member))
			{
				$member = Profile::getInstance($member);
			}

			// If we have a member
			if (is_object($member))
			{
				if (!$member->get('picture'))
				{
					// Do we auto-generate a picture?
					if ($config->get('identicon'))
					{
						$path = JPATH_ROOT . DS . trim($config->get('webpath', '/site/members'), DS) . DS . self::niceidformat($member->get('uidNumber'));

						if (!is_dir($path))
						{
							\JFolder::create($path);
						}

						if (is_dir($path))
						{
							$identicon = new Identicon();

							// Create a profile image
							$imageData = $identicon->getImageData($member->get('email'), 200, $config->get('identicon_color', null));
							file_put_contents($path . DS . 'identicon.png', $imageData);

							// Create a thumbnail image
							$imageData = $identicon->getImageData($member->get('email'), 50, $config->get('identicon_color', null));
							file_put_contents($path . DS . 'identicon_thumb.png', $imageData);

							// Save image to profile
							$member->set('picture', 'identicon.png');
							// Update directly. Using update() method can cause unexpected data loss in some cases.
							$database = \JFactory::getDBO();
							$database->setQuery("UPDATE `#__xprofiles` SET picture=" . $database->quote($member->get('picture')) . " WHERE uidNumber=" . $member->get('uidNumber'));
							$database->query();
							//$member->update();
						}
					}
				}

				// If member has a picture set
				if ($member->get('picture'))
				{
					$thumb  = DS . trim($config->get('webpath', '/site/members'), DS);
					$thumb .= DS . self::niceidformat($member->get('uidNumber'));

					$thumbAlt = $thumb . DS . ltrim($member->get('picture'), DS);
					if ($thumbit)
					{
						$thumbAlt = $thumb . DS . 'thumb.png';
					}

					$thumb .= DS . ltrim($member->get('picture'), DS);

					if ($thumbit)
					{
						$thumb = self::thumbit($thumb);
					}

					$paths[] = $thumbAlt;
					$paths[] = $thumb;
				}
				else
				{
					// If use of gravatars is enabled
					if ($config->get('gravatar'))
					{
						$hash = md5(strtolower(trim($member->get('email'))));
						$protocol = \JBrowser::getInstance()->isSSLConnection() ? 'https' : 'http';
						//$paths[] = $protocol . '://www.gravatar.com/avatar/' . htmlspecialchars($hash) . '?' . (!$thumbit ? 's=300&' : '') . 'd=' . urlencode(JURI::base() . $dfthumb);
						return $protocol
								. '://www.gravatar.com/avatar/' . htmlspecialchars($hash) . '?'
								. (!$thumbit ? 's=300&' : '')
								. 'd=' . urlencode(str_replace('/administrator', '', rtrim(\JURI::base(), DS)) . DS . $dfthumb);
					}
				}
			}
		}

		// Add the default picture last
		$paths[] = $dfthumb;

		// Start running through paths until we find a valid one
		foreach ($paths as $path)
		{
			if ($path && file_exists(JPATH_ROOT . $path))
			{
				return str_replace('/administrator', '', rtrim(\JURI::getInstance()->base(true), DS)) . $path;
			}
		}
	}

	/**
	 * Generate a thumbnail file name format
	 * example.jpg -> example_thumb.jpg
	 *
	 * @param      string $thumb Filename to get thumbnail of
	 * @return     string
	 */
	public static function thumbit($thumb)
	{
		jimport('joomla.filesystem.file');
		$ext = \JFile::getExt($thumb);

		return \JFile::stripExt($thumb) . '_thumb.' . $ext;
	}

	/**
	 * Pad a user ID with zeros
	 * ex: 123 -> 00123
	 *
	 * @param      integer $someid
	 * @return     integer
	 */
	public static function niceidformat($someid)
	{
		$prfx = '';
		if (substr($someid, 0, 1) == '-')
		{
			$prfx = 'n';
			$someid = substr($someid, 1);
		}
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $prfx . $someid;
	}
}

