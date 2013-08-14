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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');                              

//set title
JToolBarHelper::title('<a href="index.php?option='.$this->option.'">' . JText::_( 'Newsletter Tools' ) . '</a>', 'tools.png');
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	// do field validation
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend>Mozify Image</legend>
				<table class="admintable">
				<tbody>
					<tr>
						<td colspan="2">
							<span class="hint">Mozify means to turn an reproduce an image in mosaic tiles.</span>
						</td>
					</tr>
					<tr>
						<td>Image File</td>
						<td>
							<input type="file" name="image-file" />
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;font-weight:bold;font-size:16px">&mdash;&mdash;&mdash; or &mdash;&mdash;&mdash;</td>
					</tr>
					<tr>
						<td>Image URL</td>
						<td>
							<input type="text" name="image-url" />
						</td>
					</tr>
					<tr>
						<td>Mosaic Size</td>
						<td>
							<select name="mosaic-size">
								<option value="1">1</option>
								<option value="3">3</option>
								<option selected="selected" value="5">5</option>
								<option value="10">10</option>
								<option value="15">15</option>
								<option value="20">20</option>
								<option value="25">25</option>
								<option value="30">30</option>
								<option value="35">35</option>
								<option value="40">40</option>
								<option value="45">45</option>
								<option value="50">50</option>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" value="Submit" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<?php if ($this->code != '') : ?>
			<h3 style="padding-top: 0">Original</h3>
			<img src="<?php echo str_replace(JPATH_ROOT, '', $this->original); ?>" />
			
			<h3>Mozified</h3>
			<iframe id="preview-iframe" style="border:1px solid transparent"></iframe>
			<div id="preview-code" style="display:none"><?php echo $this->preview; ?></div>
			
			<h3>Code</h3>
			<textarea id="code"><?php echo str_replace("\n", "", $this->code); ?></textarea>
		
			<script>
				jQuery(document).ready(function($){
					//get iframe and mozified code
					var previewIframe = $('#preview-iframe'),
						previewCode = $('#preview-code').find('table').first();
					
					//set iframe height and width
					//add preview code to iframe
					previewIframe
						.css({
							width: previewCode.attr('width') + 'px',
							height: previewCode.attr('height') + 'px'
						})
						.contents().find('html').html( previewCode );
				});
			</script>
		<?php endif; ?>
	</div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="mozify" />
	<input type="hidden" name="boxchecked" value="0" />
</form>