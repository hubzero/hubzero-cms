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

if ($this->publicView || !isset($this->tabs))
{
	$this->tabs = array();
}
if ($this->active == 'edit')
{
	$this->tabs[] = array('name' => 'edit', 'title' => 'Edit', 'submenu' => '', 'show' => true);
}

// Sort tabs so that asset tabs are together
foreach ($this->tabs as $tab)
{
	if ($tab['submenu'] == 'Assets')
	{
		$assetTabs[] = $tab;
	}
}

if (count($assetTabs) > 1)
{
	array_splice( $this->tabs, 3, 0, array(0 => array('name' => 'assets', 'title' => 'Assets', 'show' => true)) );
}

$counts = $this->model->get('counts');

?>
<div class="menu-wrapper">
	<?php if ($this->publicView == false && isset($this->tabs) && $this->tabs) { ?>
		<ul>
			<?php foreach ($this->tabs as $tab)
			{
				if (isset($tab['submenu']) && $tab['submenu'] == 'Assets' && count($assetTabs) > 1)
				{
					continue;
				}
				if (isset($tab['alias']) && trim($tab['alias']))
				{
					$tab['name'] = trim($tab['alias']);
				}
				$gopanel = $tab['name'] == 'assets' ? 'files' : $tab['name'];
				$active = (($tab['name'] == $this->active) || ($tab['name'] == 'assets' && (isset($tab['submenu']) && $tab['submenu'] == 'Assets')))
				?>
				<li<?php if ($active) { echo ' class="active"'; } ?> id="tab-<?php echo $tab['name']; ?>">
					<a class="tab-<?php echo $tab['name']; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=' . $gopanel); ?>/" title="<?php echo ucfirst(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . ucfirst($tab['title']); ?>">
						<span class="label"><?php echo $tab['title']; ?></span>
						<?php if ($tab['name'] != 'feed' && isset($counts[$tab['name']]) && $counts[$tab['name']] != 0) { ?>
							<span class="mini" id="c-<?php echo $tab['name']; ?>"><span id="c-<?php echo $tab['name']; ?>-num"><?php echo $counts[$tab['name']]; ?></span></span>
						<?php } elseif ($tab['name'] == 'feed') { ?>
							<span id="c-new" class="mini highlight <?php if (empty($counts['new'])) { echo 'hidden'; } ?>"><span id="c-new-num"><?php echo empty($counts['new']) ? 0 : $counts['new']; ?></span></span>
						<?php } ?>
					</a>
					<?php if ($tab['name'] == 'assets') { ?>
						<div id="asset-selection" class="submenu-wrap">
							<?php foreach ($assetTabs as $aTab) { ?>
								<p>
									<a class="<?php echo $aTab['name']; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=' . $aTab['name']); ?>/" title="<?php echo ucfirst(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . ucfirst($aTab['title']); ?>" id="tab-<?php echo $aTab['name']; ?>">
										<span class="label"><?php echo $aTab['title']; ?></span>
										<?php if (isset($counts[$aTab['name']]) && $counts[$aTab['name']] != 0) { ?>
											<span class="mini" id="c-<?php echo $aTab['name']; ?>"><span id="c-<?php echo $aTab['name']; ?>-num"><?php echo $counts[$aTab['name']]; ?></span></span>
										<?php } ?>
									</a>
								</p>
							<?php } ?>
						</div>
					<?php } ?>
				</li>
			<?php } // end foreach ?>
		</ul>
	<?php } else {  ?>
		<?php if (isset($this->guest) && $this->guest) { ?>
			<p><?php echo Lang::txt('COM_PROJECTS_ARE_YOU_MEMBER'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=view') . '?action=login'; ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LOGIN')).'</a> '.Lang::txt('COM_PROJECTS_LOGIN_TO_PRIVATE_AREA'); ?></p>
		<?php } ?>
	<?php } ?>
</div>