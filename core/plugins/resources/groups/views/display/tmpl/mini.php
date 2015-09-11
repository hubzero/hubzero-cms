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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$logo = $this->group->getLogo();
?>
<div id="group-owner" class="container">
	<h3><?php echo $this->escape(stripslashes($this->group->get('description'))); ?></h3>
	<div class="group-content">
	<?php if ($logo) { ?>
		<p class="group-img">
			<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>">
				<img src="<?php echo $logo; ?>" width="50" alt="<?php echo Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $this->escape(stripslashes($this->group->get('description')))); ?>" />
			</a>
		</p>
		<p class="group-description group-withlogo"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	<?php } else { ?>
		<p class="group-description"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	<?php } ?>
	</div>
</div>