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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div<?php echo ($this->cssId) ? ' id="' . $this->cssId . '"' : ''; echo ($this->cssClass) ? ' class="' . $this->cssClass . '"' : ''; ?>>
<?php if (count($this->rows) > 0) { ?>
	<ul class="questions">
	<?php
	foreach ($this->rows as $row)
	{
		$name = Lang::txt('MOD_RECENTQUESTIONS_ANONYMOUS');
		if (!$row->get('anonymous'))
		{
			$name = $row->creator()->get('name');
		}
		$rcount = $row->responses()->where('state', '<', 2)->count();
		?>
		<li>
		<?php if ($this->style == 'compact') { ?>
			<a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a>
		<?php } else { ?>
			<h4><a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a></h4>
			<p class="entry-details">
				<?php echo Lang::txt('MOD_RECENTQUESTIONS_ASKED_BY', $this->escape($name)); ?> @
				<span class="entry-time"><?php echo $row->created('time'); ?></span> on
				<span class="entry-date"><?php echo $row->created('date'); ?></span>
				<span class="entry-details-divider">&bull;</span>
				<span class="entry-comments">
					<a href="<?php echo Route::url($row->link() . '#answers'); ?>" title="<?php echo Lang::txt('MOD_RECENTQUESTIONS_RESPONSES', $rcount); ?>">
						<?php echo $rcount; ?>
					</a>
				</span>
			</p>
			<p class="entry-tags"><?php echo Lang::txt('MOD_RECENTQUESTIONS_TAGS'); ?>:</p>
			<?php
			echo $row->tags('cloud');
			?>
		<?php } ?>
		</li>
		<?php
	}
	?>
	</ul>
<?php } else { ?>
	<p><?php echo Lang::txt('MOD_RECENTQUESTIONS_NO_RESULTS'); ?></p>
<?php } ?>
</div>