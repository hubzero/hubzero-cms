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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div class="watch<?php echo $this->watched ? ' watching' : ' '; ?>">
	<span class="pub-info-pop tooltips" title="<?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_TITLE') . ' :: ' . Lang::txt('PLG_PUBLICATIONS_WATCH_EXPLAIN'); ?>">&nbsp;</span>
	<?php if ($this->watched) { ?>
		<p><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_WATCHED'); ?> <a href="<?php echo Route::url($this->publication->link() . '&active=watch&action=unsubscribe&confirm=1'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_UNSUBSCRIBE'); ?></a></p>
	<?php } else { ?>
		<p><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_WANT_TO_FOLLOW'); ?>
			<a href="<?php echo Route::url($this->publication->link() . '&active=watch&action=subscribe&confirm=1'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBE'); ?></a>
		</p>
	<?php } ?>
</div>