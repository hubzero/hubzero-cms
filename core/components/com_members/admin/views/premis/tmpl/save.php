<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('COM_MEMBERS_PREMIS'), 'user.png');
if ($canDo->get('core.edit'))
{
	Toolbar::addNew();
	Toolbar::editList();
	Toolbar::deleteList();
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php
	}
	else
	{
		echo '<p>Import complete</p>';

		echo '<p>Total records processed: ' . ($this->ok + $this->fail) . '<br>';
		echo 'Successfully processed: ' . $this->ok . '<br>';
		echo 'Errors processing: ' . $this->fail . '</p>';

		if ($this->fail)
		{
			echo '<h4>Error log:</h4>';
			echo '<p id="report">';
			foreach ($this->report as $line)
			{
				if ($line['status'] != 'ok')
				{
					echo 'Line ' . $line['line'] . ': ' .  $line['msg'] . '<br>';
				}
			}
			echo '</p>';
		}
	}

