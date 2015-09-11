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

// No direct access.
defined('_HZEXEC_') or die();

$this->css('component.css')
     ->js('create.js');

$base = rtrim(Request::base(true), '/');

if (!$this->allowupload) { ?>
	<p class="warning">
		<?php echo Lang::txt('COM_TOOLS_SUPPORTING_DOCS_ONLY_CURRENT'); ?>
	</p>
<?php } ?>

<?php $this->allowupload = true; ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
		<fieldset>
			<label for="upload">
				<input type="file" class="option" name="upload" id="upload" />
				<input type="submit" class="option" value="<?php echo strtolower(Lang::txt('COM_TOOLS_UPLOAD')); ?>" />
			</label>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
		</fieldset>
	</form>

<?php if ($this->getError()) { ?>
	<p class="error">
		<?php echo implode('<br />', $this->getErrors()); ?>
	</p>
<?php } ?>

<?php
$out = '';
// loop through children and build list
if ($this->children)
{
	$base = $this->cparams->get('uploadpath');

	$k = 0;
	$i = 0;
	$files = array(13,15,26,33,35,38);
	$n = count($this->children);

	if ($this->allowupload)
	{
		$out .= '<p>'.Lang::txt('COM_TOOLS_ATTACH_EDIT_TITLE_EXPLANATION').'</p>'."\n";
	}
	$out .= '<table class="list">'."\n";

	foreach ($this->children as $child)
	{
		$k++;

		// figure ou the URL to the file
		switch ($child->type)
		{
			case 12:
				if ($child->path)
				{
					// internal link, not a resource
					$url = $child->path;
				}
				else
				{
					// internal link but a resource
					$url = 'index.php?option=com_resources&id='. $child->id;
				}
			break;

			default:
				$url = $child->path;
			break;
		}

		// figure out the file type so we can give it the appropriate CSS class
		$type = '';
		$liclass = '';

		$type = Filesystem::extension($url);
		$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;
		if ($child->type == 12)
		{
			$liclass = ' class="ftitle html';
		}
		else
		{
			$type = ($type) ? $type : 'html';
			$liclass = ' class="ftitle '.$type;
		}

		$out .= ' <tr>';
		$out .= '  <td width="100%">';
		if ($this->allowupload)
		{
			$out .= '<span'.$liclass.' item:name id:'.$child->id.'" data-id="' . $child->id . '">'.$this->escape($child->title).'</span><br /><span class="caption">(<a href="'.Route::url('index.php?option=com_resources&task=download&id='.$child->id).'" title="'.$child->title.'">'.\Components\Tools\Helpers\Html::getFileAttribs($url, $base).'</a>)</span>';
		}
		else
		{
			$out .= '<span><a href="'.Route::url('index.php?option=com_resources&task=download&id=' . $child->id).'">'.$this->escape($child->title).'</a></span>';
		}
		$out .='</td>';
		if ($this->allowupload)
		{
			$out .= '  <td class="d">';

			if ($i > 0 || ($i+0 > 0))
			{
				$out .= '<a href="'. $base . '/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;tmpl=component&amp;pid='.$this->resource->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=up" class="order up" title="'.Lang::txt('COM_TOOLS_MOVE_UP').'"><span>'.Lang::txt('COM_TOOLS_MOVE_UP').'</span></a>';
			}
			else
			{
				$out .= '&nbsp;';
			}
			$out .= '</td>';

			$out .= '  <td class="u">';

			if ($i < $n-1 || $i+0 < $n-1)
			{
				$out .= '<a href="'. $base . '/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;tmpl=component&amp;pid='.$this->resource->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=down" class="order down" title="'.Lang::txt('COM_TOOLS_MOVE_DOWN').'"><span>'.Lang::txt('COM_TOOLS_MOVE_DOWN').'</span></a>';
			}
			else
			{
				$out .= '&nbsp;';
			}
			$out .= '</td>';
			$out .= '  <td class="t"><a href="'. $base . '/index.php?option='.$this->option.'&amp;controller='.$this->controller.'&amp;task=delete&amp;tmpl=component&amp;id='.$child->id.'&amp;pid='.$this->resource->id.'" class="icon-delete delete"><span> ' . Lang::txt('COM_TOOLS_DELETE') . '</span></a></td>';
		}
		$out .= ' </tr>';

		$i++;
	}
	$out .= '</table>';
}
else
{
	$out .= '<p>'.Lang::txt('COM_TOOLS_ATTACH_NONE_FOUND').'</p>';
}
echo $out;