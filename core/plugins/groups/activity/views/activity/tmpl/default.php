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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css()
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();

$no_html = Request::getInt('no_html', 0);

if (!$no_html) {
?>
<div class="activities">
	<form action="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity'); ?>" method="get">
		<fieldset class="filters">
			<h3><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_LATEST'); ?></h3>
			<?php /*<label for="filter-category">
				<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FROM'); ?>
				<select name="category" id="filter-category">
					<option value=""><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ALL'); ?></option>
					<?php
						if ($this->categories)
						{
							foreach ($this->components as $component)
							{
								$component = substr($component, 4);
								$sbjt  = '<option value="'.$component.'"';
								$sbjt .= ($component == $this->filter) ? ' selected="selected"' : '';
								$sbjt .= '>'.$component.'</option>'."\n";
								echo $sbjt;
							}
						}
					?>
				</select>
			</label>

			<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILTER'); ?>" />*/ ?>
		</fieldset>
<?php } ?>

		<?php if ($this->rows->count()) { ?>
			<ul class="activity-feed" data-url="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity'); ?>">
				<?php
				foreach ($this->rows as $row)
				{
					$this->view('default_item')
						->set('group', $this->group)
						->set('row', $row)
						->display();
				}
				?>
			</ul>
			<?php echo $this->rows->pagination; ?>
		<?php } else { ?>
			<div class="results-none">
				<div class="messages">
					<p><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_NO_RESULTS'); ?></p>
				</div>
				<div class="questions">
					<p>
						<strong><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ABOUT_TITLE'); ?></strong><br />
						<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ABOUT'); ?>
					<p>
				</div>
			</div>
		<?php } ?>

<?php if (!$no_html) { ?>
	</form>
</div>
<?php }
