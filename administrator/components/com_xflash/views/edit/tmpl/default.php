<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
JToolBarHelper::title( JText::_( 'Flash Banner Manager' ), 'addedit.png' );
JToolBarHelper::preferences('com_xflash', '550');
JToolBarHelper::save('savedata', 'Save changes');

$slides = array();
foreach ($this->xml->Slides[0]->Slide as $slide)
{
	$slides[] = $slide;
	foreach ($slides as $s)
	{
		if ($s->sType != 'regular') {
			$s->style = 'style="display:none"';
		} else {
			$s->style = 'style="display:table-row"';
		}
	}

}
$res = array();
if ($this->xml->Resources[0]) {
	foreach ($this->xml->Resources[0]->Resource as $resource)
	{
		$res[] = $resource;
	}
}
jimport('joomla.html.pane');
jimport('joomla.application.module.helper');
$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}

function setType(optionSelected, slNum) 
{ 
	if (optionSelected!=0) { // quote or resource
		$("h"+slNum).style.display = 'none';  
		$("st"+slNum).style.display = 'none';
		$("d"+slNum).style.display = 'none';  
		$("u"+slNum).style.display = 'none';   
		$("ii"+slNum).style.display = 'none';      
	} else {
		$("h"+slNum).style.display = 'table-row';  
		$("st"+slNum).style.display = 'table-row'; 
		$("d"+slNum).style.display = 'table-row';
		$("u"+slNum).style.display = 'table-row';    
		$("ii"+slNum).style.display = 'table-row'; 
	}
}
</script>
<form action="index.php" method="post" name="adminForm">
	<div class="col width-70" >
		<fieldset class="adminform">
			<div id="xflash-container" style="background: transparent url('/templates/<?php echo $this->template ?>/images/home/feature-background.jpg') 0 0 repeat-x;height:230px;width:600px;">
				<!-- FLASH OBJECT -->
			</div>
          
			<table class="admintable">
				<tbody>
					<tr>
						<th class="xflash_hed_big"><?php echo JText::_('SLIDES'); ?></th>
						<td class="xflash_hed_tips"><?php echo JText::_('SLIDES_TIPS'); ?></td>
					</tr>
          <?php for ($i=0;$i<9; $i++) { // draw input fields for all slides ?>
					<tr>
						<th colspan="2" class="slidenum" id="sn<?php echo $i; ?>"><?php echo strtoupper(JText::_('SLIDE')); ?> <?php echo ($i+1); ?></th>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('TYPE'); ?>:</label></td>
						<td>
							<select name="slide[<?php echo $i; ?>][sType]" onchange="setType(this.selectedIndex, <?php echo $i; ?>);">
								<option value="regular"<?php if (isset($slides[$i]) && $slides[$i]->sType=="regular" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('REGULAR'); ?></option>
								<option value="quote"<?php if (isset($slides[$i]) && $slides[$i]->sType=="quote" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('QUOTE'); ?></option>
								<option value="resource"<?php if (isset($slides[$i]) && $slides[$i]->sType=="resource" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('RESOURCE'); ?></option>
							</select>
						</td>
					</tr>
 					<tr id="h<?php echo $i; ?>" <?php if (isset($slides[$i])) { echo $slides[$i]->style; } ?>>
						<td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
						<td><input type="text" maxlength="40" name="slide[<?php echo $i; ?>][header]"  value="<?php if (isset($slides[$i])) { echo htmlspecialchars($slides[$i]->header); } ?>" /></td>
					</tr>
					<tr id="st<?php echo $i; ?>" <?php if (isset($slides[$i])) { echo $slides[$i]->style; } ?>>
						<td class="key"><label><?php echo JText::_('SUBTITLE'); ?>:</label></td>
						<td><input type="text" maxlength="80" name="slide[<?php echo $i; ?>][subtitle]"  value="<?php if (isset($slides[$i])) { echo htmlspecialchars($slides[$i]->subtitle); } ?>" /></td>
					</tr>
 					<tr id="d<?php echo $i; ?>" <?php if (isset($slides[$i])) { echo $slides[$i]->style; } ?>>
						<td class="key"><label><?php echo JText::_('DESC'); ?>:</label></td>
						<td><input type="text" maxlength="278" name="slide[<?php echo $i; ?>][body]" value="<?php if (isset($slides[$i])) { echo htmlspecialchars($slides[$i]->body); } ?>" /></td>
					</tr>
					<tr id="u<?php echo $i; ?>" <?php if (isset($slides[$i])) { echo $slides[$i]->style; } ?>>
						<td class="key"><label><?php echo JText::_('URL'); ?>:</label></td>
						<td><input type="text" maxlength="278" name="slide[<?php echo $i; ?>][path]"  value="<?php if (isset($slides[$i])) { echo $slides[$i]->path; } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('URL_LABEL'); ?>:</label></td>
						<td><input type="text" maxlength="278" name="slide[<?php echo $i; ?>][pathLabel]"  value="<?php if (isset($slides[$i])) { echo htmlspecialchars($slides[$i]->pathLabel); } ?>" /></td>
					</tr>
					<tr id="ii<?php echo $i; ?>" <?php if (isset($slides[$i])) { echo $slides[$i]->style; } ?>>
						<td class="key"><label><?php echo JText::_('IMA'); ?>:</label></td>
						<td><input type="text" name="slide[<?php echo $i; ?>][ima]" value="<?php if (isset($slides[$i])) { echo $slides[$i]->ima; } ?>" /><span class="xflash_note"><?php echo JText::_('IMA_TIPS'); ?></span></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('BG_IMA'); ?>:</label></td>
						<td><input type="text" name="slide[<?php echo $i; ?>][bg]" value="<?php if (isset($slides[$i])) { echo $slides[$i]->bg; } ?>" /><span class="xflash_note"><?php echo JText::_('BG_TIPS'); ?></span></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('BG_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="slide[<?php echo $i; ?>][bgcol]" class="minor" value="<?php if (isset($slides[$i])) { echo substr($slides[$i]->bgcol, 2); } ?>" /> <span class="xflash_note"><?php echo JText::_('BG_COL_TIPS'); ?></span></td>
					</tr>
 					<tr>
						<td class="key"><label><?php echo JText::_('TITLE_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="slide[<?php echo $i; ?>][headerCol]" class="minor" value="<?php if (isset($slides[$i]) && $slides[$i]->headerCol!="" ) { echo substr($slides[$i]->headerCol, 2); } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('SUBTITLE_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="slide[<?php echo $i; ?>][subtitleCol]" class="minor" value="<?php if (isset($slides[$i]) && $slides[$i]->subtitleCol!="" ) { echo substr($slides[$i]->subtitleCol, 2); } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('DESC_COL'); ?>:</label></td>
						<td><input type="text" maxlength="6" name="slide[<?php echo $i; ?>][bodyCol]" class="minor" value="<?php if (isset($slides[$i]) && $slides[$i]->bodyCol!="" ) { echo substr($slides[$i]->bodyCol, 2); } ?>" /></td>
					</tr>
           <?php } ?>
  					<tr>
						<th class="xflash_hed_big"><?php echo JText::_('QUOTES'); ?></th>
						<td class="xflash_hed_tips"><?php echo JText::_('QUOTES_TIPS_1'); ?> <a href="index2.php?option=com_feedback&type=selected"><?php echo JText::_('FEEDBACK'); ?></a> <?php echo JText::_('QUOTES_TIPS_2'); ?></td>
					</tr>
					<tr>
						<th class="xflash_hed_big"><?php echo JText::_('FEATURED'); ?></th>
						<td class="xflash_hed_tips"><?php echo JText::_('FEATURED_TIPS'); ?></td>
					</tr>
           <?php for ($k=0;$k < $this->num_featured; $k++) { // draw input fields for all resources ?>
 					<tr>
						<th colspan="2" class="slidenum"><?php echo strtoupper(JText::_('RESOURCE')); ?> <?php echo ($k+1); ?></th>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('RES_ID'); ?>:</label></td>
						<td><input type="text" maxlength="10" name="res[<?php echo $k; ?>][rid]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->rid); } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('ALIAS'); ?>:</label></td>
						<td><input type="text" maxlength="35" name="res[<?php echo $k; ?>][alias]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->alias); } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
						<td><input type="text" maxlength="40" name="res[<?php echo $k; ?>][rTitle]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->rTitle); } ?>" /></td>
					</tr>
 					<tr>
						<td class="key"><label><?php echo JText::_('CATEGORY'); ?>:</label></td>
						<td> 
							<select name="res[<?php echo $k; ?>][category]">
								<option value="tools"<?php if (isset($res[$k]) && $res[$k]->category=="tools" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOLS'); ?></option>
								<option value="topics"<?php if (isset($res[$k]) && $res[$k]->category=="topics" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('TOPICS'); ?></option>
								<option value=""<?php if (isset($res[$k]) && $res[$k]->category=="" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('GENERAL'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('TAGLINE'); ?>:</label></td>
						<td><input type="text" maxlength="80" name="res[<?php echo $k; ?>][tagline]"  value="<?php if (isset($res[$k])) { echo htmlspecialchars( $res[$k]->tagline); } ?>" /></td>
					</tr>
 					<tr>
						<td class="key"><label><?php echo JText::_('DESC'); ?>:</label></td>
						<td><input type="text" maxlength="278" name="res[<?php echo $k; ?>][body]"  value="<?php if (isset($res[$k])) { echo htmlspecialchars( $res[$k]->body); } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('IMA'); ?>:</label></td>
						<td><input type="text"  name="res[<?php echo $k; ?>][ima]"  value="<?php if (isset($res[$k])) { echo htmlspecialchars( $res[$k]->ima); } ?>" /><span class="xflash_note"><?php echo JText::_('PATH_TIPS'); ?></span></td>
					</tr>
           <?php } ?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-30">
	<?php echo $pane->startPane("content-pane"); ?>
	<?php echo $pane->startPanel( 'Main settings', 'cpanel-panel-xflashprefs' ); ?>	
		<fieldset class="adminform">
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('BORDER_COLOR'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[sBorderCol]" class="minor" value="<?php  echo substr($this->xml->Prefs[0]->sBorderCol, 2);  ?>" /><span class="xflash_note"><?php echo JText::_('BORDER_TIPS'); ?></span></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('BUTTON_ALPHA'); ?>:</label></td>
						<td>
							<select name="prefs[bAlphaOff]">
								<option value="30"<?php if ($this->xml->Prefs[0]->bAlphaOff == '30') { echo ' selected="selected"'; } ?>>30</option>
								<option value="50"<?php if ($this->xml->Prefs[0]->bAlphaOff == '50') { echo ' selected="selected"'; } ?>>50</option>
								<option value="75"<?php if ($this->xml->Prefs[0]->bAlphaOff == '75') { echo ' selected="selected"'; } ?>>75</option>
								<option value="100"<?php if ($this->xml->Prefs[0]->bAlphaOff == '100') { echo ' selected="selected"'; } ?>>100</option>
							</select>
							<span class="xflash_note"><?php echo JText::_('ALPHA_TIPS'); ?></span>
						</td>
					</tr>      
 					<tr>
						<td class="key"><label><?php echo JText::_('BUTTON_ROLL_ALPHA'); ?>:</label></td>
						<td>
							<select name="prefs[bAlphaOn]">
								<option value="40"<?php if ($this->xml->Prefs[0]->bAlphaOn == '40') { echo ' selected="selected"'; } ?>>40</option>
								<option value="75"<?php if ($this->xml->Prefs[0]->bAlphaOn == '75') { echo ' selected="selected"'; } ?>>75</option>
								<option value="100"<?php if ($this->xml->Prefs[0]->bAlphaOn == '100') { echo ' selected="selected"'; } ?>>100</option>
							</select>
							<span class="xflash_note"><?php echo JText::_('ALPHA_TIPS'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('NAV_BUTTON_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[bColOff]"  value="<?php if ($this->xml->Prefs[0]->bColOff) { echo substr($this->xml->Prefs[0]->bColOff, 2); } else { echo $this->defaults['bColOff']; } ?>" /></td>
					</tr> 
					<tr>
						<td class="key"><label><?php echo JText::_('NAV_BUTTON_ON_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[bColOn]"  value="<?php if ($this->xml->Prefs[0]->bColOn) { echo substr($this->xml->Prefs[0]->bColOn, 2); } else { echo $this->defaults['bColOn']; } ?>" /></td>
					</tr>   
					<tr>
						<td class="key"><label><?php echo JText::_('NAV_BUTTON_SEL_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[bSelected]"  value="<?php if ($this->xml->Prefs[0]->bSelected) { echo substr($this->xml->Prefs[0]->bSelected, 2); } else { echo $this->defaults['bSelected']; } ?>" /></td>
					</tr>     
 					<tr>
						<td class="key"><label><?php echo JText::_('NAV_BUTTON_TEXT_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[bTextOff]"  value="<?php if ($this->xml->Prefs[0]->bTextOff) { echo substr($this->xml->Prefs[0]->bTextOff, 2); } else { echo $this->defaults['bTextOff']; } ?>" /></td>
					</tr>                       
 					<tr>
						<td class="key"><label><?php echo JText::_('NAV_BUTTON_TEXT_ON_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[bTextOn]"  value="<?php if ($this->xml->Prefs[0]->bTextOn) { echo substr($this->xml->Prefs[0]->bTextOn, 2); } else { echo $this->defaults['bTextOn']; } ?>" /><input type="hidden" name="prefs[transition]" value="<?php echo $this->defaults['transition']; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('TRANSITION_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[transitionCol]"  value="<?php if ($this->xml->Prefs[0]->transitionCol) { echo substr($this->xml->Prefs[0]->transitionCol, 2); } else { echo $this->defaults['transitionCol']; } ?>" /></td>
					</tr>      
					<tr>
						<td class="key"><label><?php echo JText::_('ROTATION'); ?>:</label></td>
						<td>
							<select name="prefs[countdown]">
 								<option value="5000"<?php if ($this->xml->Prefs[0]->countdown == '5000') { echo ' selected="selected"'; } ?>>5</option>
 								<option value="6000"<?php if ($this->xml->Prefs[0]->countdown == '6000') { echo ' selected="selected"'; } ?>>6</option>
 								<option value="7000"<?php if ($this->xml->Prefs[0]->countdown == '7000') { echo ' selected="selected"'; } ?>>7</option>
 								<option value="8000"<?php if ($this->xml->Prefs[0]->countdown == '8000') { echo ' selected="selected"'; } ?>>8</option>
 								<option value="9000"<?php if ($this->xml->Prefs[0]->countdown == '9000') { echo ' selected="selected"'; } ?>>9</option>
 								<option value="10000"<?php if ($this->xml->Prefs[0]->countdown == '10000') { echo ' selected="selected"'; } ?>>10</option>
							</select> seconds
							<input type="hidden" name="prefs[imagePath]" value="<?php echo $this->defaults['imagePath']; ?>" />
							<input type="hidden" name="prefs[serverPath]" value="<?php echo $this->defaults['serverPath']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('BG_GRAPHIC'); ?>:</label></td>
						<td>
							<select name="prefs[watermark]">
 								<option value="waves"<?php if ($this->xml->Prefs[0]->watermark == 'waves') { echo ' selected="selected"'; } ?>><?php echo JText::_('WAVES'); ?></option>
								<option value="spikes"<?php if ($this->xml->Prefs[0]->watermark == 'spikes') { echo ' selected="selected"'; } ?>><?php echo JText::_('SPIKES'); ?></option>
								<option value="stripes"<?php if ($this->xml->Prefs[0]->watermark == 'stripes') { echo ' selected="selected"'; } ?>><?php echo JText::_('STRIPES'); ?></option>
 								<option value=""<?php if ($this->xml->Prefs[0]->watermark == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('NONE'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('TITLE_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[headerCol]"  value="<?php if ($this->xml->Prefs[0]->headerCol) { echo substr($this->xml->Prefs[0]->headerCol, 2); } else { echo $this->defaults['headerCol']; } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('SUBTITLE_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[subtitleCol]"  value="<?php if ($this->xml->Prefs[0]->subtitleCol) { echo substr($this->xml->Prefs[0]->subtitleCol, 2); } else { echo $this->defaults['subtitleCol']; } ?>" /></td>
					</tr>  
					<tr>
						<td class="key"><label><?php echo JText::_('DESC_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[bodyCol]"  value="<?php if ($this->xml->Prefs[0]->bodyCol) { echo substr($this->xml->Prefs[0]->bodyCol, 2); } else { echo $this->defaults['bodyCol']; } ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('HYPERLINK_COL'); ?> (#):</label></td>
						<td><input type="text" maxlength="6" name="prefs[linkCol]"  value="<?php if ($this->xml->Prefs[0]->linkCol) { echo substr($this->xml->Prefs[0]->linkCol, 2); } else { echo $this->defaults['linkCol']; } ?>" /></td>
					</tr>                
					<tr>  
						<td class="key"><label><?php echo JText::_('HEAD_FONT'); ?>:</label></td>
						<td>
							<select name="prefs[headerFont]">
								<option value="Arial"<?php if ($this->xml->Prefs[0]->headerFont == 'Arial') { echo ' selected="selected"'; } ?>>Arial</option>
								<option value="Century Gothic"<?php if ($this->xml->Prefs[0]->headerFont == 'Century Gothic') { echo ' selected="selected"'; } ?>>Century Gothic</option>
								<option value="Helvetica"<?php if ($this->xml->Prefs[0]->headerFont == 'Helvetica') { echo ' selected="selected"'; } ?>>Helvetica</option>
								<option value="Trebuchet MS"<?php if ($this->xml->Prefs[0]->headerFont == 'Trebuchet MS') { echo ' selected="selected"'; } ?>>Trebuchet MS</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('DESC_FONT'); ?>:</label></td>
						<td>
							<select name="prefs[bodyFont]">
								<option value="Arial"<?php if ($this->xml->Prefs[0]->bodyFont == 'Arial') { echo ' selected="selected"'; } ?>>Arial</option>
								<option value="Century Gothic"<?php if ($this->xml->Prefs[0]->bodyFont == 'Century Gothic') { echo ' selected="selected"'; } ?>>Century Gothic</option>
								<option value="Helvetica"<?php if ($this->xml->Prefs[0]->bodyFont == 'Helvetica') { echo ' selected="selected"'; } ?>>Helvetica</option>
								<option value="Trebuchet MS"<?php if ($this->xml->Prefs[0]->bodyFont == 'Trebuchet MS') { echo ' selected="selected"'; } ?>>Trebuchet MS</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('FONT_SIZE_TITLE'); ?>:</label></td>
						<td>
							<select name="prefs[headerSize]">
								<option value="18"<?php if ($this->xml->Prefs[0]->headerSize == '18') { echo ' selected="selected"'; } ?>>18</option>
								<option value="20"<?php if ($this->xml->Prefs[0]->headerSize == '20') { echo ' selected="selected"'; } ?>>20</option>
								<option value="22"<?php if ($this->xml->Prefs[0]->headerSize == '22') { echo ' selected="selected"'; } ?>>22</option>
								<option value="24"<?php if ($this->xml->Prefs[0]->headerSize == '24') { echo ' selected="selected"'; } ?>>24</option>
								<option value="26"<?php if ($this->xml->Prefs[0]->headerSize == '26') { echo ' selected="selected"'; } ?>>26</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('FONT_SIZE_SUBTITLE'); ?>:</label></td>
						<td>
							<select name="prefs[subtitleSize]">
								<option value="10"<?php if ($this->xml->Prefs[0]->subtitleSize == '10') { echo ' selected="selected"'; } ?>>10</option>
								<option value="12"<?php if ($this->xml->Prefs[0]->subtitleSize == '12') { echo ' selected="selected"'; } ?>>12</option>
								<option value="14"<?php if ($this->xml->Prefs[0]->subtitleSize == '14') { echo ' selected="selected"'; } ?>>14</option>
								<option value="18"<?php if ($this->xml->Prefs[0]->subtitleSize == '18') { echo ' selected="selected"'; } ?>>18</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('FONT_SIZE_DESC'); ?>:</label></td>
						<td>
							<select name="prefs[bodySize]">
								<option value="10"<?php if ($this->xml->Prefs[0]->bodySize == '10') { echo ' selected="selected"'; } ?>>10</option>
								<option value="11"<?php if ($this->xml->Prefs[0]->bodySize == '11') { echo ' selected="selected"'; } ?>>11</option>
								<option value="12"<?php if ($this->xml->Prefs[0]->bodySize == '12') { echo ' selected="selected"'; } ?>>12</option>
								<option value="13"<?php if ($this->xml->Prefs[0]->bodySize == '13') { echo ' selected="selected"'; } ?>>13</option>
								<option value="14"<?php if ($this->xml->Prefs[0]->bodySize == '14') { echo ' selected="selected"'; } ?>>14</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="savedata" />
		<?php echo $pane->endPanel(); ?>
		<?php echo $pane->startPanel( 'Images', 'cpanel-panel-images' );?>	
			<iframe width="100%" height="500" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;task=media&amp;no_html=1"></iframe>
		<?php echo $pane->endPanel(); ?>
		<?php echo $pane->endPane(); ?>
	</div>
	<div class="clr"></div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
