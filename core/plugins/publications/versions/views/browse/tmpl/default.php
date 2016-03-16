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

// No direct access
defined('_HZEXEC_') or die();

$this->css();

// Build pub url
$route = $this->publication->project_provisioned == 1
	? 'index.php?option=com_publications&task=submit'
	: 'index.php?option=com_projects&alias=' . $this->publication->project_alias . '&active=publications';
$url = Route::url($route . '&pid=' . $this->publication->id);

?>
<h3 id="versions">
	<?php echo Lang::txt('PLG_PUBLICATION_VERSIONS'); ?>
</h3>
<?php if ($this->authorized && $this->contributable) { ?>
	<p class="info statusmsg"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_ONLY_PUBLIC_SHOWN'); ?>
		<a href="<?php echo $url . '?action=versions'; ?>"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VIEW_ALL'); ?></a>
	</p>
<?php } ?>
<?php if ($this->versions && count($this->versions) > 0) { ?>
	<table class="resource-versions">
		<thead>
			<tr>
				<th><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VERSION'); ?></th>
				<th><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_RELEASED'); ?></th>
				<th><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_DOI_HANDLE'); ?></th>
				<th><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_STATUS'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$cls = 'even';

			foreach ($this->versions as $v)
			{
				$handle = ($v->doi) ? $v->doi : '' ;

				$cls = (($cls == 'even') ? 'odd' : 'even');
				?>
				<tr class="<?php echo $cls; ?>">
					<td <?php if ($v->version_number == $this->publication->version_number) { echo 'class="active"'; }  ?>><?php echo $v->version_label; ?></td>
					<td><?php echo ($v->published_up && $v->published_up!='0000-00-00 00:00:00') ? Date::of($v->published_up)->toLocal('M d, Y') : 'N/A'; ?></td>
					<td><?php echo $v->doi ? $v->doi : Lang::txt('COM_PUBLICATIONS_NA'); ?></td>
					<td class="<?php echo $v->state == 1 ? 'state_published' : 'state_unpublished'; ?>"><?php echo $v->state == 1 ? Lang::txt('PLG_PUBLICATION_VERSIONS_PUBLISHED') : Lang::txt('PLG_PUBLICATION_VERSIONS_UNPUBLISHED'); ?></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&v=' . $v->version_number); ?>"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VIEW'); ?></a></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
<?php } else { ?>
	<p class="nocontent"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_NO_VERIONS_FOUND'); ?></p>
<?php } ?>
