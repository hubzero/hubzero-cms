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
?>

<ul class="file-selector" id="file-selector">
	<?php $id = 'dir-' . strtolower(\Components\Projects\Helpers\Html::generateCode(5, 5, 0, 1, 1)); ?>
	<li class="type-folder collapsed connection" id="<?php echo $id; ?>" data-connection="-1" data-path=".">
		<span class="item-info"></span>
		<span class="item-wrap">
			<span class="collapsor">&nbsp;</span>
			<img src="/core/plugins/filesystem/local/assets/img/icon.png" alt="" />
			<span title=""><?php echo $this->model->get('title'); ?> Master Repository</span>
		</span>
	</li>

	<?php foreach ($this->connections as $connection) : ?>
		<?php $imgRel = '/plugins/filesystem/' . $connection->provider->alias . '/assets/img/icon.png'; ?>
		<?php $img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel; ?>
		<?php $id = 'dir-' . strtolower(\Components\Projects\Helpers\Html::generateCode(5, 5, 0, 1, 1)); ?>
		<li class="type-folder collapsed connection" id="<?php echo $id; ?>" data-connection="<?php echo $connection->id ?>" data-path=".">
			<span class="item-info"></span>
			<span class="item-wrap">
				<span class="collapsor">&nbsp;</span>
				<img src="<?php echo $img; ?>" alt="" />
				<span title=""><?php echo $connection->name; ?></span>
			</span>
		</li>
	<?php endforeach; ?>
</ul>
