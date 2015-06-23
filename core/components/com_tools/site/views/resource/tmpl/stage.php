<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->stages = array(
	Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_DESCRIPTION'),
	Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONTRIBUTORS'),
	Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_ATTACHMENTS'),
	Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_TAGS'),
	Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE')
);
$key = $this->stage-1;
?>
<div class="clear"></div>
<ol id="steps">
	<li><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_EDIT_PAGE_FOR') . ' ' . ($this->version=='dev' ? Lang::txt('COM_TOOLS_CONTRIBTOOL_TIP_NEXT_TOOL_RELEASE') : Lang::txt('COM_TOOLS_CONTRIBTOOL_TIP_CURRENT_VERSION')); ?>:</li>
	<?php
	for ($i=0, $n=count( $this->stages ); $i < $n; $i++)
	{
		$html  = "\t\t".' <li';
		if ($i==$key)
		{
			$html .= ' class="active"';
		}
		$html .= '>';

		if ($this->version=='dev' && $i!=$key && ($i+1)!= count($this->stages))
		{
			$html .='<a href="'.Route::url('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.($i+1).'&app=' . $this->row->alias).'">'.$this->stages[$i].'</a>';
		}
		else if ($this->version=='current' && $i!=$key && ($i+1)!= count($this->stages) && ($i==0 or $i==3 or $i==2))
		{
			$html .='<a href="'.Route::url('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.($i+1).'&app=' . $this->row->alias . '&editversion=current').'">'.$this->stages[$i].'</a>';
		}
		else
		{
			$html .= $this->stages[$i];
		}

		$html .= '</li>'."\n";

		echo $html;
	}
	?>
</ol>

<?php
$html = '<p class="';
if ($this->version=='dev')
{
	if ($this->vnum)
	{
		$html .= 'devversion">' . ucfirst(Lang::txt('COM_TOOLS_VERSION')) . ' ' . $this->vnum;
	}
	else
	{
		$html .= 'devversion">' . ucfirst(Lang::txt('COM_TOOLS_CONTRIBTOOL_NEXT_VERSION'));
	}
	$html .= ' - '.Lang::txt('COM_TOOLS_CONTRIBTOOL_NOT_PUBLISHED_YET');
}
else if ($this->version == 'current')
{
	$html .= 'currentversion">' . ucfirst(Lang::txt('COM_TOOLS_VERSION')) . ' ' . $this->vnum . ' - ' . Lang::txt('COM_TOOLS_CONTRIBTOOL_PUBLISHED_NOW');
}
$html .= ($this->version == 'dev' && $this->status['published']) ? ' <span><a href="'.Route::url('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.$this->stage.'&app=' . $this->row->alias . '&editversion=current') . '">' . Lang::txt('COM_TOOLS_CONTRIBTOOL_CHANGE_CURRENT_VERSION') . '</a></span>' : '';
$html .= ($this->version == 'current' && $this->status['published']) ? ' <span><a href="'.Route::url('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.$this->stage.'&app=' . $this->row->alias) . '">' . Lang::txt('COM_TOOLS_CONTRIBTOOL_CHANGE_UPCOMING_VERSION') . '</a></span>' : '' ;
$html .= '</p>';

echo $html;
?>
<div class="clear"></div>
