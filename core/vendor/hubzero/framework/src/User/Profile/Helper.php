<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * Run a callback across all profiles
	 *
	 * @param   object   $func  Anonymous function
	 * @return  boolean
	 */
	public static function iterate_profiles($func)
	{
		$db = \App::get('db');
		$db->setQuery("SELECT uidNumber FROM `#__xprofiles`;");

		$result = $db->loadColumn();

		if ($result === false)
		{
			throw new Exception('Error retrieving data from xprofiles table: ' . $db->getErrorMsg(), 500);
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
	 * @param   string  $email  Email address to look up
	 * @return  mixed   False if not found, string if found
	 */
	public static function find_by_email($email)
	{
		if (empty($email))
		{
			return false;
		}

		$db = \App::get('db');
		$db->setQuery("SELECT username FROM `#__xprofiles` WHERE `email`=" . $db->quote($email));

		$result = $db->loadColumn();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Get member picture
	 *
	 * @param   mixed    $member     Member to get picture for
	 * @param   integer  $anonymous  Anonymous user?
	 * @param   boolean  $thumbit    Display thumbnail (default) or full image?
	 * @return  string   Image URL
	 */
	public static function getMemberPhoto($member, $anonymous=0, $thumbit=true, $serveFile=true)
	{
		static $dfthumb;
		static $dffull;

		$config = \Component::params('com_members');

		// Get the default picture
		// We need to do this here as it may be needed by the Gravatar service
		if (!$dffull)
		{
			$dffull = '/core/components/com_members/site/assets/img/profile.gif'; //ltrim($config->get('defaultpic', '/components/com_members/site/assets/img/profile.gif'), DS);
		}
		if (!$dfthumb)
		{
			if ($thumbit)
			{
				$dfthumb = self::thumbit($dffull);
			}
		}

		// lets make sure we have a profile object
		if ($member instanceof \JUser)
		{
			$member = Profile::getInstance($member->get('id'));
		}
		else if (is_numeric($member) || is_string($member))
		{
			$member = Profile::getInstance($member);
		}

		$paths = array();

		$apppath = trim(substr(PATH_APP, strlen(PATH_ROOT)), DS) . '/site/members';

		// If not anonymous
		if (!$anonymous)
		{
			// If we have a member
			if (is_object($member))
			{
				if (!$member->get('picture'))
				{
					// Do we auto-generate a picture?
					if ($config->get('identicon'))
					{
						$path = PATH_APP . DS . trim($config->get('webpath', '/site/members'), DS) . DS . self::niceidformat($member->get('uidNumber'));

						if (!is_dir($path))
						{
							\App::get('filesystem')->makeDirectory($path);
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
							$database = \App::get('db');
							$database->setQuery("UPDATE `#__xprofiles` SET picture=" . $database->quote($member->get('picture')) . " WHERE uidNumber=" . $member->get('uidNumber'));
							$database->query();
							//$member->update();
						}
					}
				}

				// If member has a picture set
				if ($member->get('picture'))
				{
					$thumb  = DS . $apppath . DS . self::niceidformat($member->get('uidNumber'));

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
						$protocol = \App::get('request')->isSecure() ? 'https' : 'http';
						//$paths[] = $protocol . '://www.gravatar.com/avatar/' . htmlspecialchars($hash) . '?' . (!$thumbit ? 's=300&' : '') . 'd=' . urlencode(JURI::base() . $dfthumb);
						return $protocol
								. '://www.gravatar.com/avatar/' . htmlspecialchars($hash) . '?'
								. (!$thumbit ? 's=300&' : '')
								. 'd=' . urlencode(str_replace('/administrator', '', rtrim(\App::get('request')->base(), '/')) . '/' . $dfthumb);
					}
				}
			}
		}

		// Add the default picture last
		$paths[] = ($thumbit) ? $dfthumb : $dffull;

		// Start running through paths until we find a valid one
		foreach ($paths as $path)
		{
			if ($path && file_exists(PATH_ROOT . $path))
			{
				if (!$anonymous)
				{
					// build base path (ex. /site/members/12345)
					$baseMemberPath  = DS . $apppath . DS . self::niceidformat($member->get('uidNumber'));

					// if we want to serve file & path is within /site
					if ($serveFile && strpos($path, $baseMemberPath) !== false)
					{
						// get picture name (allows to pics in subfolder)
						$pic = trim(str_replace($baseMemberPath, '', $path), DS);

						// build serve link
						if (\App::isAdmin())
						{
							$link = \Route::url('index.php?option=com_members&controller=members&task=picture&id=' . $member->get('uidNumber') . '&image=' . $pic);
						}
						else
						{
							$link = \Route::url('index.php?option=com_members&id=' . $member->get('uidNumber')) . DS . 'Image:' . $pic;
						}
						return $link;
					}
				}

				return str_replace('/administrator', '', rtrim(\App::get('request')->base(true), '/')) . $path;
			}
		}
	}

	/**
	 * Generate a thumbnail file name format
	 * example.jpg -> example_thumb.jpg
	 *
	 * @param   string  $thumb  Filename to get thumbnail of
	 * @return  string
	 */
	public static function thumbit($thumb)
	{
		$dot = strrpos($thumb, '.') + 1;
		$ext = substr($thumb, $dot);

		return preg_replace('#\.[^.]*$#', '', $thumb) . '_thumb.' . $ext;
	}

	/**
	 * Pad a user ID with zeros
	 * ex: 123 -> 00123
	 *
	 * @param   integer  $someid
	 * @return  integer
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

