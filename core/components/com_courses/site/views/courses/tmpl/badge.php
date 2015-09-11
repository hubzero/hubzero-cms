<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as state by the Free Software
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('badge.css');

switch ($this->action)
{
	case 'image':
		// Build the upload path
		$img_location  = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS);
		$img_location .= DS . 'badges' . DS . $this->badge->get('id') . DS;

		if (is_dir($img_location))
		{
			$file = Filesystem::files($img_location, 'badge');

			if (isset($file[0]))
			{
				$img_location .= $file[0];
			}
			else
			{
				return App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND'));
			}
		}
		else
		{
			return App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND'));
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($img_location);
		$xserver->disposition('inline');
		$xserver->acceptranges(false);

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('COM_COURSES_SERVER_ERROR'));
		}
		else
		{
			exit;
		}
		return;
	break;

	case 'criteria':
		$title = Lang::txt('COM_COURSES_BADGE_CRITERIA');
		$body  = "<div class=\"criteria-text\">\n";
		$body .= $this->badge->get('criteria_text') . "\n";
		$body .= "</div>\n";
	break;

	case 'validation':
		if (!$this->token)
		{
			App::abort(404, Lang::txt('COM_COURSES_INVALID_REQUEST'));
		}

		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.badge.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.badge.criteria.php';

		$db = \App::get('db');

		$memberBadge = new \Components\Courses\Tables\MemberBadge($db);
		$memberBadge->load(array('validation_token' => $this->token));

		if (!$memberBadge->get('id'))
		{
			App::abort(404, Lang::txt('COM_COURSES_INVALID_REQUEST'));
		}

		$memberTbl = new \Components\Courses\Tables\Member($db);
		$memberTbl->loadByMemberId($memberBadge->member_id);
		$user_id = $memberTbl->get('user_id');

		$criteria = new \Components\Courses\Tables\SectionBadgeCriteria($db);
		$criteria->load($memberBadge->get('criteria_id'));

		$title = Lang::txt('COM_COURSES_BADGE_VALIDATION');
		$body  = "<img class=\"badge-img\" src=\"".$this->badge->get('img_url')."\" width=\"125\" />\n";
		$body .= "<div class=\"badge-validation\">\n";
		$body .= Lang::txt('COM_COURSES_BADGE_VALIDATION_TEXT', User::getInstance($user_id)->get('name'), Date::of($memberBadge->get('earned_on'))->format('M d, Y'));
		$body .= "</div>\n";
		$body .= "<div class=\"badge-criteria\">\n";
		$body .= $criteria->get('text');
		$body .= "</div>\n";
	break;

	default:
		App::abort(404, Lang::txt('COM_COURSES_INVALID_REQUEST'));
	break;
}
?>

<header id="content-header">
	<h2><?php echo Lang::txt($title); ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
		<?php echo $body; ?>
	</div>
</section>