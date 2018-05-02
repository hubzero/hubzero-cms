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

$script  = 'function insertPagebreak() {'."\n\t";
// Get the pagebreak title
$script .= 'var title = document.getElementById("title").value;'."\n\t";
$script .= 'if (title != \'\') {'."\n\t\t";
$script .= 'title = "title=\""+title+"\" ";'."\n\t";
$script .= '}'."\n\t";
// Get the pagebreak toc alias -- not inserting for now
// don't know which attribute to use...
$script .= 'var alt = document.getElementById("alt").value;'."\n\t";
$script .= 'if (alt != \'\') {'."\n\t\t";
$script .= 'alt = "alt=\""+alt+"\" ";'."\n\t";
$script .= '}'."\n\t";
$script .= 'var tag = "<hr class=\"system-pagebreak\" "+title+" "+alt+"/>";'."\n\t";
$script .= 'window.parent.jInsertEditorText(tag, \''.$this->eName.'\');'."\n\t";
$script .= 'window.parent.$.fancybox.close();'."\n\t";
$script .= 'return false;'."\n";
$script .= '}'."\n";

Document::addScriptDeclaration($script);
?>
	<form>
		<table>
			<tr>
				<th>
					<label for="title">
						<?php echo Lang::txt('COM_CONTENT_PAGEBREAK_TITLE'); ?>
					</label>
				</th>
				<td>
					<input type="text" id="title" name="title" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="alias">
						<?php echo Lang::txt('COM_CONTENT_PAGEBREAK_TOC'); ?>
					</label>
				</th>
				<td>
					<input type="text" id="alt" name="alt" />
				</td>
			</tr>
		</table>
	</form>
	<button onclick="insertPagebreak();"><?php echo Lang::txt('COM_CONTENT_PAGEBREAK_INSERT_BUTTON'); ?></button>
