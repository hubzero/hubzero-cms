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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

$rows = array();
foreach ($this->unapprovedPages as $unapprovedPage)
{
	$gidNumber = $unapprovedPage->get('gidNumber');
	if (!isset($rows[$gidNumber]['pages']))
	{
		$rows[$gidNumber]['pages'] = 1;
		continue;
	}
	$rows[$gidNumber]['pages']++;
}
foreach ($this->unapprovedModules as $unapprovedModule)
{
	$gidNumber = $unapprovedModule->get('gidNumber');
	if (!isset($rows[$gidNumber]['modules']))
	{
		$rows[$gidNumber]['modules'] = 1;
		continue;
	}
	$rows[$gidNumber]['modules']++;
}
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="adminlist grouppages-list">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_GROUPPAGES_COL_GROUP'); ?></th>
				<th scope="col"><?php echo Lang::txt('MOD_GROUPPAGES_COL_PAGES'); ?></th>
				<th scope="col"><?php echo Lang::txt('MOD_GROUPPAGES_COL_MODULES'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($rows) > 0) : ?>
				<?php foreach ($rows as $gidNumber => $row) : ?>
					<tr>
						<td>
							<?php
								$group = \Hubzero\User\Group::getInstance($gidNumber);
								echo $group->get('description');
							?>
						</td>
						<td>
							<a class="page" href="<?php echo Route::url('index.php?option=com_groups&gid=' . $group->get('cn') . '&controller=pages'); ?>">
								<?php echo (isset($row['pages'])) ? $row['pages'] : 0; ?>
							</a>
						</td>
						<td>
							<a class="module" href="<?php echo Route::url('index.php?option=com_groups&gid=' . $group->get('cn') . '&controller=modules'); ?>">
								<?php echo (isset($row['modules'])) ? $row['modules'] : 0; ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3">
						<em><?php echo Lang::txt('MOD_GROUPPAGES_NO_RESULTS'); ?></em>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>