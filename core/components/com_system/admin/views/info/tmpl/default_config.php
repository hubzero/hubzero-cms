<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SETTING'); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->config as $key => $value):?>
				<tr>
					<td>
						<?php echo $key;?>
					</td>
					<td>
						<?php
						if (is_array($value))
						{
							foreach ($value as $ky => $val)
							{
								if (is_array($val))
								{
									foreach ($val as $k => $v)
									{
										echo htmlspecialchars($k, ENT_QUOTES) .' = ' . htmlspecialchars($v, ENT_QUOTES) . '<br />';
									}
								}
								else
								{
									echo htmlspecialchars($ky, ENT_QUOTES) .' = ' . htmlspecialchars($val, ENT_QUOTES) . '<br />';
								}
							}
						}
						else
						{
							echo htmlspecialchars($value, ENT_QUOTES);
						}
						?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</fieldset>
