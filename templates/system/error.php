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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die;

if (!isset($this->error))
{
	$this->error = new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 404);
	$this->debug = false;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->getLanguage(); ?>" dir="<?php echo $this->getDirection(); ?>">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/error.css" type="text/css" />
		<?php if ($this->getDirection() == 'rtl') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/error_rtl.css" type="text/css" />
		<?php endif; ?>
		<?php if (Config::get('debug_lang', '0') == '1' || Config::get('debug', '0') == '1') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/media/cms/css/debug.css" type="text/css" />
		<?php endif; ?>
	</head>
	<body>
		<div class="error">
			<div id="outline">
				<div id="errorboxoutline">
					<div id="errorboxheader"><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></div>
					<div id="errorboxbody">
						<p><strong><?php echo Lang::txt('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></strong></p>
						<ol>
							<li><?php echo Lang::txt('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
							<li><?php echo Lang::txt('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
							<li><?php echo Lang::txt('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
							<li><?php echo Lang::txt('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
							<li><?php echo Lang::txt('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></li>
							<li><?php echo Lang::txt('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></li>
						</ol>
						<p><strong><?php echo Lang::txt('JERROR_LAYOUT_PLEASE_TRY_ONE_OF_THE_FOLLOWING_PAGES'); ?></strong></p>
						<ul>
							<li><a href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo Lang::txt('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>"><?php echo Lang::txt('JERROR_LAYOUT_HOME_PAGE'); ?></a></li>
						</ul>
						<p><?php echo Lang::txt('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?>.</p>
						<div id="techinfo">
							<p><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
							<p>
								<?php if ($this->debug) :
									echo $this->renderBacktrace();
								endif; ?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
