<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Cron\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_CRON') . ': ' . Lang::txt('COM_CRON_RUN'), 'cron');

function prettyPrint($json)
{
	$result = '';
	$level = 0;
	$prev_char = '';
	$in_quotes = false;
	$ends_line_level = null;
	$json_length = strlen($json);

	for ($i = 0; $i < $json_length; $i++)
	{
		$char = $json[$i];
		$new_line_level = null;
		$post = "";
		if ($ends_line_level !== null)
		{
			$new_line_level  = $ends_line_level;
			$ends_line_level = null;
		}
		if ($char === '"' && $prev_char != '\\')
		{
			$in_quotes = !$in_quotes;
		}
		else if (! $in_quotes)
		{
			switch ($char)
			{
				case '}':
				case ']':
					$level--;
					$ends_line_level = null;
					$new_line_level  = $level;
				break;

				case '{':
				case '[':
					$level++;

				case ',':
					$ends_line_level = $level;
				break;

				case ':':
					$post = ' ';
				break;

				case " ":
				case "\t":
				case "\n":
				case "\r":
					$char = '';
					$ends_line_level = $new_line_level;
					$new_line_level  = null;
				break;
			}
		}
		if ($new_line_level !== null)
		{
			$result .= "\n" . str_repeat("\t", $new_line_level);
		}
		$result .= $char . $post;
		$prev_char = $char;
	}

	return $result;
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<tbody>
			<tr>
				<td>
<pre>
<?php echo str_replace("\t", ' &nbsp; &nbsp;', prettyPrint(json_encode($this->output))); ?>
</pre>
				</td>
			</tr>
		</tbody>
	</table>

	<?php echo Html::input('token'); ?>
</form>