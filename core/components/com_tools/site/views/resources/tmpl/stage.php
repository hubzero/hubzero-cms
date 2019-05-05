<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
$html .= ($this->version == 'current' && $this->status['published']) ? ' <span><a href="'.Route::url('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.$this->stage.'&app=' . $this->row->alias) . '">' . Lang::txt('COM_TOOLS_CONTRIBTOOL_CHANGE_UPCOMING_VERSION') . '</a></span>' : '';
$html .= '</p>';

echo $html;
?>
<div class="clear"></div>
