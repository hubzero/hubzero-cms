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

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDQUESTION_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) {
		$name = Lang::txt('MOD_FEATUREDQUESTION_ANONYMOUS');
		if ($this->row->anonymous == 0)
		{
			$user = User::getInstance($this->row->created_by);
			if (is_object($user))
			{
				$name = $user->get('name');
			}
		}

		$when = Date::of($this->row->created)->relative();
	?>
	<div class="<?php echo $this->cls; ?>">
		<h3><?php echo Lang::txt('MOD_FEATUREDQUESTION'); ?></h3>
	<?php if (is_file(PATH_APP . $this->thumb)) { ?>
		<p class="featured-img">
			<a href="<?php echo Route::url('index.php?option=com_answers&task=question&id=' . $this->row->id); ?>">
				<img width="50" height="50" src="<?php echo $this->thumb; ?>" alt="" />
			</a>
		</p>
	<?php } ?>
		<p>
			<a href="<?php echo Route::url('index.php?option=com_answers&task=question&id=' . $this->row->id); ?>">
				<?php echo $this->escape(strip_tags($this->row->subject)); ?>
			</a>
		<?php if ($this->row->question) { ?>
			: <?php echo \Hubzero\Utility\String::truncate($this->escape(strip_tags($this->row->question)), $this->txt_length); ?>
		<?php } ?>
			<br />
			<span><?php echo Lang::txt('MOD_FEATUREDQUESTION_ASKED_BY', $name); ?></span> -
			<span><?php echo Lang::txt('MOD_FEATUREDQUESTION_AGO', $when); ?></span> -
			<span><?php echo ($this->row->rcount == 1) ? Lang::txt('MOD_FEATUREDQUESTION_RESPONSE', $this->row->rcount) : Lang::txt('MOD_FEATUREDQUESTION_RESPONSES', $this->row->rcount); ?></span>
		</p>
	</div>
	<?php
	}
}
?>