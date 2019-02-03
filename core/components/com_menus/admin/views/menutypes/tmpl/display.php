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
?>
<script type="text/javascript">
	setmenutype = function(type)
	{
		window.parent.Joomla.submitbutton('items.setType', type);
		window.parent.$.fancybox.close();
	}
</script>

<h2 class="modal-title"><?php echo Lang::txt('COM_MENUS_TYPE_CHOOSE'); ?></h2>
<ul class="menu_types">
	<?php foreach ($this->types as $name => $list): ?>
		<li>
			<dl class="menu_type">
				<dt><?php echo Lang::txt($name);?></dt>
				<dd><ul>
						<?php foreach ($list as $item): ?>
						<li><a class="choose_type" href="#" title="<?php echo Lang::txt($item->description); ?>"
								onclick="javascript:setmenutype('<?php echo base64_encode(json_encode(array('id' => $this->recordId, 'title' => $item->title, 'request' => $item->request))); ?>')">
								<?php echo Lang::txt($item->title);?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</dd>
			</dl>
		</li>
	<?php endforeach; ?>
	<li>
		<dl class="menu_type">
			<dt><?php echo Lang::txt('COM_MENUS_TYPE_SYSTEM'); ?></dt>
			<dd>
				<ul>
					<li>
						<a class="choose_type" href="#" title="<?php echo Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL_DESC'); ?>"
							onclick="javascript:setmenutype('<?php echo base64_encode(json_encode(array('id' => $this->recordId, 'title'=>'url'))); ?>')">
							<?php echo Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL'); ?>
						</a>
					</li>
					<li>
						<a class="choose_type" href="#" title="<?php echo Lang::txt('COM_MENUS_TYPE_ALIAS_DESC'); ?>"
							onclick="javascript:setmenutype('<?php echo base64_encode(json_encode(array('id' => $this->recordId, 'title'=>'alias'))); ?>')">
							<?php echo Lang::txt('COM_MENUS_TYPE_ALIAS'); ?>
						</a>
					</li>
					<li>
						<a class="choose_type" href="#"  title="<?php echo Lang::txt('COM_MENUS_TYPE_SEPARATOR_DESC'); ?>"
							onclick="javascript:setmenutype('<?php echo base64_encode(json_encode(array('id' => $this->recordId, 'title'=>'separator'))); ?>')">
							<?php echo Lang::txt('COM_MENUS_TYPE_SEPARATOR'); ?>
						</a>
					</li>
				</ul>
			</dd>
		</dl>
	</li>
</ul>
