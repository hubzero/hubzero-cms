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

// No direct access.
defined('_HZEXEC_') or die();
?>
<style>
#noresults {
	margin-right: auto;
	margin-left: auto;
}
</style>

<table class="adminlist searchDocument">
	<thead>
		<tr>
			<th><?php echo Lang::txt('ID'); ?></th>
			<th><?php echo Lang::txt('Type'); ?></th>
			<th><?php echo Lang::txt('Title'); ?></th>
			<th><?php echo Lang::txt('Access'); ?></th>
			<th><?php echo Lang::txt('Owner'); ?></th>
			<th> </th>
		</tr>
	</thead>
	<tbody>
			<?php foreach ($this->documents as $document): ?>
				<tr>
					<td><?php echo $document['id']; ?></td>
					<td><?php echo $document['hubtype']; ?></td>
					<td><?php echo $document['title'][0]; ?></td>
					<td><?php echo $document['access_level']; ?></td>
					<td>
						<?php 
							if (isset($document['owner']) && $document['owner'] == '')
							{
								if ($document['owner_type'] == 'user')
								{
									$user = \Hubzero\User\User::one($document['owner'][0]);
									if (isset($user) && is_object($user))
									{
										echo $user->get('name');
									}
									else
									{
										echo Lang::txt('UNKNOWN');
									}
								}
								elseif ($document['owner_type'] == 'group')
								{
									$group = \Hubzero\User\Group::getInstance($document['owner'][0]);
									if (isset($group) && is_object($group))
									{
										echo $group->get('description');
									}
									else
									{
										echo Lang::txt('UNKNOWN');
									}
								}
							}
							else
							{
								echo $document['owner_type'] . ' - ';
								echo Lang::txt('UNKNOWN');
							}
						?>
					</td>
					<td>
						<?php if (!in_array($document['id'], $this->blacklist)): ?>
							<a class="button" href="<?php echo Route::url('index.php?option='.$this->option.'&task=addToBlackList&controller='. $this->controller . '&id=' . $document['id']); ?>"><?php echo Lang::txt('COM_SEARCH_ADD_BLACKLIST'); ?></a>
						<?php else: ?>
							<span><?php echo Lang::txt('COM_SEARCH_MARKED_FOR_REMOVAL'); ?></span>
						<?php endif; ?>

					</td>
			</tr>
			<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11">
				<?php echo $this->pagination; ?>
			</td>
		</tr>
	</tfoot>
</table>
