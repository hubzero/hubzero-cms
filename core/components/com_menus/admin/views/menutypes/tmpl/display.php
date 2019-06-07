<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<script type="text/javascript">
	setmenutype = function(type)
	{
		window.parent.Hubzero.submitbutton('items.setType', type);
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
