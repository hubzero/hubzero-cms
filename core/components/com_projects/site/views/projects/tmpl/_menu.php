<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$assetTabs = array();

// Sort tabs so that asset tabs are together
foreach ($this->tabs as $tab)
{
	if ($tab['submenu'] == 'Assets')
	{
		$assetTabs[] = $tab;
	}
}
$a = 0;

$counts = $this->model->get('counts');

?>
	<ul class="projecttools">
		<li<?php if ($this->active == 'feed') { echo ' class="active"'; }?>>
			<a class="newsupdate" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=feed'); ?>" title="<?php echo Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>"><span><?php echo Lang::txt('COM_PROJECTS_TAB_FEED'); ?></span>
			<span id="c-new" class="mini highlight <?php if (empty($counts['new'])) { echo 'hidden'; } ?>"><span id="c-new-num"><?php echo empty($counts['new']) ? 0 : $counts['new']; ?></span></span></a>
		</li>
		<li<?php if ($this->active == 'info') { echo ' class="active"'; }?>><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=info'); ?>" class="inform" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('COM_PROJECTS_TAB_INFO')); ?>">
			<span><?php echo Lang::txt('COM_PROJECTS_TAB_INFO'); ?></span></a>
		</li>
<?php
foreach ($this->tabs as $tab)
{
	if ($tab['name'] == 'blog')
	{
		continue;
	}

	if (isset($tab['submenu']) && $tab['submenu'] == 'Assets' && count($assetTabs) > 0)
	{
		// counter for asset tabs
		$a++;

		// Header tab
		if ($a == 1)
		{
			?>
			<li class="assets">
				<span><?php echo Lang::txt('COM_PROJECTS_TAB_ASSETS'); ?></span>

				<ul class="assetlist">
					<?php
					foreach ($assetTabs as $aTab)
					{
						?>
						<li<?php if ($aTab['name'] == $this->active) { echo ' class="active"'; } ?>>
							<a class="<?php echo $aTab['name']; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=' . $aTab['name']); ?>/" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower($aTab['title']); ?>">
								<span><?php echo $aTab['title']; ?></span>
								<?php if (isset($counts[$aTab['name']]) && $counts[$aTab['name']] != 0) { ?>
									<span class="mini" id="c-<?php echo $aTab['name']; ?>"><span id="c-<?php echo $aTab['name']; ?>-num"><?php echo $counts[$aTab['name']]; ?></span></span>
								<?php } ?>
							</a>
						</li>
						<?php
					}
					?>
				</ul>
			</li>
			<?php
		}

		continue;
	}

	?>
	<li<?php if ($tab['name'] == $this->active) { echo ' class="active"'; } ?>>
		<a class="<?php echo $tab['name']; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=' . $tab['name']); ?>/" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower($tab['title']); ?>">
			<span><?php echo $tab['title']; ?></span>
		<?php if (isset($counts[$tab['name']]) && $counts[$tab['name']] != 0) { ?>
			<span class="mini" id="c-<?php echo $tab['name']; ?>"><span id="c-<?php echo $tab['name']; ?>-num"><?php echo $counts[$tab['name']]; ?></span></span>
		<?php } ?>
		</a>
	</li>
	<?php
}
?>
</ul>
