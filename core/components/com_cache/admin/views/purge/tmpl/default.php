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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CACHE_PURGE_EXPIRED_CACHE'), 'purge.png');
Toolbar::custom('purge', 'delete.png', 'delete_f2.png', 'COM_CACHE_PURGE_EXPIRED', false);
Toolbar::divider();
if (User::authorise('core.admin', 'com_cache'))
{
	Toolbar::preferences('com_cache');
	Toolbar::divider();
}
Toolbar::help('purge_expired');
?>

<form action="<?php echo Route::url('index.php?option=com_cache'); ?>" method="post" name="adminForm" id="item-form">

	<p class="mod-purge-instruct"><?php echo Lang::txt('COM_CACHE_PURGE_INSTRUCTIONS'); ?></p>
	<p class="warning"><?php echo Lang::txt('COM_CACHE_RESOURCE_INTENSIVE_WARNING'); ?></p>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_cache" />

	<?php echo Html::input('token'); ?>
</form>
