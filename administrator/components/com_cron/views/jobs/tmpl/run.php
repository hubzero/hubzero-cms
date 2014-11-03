<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = CronHelperPermissions::getActions('component');

JToolBarHelper::title(JText::_('COM_CRON') . ': ' . JText::_('COM_CRON_RUN'), 'cron.png');

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

<form action="index.php" method="post" name="adminForm" id="adminForm">

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

	<?php echo JHTML::_('form.token'); ?>
</form>