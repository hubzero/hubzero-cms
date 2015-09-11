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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Cron\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_CRON') . ': ' . Lang::txt('COM_CRON_RUN'), 'cron.png');

function prettyPrint($json)
{
	$result = '';
	$level = 0;
	$prev_char = '';
	$in_quotes = false;
	$ends_line_level = NULL;
	$json_length = strlen($json);

	for ($i = 0; $i < $json_length; $i++)
	{
		$char = $json[$i];
		$new_line_level = NULL;
		$post = "";
		if ($ends_line_level !== NULL)
		{
			$new_line_level  = $ends_line_level;
			$ends_line_level = NULL;
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
					$ends_line_level = NULL;
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
					$new_line_level  = NULL;
				break;
			}
		}
		if ($new_line_level !== NULL)
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