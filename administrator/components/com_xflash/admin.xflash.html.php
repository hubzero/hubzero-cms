<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class XFlashHTML 
{
	public function shortenText($text, $chars=300) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
		}

		return $text;
	}
	
	//----------
	
	public function formSelect($name, $array, $value, $class='', $js='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		foreach($array as $anode) 
		{
			$selected = ($anode == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode.'"'.$selected.'>'.$anode.'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}
	
	//-----------
	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function edit($xml, $url, $defaults, $option, $num_featured, $template) 
	{
		$slides = array();
		foreach ($xml->Slides[0]->Slide as $slide) 
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
		if ($xml->Resources[0]) {
			foreach ($xml->Resources[0]->Resource as $resource) 
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
		</script>
     <script language="javascript">
		function setType(optionSelected, slNum) { 
			if(optionSelected!=0) { // quote or resource
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
                   <div id="xflash-container" style="background: transparent url('/templates/<?php echo $template ?>/images/home/feature-background.jpg') 0 0 repeat-x;height:230px;width:600px;">
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
                     <option value="regular"<? if(isset($slides[$i]) && $slides[$i]->sType=="regular" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('REGULAR'); ?></option>
                  	 <option value="quote"<? if(isset($slides[$i]) && $slides[$i]->sType=="quote" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('QUOTE'); ?></option>
                     <option value="resource"<? if(isset($slides[$i]) && $slides[$i]->sType=="resource" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('RESOURCE'); ?></option>
                    </select>
                   </td>
				  </tr>
                   <tr id="h<?php echo $i; ?>" <?php if(isset($slides[$i])) { echo $slides[$i]->style; } ?>>
				   <td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
				   <td><input type="text" maxlength="40" name="slide[<?php echo $i; ?>][header]"  value="<?php if(isset($slides[$i])) { echo htmlspecialchars($slides[$i]->header); } ?>" />
                   </td>
				  </tr>
                  <tr id="st<?php echo $i; ?>" <?php if(isset($slides[$i])) { echo $slides[$i]->style; } ?>>
				   <td class="key"><label><?php echo JText::_('SUBTITLE'); ?>:</label></td>
				   <td><input type="text" maxlength="80" name="slide[<?php echo $i; ?>][subtitle]"  value="<?php if(isset($slides[$i])) { echo htmlspecialchars( $slides[$i]->subtitle); } ?>" />
                   </td>
				  </tr>
                   <tr id="d<?php echo $i; ?>" <?php if(isset($slides[$i])) { echo $slides[$i]->style; } ?>>
				   <td class="key"><label><?php echo JText::_('DESC'); ?>:</label></td>
				   <td><input type="text" maxlength="278" name="slide[<?php echo $i; ?>][body]" value="<?php if(isset($slides[$i])) { echo htmlspecialchars($slides[$i]->body); } ?>" />
                   </td>
				  </tr>
                  <tr id="u<?php echo $i; ?>" <?php if(isset($slides[$i])) { echo $slides[$i]->style; } ?>>
				   <td class="key"><label><?php echo JText::_('URL'); ?>:</label></td>
				   <td><input type="text" maxlength="278" name="slide[<?php echo $i; ?>][path]"  value="<?php if(isset($slides[$i])) { echo $slides[$i]->path; } ?>" />
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('URL_LABEL'); ?>:</label></td>
				   <td><input type="text" maxlength="278" name="slide[<?php echo $i; ?>][pathLabel]"  value="<?php if(isset($slides[$i])) { echo htmlspecialchars($slides[$i]->pathLabel); } ?>" />
                   </td>
				  </tr>
                  <tr id="ii<?php echo $i; ?>" <?php if(isset($slides[$i])) { echo $slides[$i]->style; } ?>>
				 	<td class="key"><label><?php echo JText::_('IMA'); ?>:</label></td>
				   <td>
                  <input type="text"  name="slide[<?php echo $i; ?>][ima]"  value="<?php if(isset($slides[$i])) { echo $slides[$i]->ima; } ?>" />
                  <span class="xflash_note"><?php echo JText::_('IMA_TIPS'); ?></span>
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('BG_IMA'); ?>:</label></td>
				   <td>
                  <input type="text"  name="slide[<?php echo $i; ?>][bg]"  value="<?php if(isset($slides[$i])) { echo $slides[$i]->bg; } ?>" />
                  <span class="xflash_note"><?php echo JText::_('BG_TIPS'); ?></span>
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('BG_COL'); ?> (#):</label></td>
				   <td>
                  <input type="text" maxlength="6" name="slide[<?php echo $i; ?>][bgcol]" class="minor" value="<?php if(isset($slides[$i])) { echo substr($slides[$i]->bgcol, 2); } ?>" /> <span class="xflash_note"><?php echo JText::_('BG_COL_TIPS'); ?></span>
                   </td>
				  </tr>
                   <tr>
				   <td class="key"><label><?php echo JText::_('TITLE_COL'); ?> (#):</label></td>
				   <td>
                  <input type="text" maxlength="6" name="slide[<?php echo $i; ?>][headerCol]" class="minor" value="<?php if(isset($slides[$i]) && $slides[$i]->headerCol!="" ) { echo substr($slides[$i]->headerCol, 2); } ?>" />
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('SUBTITLE_COL'); ?> (#):</label></td>
				   <td>
                  <input type="text" maxlength="6" name="slide[<?php echo $i; ?>][subtitleCol]" class="minor" value="<?php if(isset($slides[$i]) && $slides[$i]->subtitleCol!="" ) { echo substr($slides[$i]->subtitleCol, 2); } ?>" />
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('DESC_COL'); ?>:</label></td>
				   <td>
                  <input type="text" maxlength="6" name="slide[<?php echo $i; ?>][bodyCol]" class="minor" value="<?php if(isset($slides[$i]) && $slides[$i]->bodyCol!="" ) { echo substr($slides[$i]->bodyCol, 2); } ?>" />
                   </td>
				  </tr>
                   <?php } ?>
                    <tr>
				   <th class="xflash_hed_big"><?php echo JText::_('QUOTES'); ?></th>
				   <td class="xflash_hed_tips"><?php echo JText::_('QUOTES_TIPS_1'); ?> <a href="index2.php?option=com_feedback&type=selected"><?php echo JText::_('FEEDBACK'); ?></a> <?php echo JText::_('QUOTES_TIPS_2'); ?>
                   </td>
				  </tr>
                    <tr>
				   <th class="xflash_hed_big"><?php echo JText::_('FEATURED'); ?></th>
				   <td class="xflash_hed_tips"><?php echo JText::_('FEATURED_TIPS'); ?>
                   </td>
				  </tr>
                   <?php for ($k=0;$k < $num_featured; $k++) { // draw input fields for all resources ?>
                   <tr>
				    <th colspan="2" class="slidenum"><?php echo strtoupper(JText::_('RESOURCE')); ?> <?php echo ($k+1); ?></th>
                   </tr>
				  <tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('RES_ID'); ?>:</label></td>
				   <td><input type="text" maxlength="10" name="res[<?php echo $k; ?>][rid]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->rid); } ?>" />
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('ALIAS'); ?>:</label></td>
				   <td><input type="text" maxlength="35" name="res[<?php echo $k; ?>][alias]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->alias); } ?>" />
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('TITLE'); ?>:</label></td>
				   <td><input type="text" maxlength="40" name="res[<?php echo $k; ?>][rTitle]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->rTitle); } ?>" />
                   </td>
				  </tr>
                   <tr>
				   <td class="key"><label><?php echo JText::_('CATEGORY'); ?>:</label></td>
				   <td> 
                   	<select name="res[<?php echo $k; ?>][category]">
                    	<option value="tools"<? if(isset($res[$k]) && $res[$k]->category=="tools" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('TOOLS'); ?></option>
						<option value="topics"<? if(isset($res[$k]) && $res[$k]->category=="topics" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('TOPICS'); ?></option>
                        <option value=""<? if(isset($res[$k]) && $res[$k]->category=="" ) { echo ' selected="selected"'; } ?>><?php echo JText::_('GENERAL'); ?></option>
                    </select>
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('TAGLINE'); ?>:</label></td>
				   <td><input type="text" maxlength="80" name="res[<?php echo $k; ?>][tagline]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->tagline); } ?>" />
                   </td>
				  </tr>
                   <tr>
				   <td class="key"><label><?php echo JText::_('DESC'); ?>:</label></td>
				   <td><input type="text" maxlength="278" name="res[<?php echo $k; ?>][body]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->body); } ?>" />
                   </td>
				  </tr>
                  <tr>
				   <td class="key"><label><?php echo JText::_('IMA'); ?>:</label></td>
				   <td><input type="text"  name="res[<?php echo $k; ?>][ima]"  value="<?php if(isset($res[$k])) { echo htmlspecialchars( $res[$k]->ima); } ?>" />
                  <span class="xflash_note"><?php echo JText::_('PATH_TIPS'); ?></span>
                   </td>
				  </tr>
                   <?php } ?>
                 </tbody>
                </table>
			</fieldset>
			
			</div>
			<div class="col width-30">
            <?php echo $pane->startPane("content-pane"); ?>
			<?php echo $pane->startPanel( 'Main settings', 'cpanel-panel-xflashprefs' );?>	
			<fieldset class="adminform">
				<table class="admintable">
				 <tbody>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('BORDER_COLOR'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[sBorderCol]" class="minor" value="<?php  echo substr($xml->Prefs[0]->sBorderCol, 2);  ?>" />
                  		<span class="xflash_note"><?php echo JText::_('BORDER_TIPS'); ?></span>
                   </td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('BUTTON_ALPHA'); ?>:</label></td>
				   	<td>
                   		<select name="prefs[bAlphaOff]">
                     		<option value="30"<? if($xml->Prefs[0]->bAlphaOff == '30') { echo ' selected="selected"'; } ?>>30</option>
                    		 <option value="50"<? if($xml->Prefs[0]->bAlphaOff == '50') { echo ' selected="selected"'; } ?>>50</option>
                     		<option value="75"<? if($xml->Prefs[0]->bAlphaOff == '75') { echo ' selected="selected"'; } ?>>75</option>
                     		<option value="100"<? if($xml->Prefs[0]->bAlphaOff == '100') { echo ' selected="selected"'; } ?>>100</option>
                    	</select>
                    <span class="xflash_note"><?php echo JText::_('ALPHA_TIPS'); ?></span>
                   	</td>
				  </tr>      
                   <tr>
				   	<td class="key"><label><?php echo JText::_('BUTTON_ROLL_ALPHA'); ?>:</label></td>
				   	<td>
                   		<select name="prefs[bAlphaOn]">
                     		<option value="40"<? if($xml->Prefs[0]->bAlphaOn == '40') { echo ' selected="selected"'; } ?>>40</option>
                     		<option value="75"<? if($xml->Prefs[0]->bAlphaOn == '75') { echo ' selected="selected"'; } ?>>75</option>
                     		<option value="100"<? if($xml->Prefs[0]->bAlphaOn == '100') { echo ' selected="selected"'; } ?>>100</option>
                    	</select>
                    <span class="xflash_note"><?php echo JText::_('ALPHA_TIPS'); ?></span>
                   	</td>
				  </tr>
                  <tr>
				   		<td class="key"><label><?php echo JText::_('NAV_BUTTON_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[bColOff]"  value="<?php  if($xml->Prefs[0]->bColOff) { echo substr($xml->Prefs[0]->bColOff, 2); } else { echo $defaults['bColOff']; } ?>" />                	
                   </td>
				  </tr> 
                  <tr>
				   		<td class="key"><label><?php echo JText::_('NAV_BUTTON_ON_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[bColOn]"  value="<?php  if($xml->Prefs[0]->bColOn) { echo substr($xml->Prefs[0]->bColOn, 2); } else { echo $defaults['bColOn']; } ?>" /></td>
				  </tr>   
                  <tr>
				   	<td class="key"><label><?php echo JText::_('NAV_BUTTON_SEL_COL'); ?> (#):</label></td>
				   	<td><input type="text" maxlength="6" name="prefs[bSelected]"  value="<?php  if($xml->Prefs[0]->bSelected) { echo substr($xml->Prefs[0]->bSelected, 2); } else { echo $defaults['bSelected']; } ?>" /></td>
				  </tr>     
                   <tr>
				   	<td class="key"><label><?php echo JText::_('NAV_BUTTON_TEXT_COL'); ?> (#):</label></td>
				   	<td><input type="text" maxlength="6" name="prefs[bTextOff]"  value="<?php  if($xml->Prefs[0]->bTextOff) { echo substr($xml->Prefs[0]->bTextOff, 2); } else { echo $defaults['bTextOff']; } ?>" /></td>
				  </tr>                       
                   <tr>
				   	<td class="key"><label><?php echo JText::_('NAV_BUTTON_TEXT_ON_COL'); ?> (#):</label></td>
				   	<td><input type="text" maxlength="6" name="prefs[bTextOn]"  value="<?php  if($xml->Prefs[0]->bTextOn) { echo substr($xml->Prefs[0]->bTextOn, 2); } else { echo $defaults['bTextOn']; } ?>" />
                  	  <input type="hidden" name="prefs[transition]" value="<?php echo $defaults['transition']; ?>" />
                   </td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('TRANSITION_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[transitionCol]"  value="<?php  if($xml->Prefs[0]->transitionCol) { echo substr($xml->Prefs[0]->transitionCol, 2); } else { echo $defaults['transitionCol']; } ?>" />                  	
                   	</td>
				  </tr>      
                  <tr>
				   	<td class="key"><label><?php echo JText::_('ROTATION'); ?>:</label></td>
				   	<td>
                    	<select name="prefs[countdown]">
                      		<option value="5000"<? if($xml->Prefs[0]->countdown == '5000') { echo ' selected="selected"'; } ?>>5</option>
                      		<option value="6000"<? if($xml->Prefs[0]->countdown == '6000') { echo ' selected="selected"'; } ?>>6</option>
                      		<option value="7000"<? if($xml->Prefs[0]->countdown == '7000') { echo ' selected="selected"'; } ?>>7</option>
                      		<option value="8000"<? if($xml->Prefs[0]->countdown == '8000') { echo ' selected="selected"'; } ?>>8</option>
                      		<option value="9000"<? if($xml->Prefs[0]->countdown == '9000') { echo ' selected="selected"'; } ?>>9</option>
                      		<option value="10000"<? if($xml->Prefs[0]->countdown == '10000') { echo ' selected="selected"'; } ?>>10</option>
                    	</select> seconds
                        <input type="hidden" name="prefs[imagePath]" value="<?php echo $defaults['imagePath']; ?>" />
            			<input type="hidden" name="prefs[serverPath]" value="<?php echo $defaults['serverPath']; ?>" />
                   	</td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('BG_GRAPHIC'); ?>:</label></td>
				   	<td>
                    	<select name="prefs[watermark]">
                      		<option value="waves"<? if($xml->Prefs[0]->watermark == 'waves') { echo ' selected="selected"'; } ?>><?php echo JText::_('WAVES'); ?></option>
                            <option value="spikes"<? if($xml->Prefs[0]->watermark == 'spikes') { echo ' selected="selected"'; } ?>><?php echo JText::_('SPIKES'); ?></option>
                            <option value="stripes"<? if($xml->Prefs[0]->watermark == 'stripes') { echo ' selected="selected"'; } ?>><?php echo JText::_('STRIPES'); ?></option>
                      		<option value=""<? if($xml->Prefs[0]->watermark == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('NONE'); ?></option>
                    	</select>
                   	</td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('TITLE_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[headerCol]"  value="<?php  if($xml->Prefs[0]->headerCol) { echo substr($xml->Prefs[0]->headerCol, 2); } else { echo $defaults['headerCol']; } ?>" />
					</td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('SUBTITLE_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[subtitleCol]"  value="<?php  if($xml->Prefs[0]->subtitleCol) { echo substr($xml->Prefs[0]->subtitleCol, 2); } else { echo $defaults['subtitleCol']; } ?>" />
					</td>
				  </tr>  
                  <tr>
				   	<td class="key"><label><?php echo JText::_('DESC_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[bodyCol]"  value="<?php  if($xml->Prefs[0]->bodyCol) { echo substr($xml->Prefs[0]->bodyCol, 2); } else { echo $defaults['bodyCol']; } ?>" />
                  	</td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('HYPERLINK_COL'); ?> (#):</label></td>
				   	<td>
                    	<input type="text" maxlength="6" name="prefs[linkCol]"  value="<?php  if($xml->Prefs[0]->linkCol) { echo substr($xml->Prefs[0]->linkCol, 2); } else { echo $defaults['linkCol']; } ?>" />
                  	</td>
				  </tr>                
                  <tr>  
				   	<td class="key"><label><?php echo JText::_('HEAD_FONT'); ?>:</label></td>
				   	<td>
                    	<select name="prefs[headerFont]">
                             <option value="Arial"<? if($xml->Prefs[0]->headerFont == 'Arial') { echo ' selected="selected"'; } ?>>Arial</option>
                             <option value="Century Gothic"<? if($xml->Prefs[0]->headerFont == 'Century Gothic') { echo ' selected="selected"'; } ?>>Century Gothic</option>
                             <option value="Helvetica"<? if($xml->Prefs[0]->headerFont == 'Helvetica') { echo ' selected="selected"'; } ?>>Helvetica</option>
                             <option value="Trebuchet MS"<? if($xml->Prefs[0]->headerFont == 'Trebuchet MS') { echo ' selected="selected"'; } ?>>Trebuchet MS</option>
                        </select>
                   </td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('DESC_FONT'); ?>:</label></td>
				   	<td>
                        <select name="prefs[bodyFont]">
                         	<option value="Arial"<? if($xml->Prefs[0]->bodyFont == 'Arial') { echo ' selected="selected"'; } ?>>Arial</option>
                         	<option value="Century Gothic"<? if($xml->Prefs[0]->bodyFont == 'Century Gothic') { echo ' selected="selected"'; } ?>>Century Gothic</option>
                         	<option value="Helvetica"<? if($xml->Prefs[0]->bodyFont == 'Helvetica') { echo ' selected="selected"'; } ?>>Helvetica</option>
                         	<option value="Trebuchet MS"<? if($xml->Prefs[0]->bodyFont == 'Trebuchet MS') { echo ' selected="selected"'; } ?>>Trebuchet MS</option>
                        </select>
                   </td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('FONT_SIZE_TITLE'); ?>:</label></td>
				   	<td>
                        <select name="prefs[headerSize]">
                             <option value="18"<? if($xml->Prefs[0]->headerSize == '18') { echo ' selected="selected"'; } ?>>18</option>
                             <option value="20"<? if($xml->Prefs[0]->headerSize == '20') { echo ' selected="selected"'; } ?>>20</option>
                             <option value="22"<? if($xml->Prefs[0]->headerSize == '22') { echo ' selected="selected"'; } ?>>22</option>
                             <option value="24"<? if($xml->Prefs[0]->headerSize == '24') { echo ' selected="selected"'; } ?>>24</option>
                             <option value="26"<? if($xml->Prefs[0]->headerSize == '26') { echo ' selected="selected"'; } ?>>26</option>
                        </select>
                   	</td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('FONT_SIZE_SUBTITLE'); ?>:</label></td>
				   	<td>
                        <select name="prefs[subtitleSize]">
                             <option value="10"<? if($xml->Prefs[0]->subtitleSize == '10') { echo ' selected="selected"'; } ?>>10</option>
                             <option value="12"<? if($xml->Prefs[0]->subtitleSize == '12') { echo ' selected="selected"'; } ?>>12</option>
                             <option value="14"<? if($xml->Prefs[0]->subtitleSize == '14') { echo ' selected="selected"'; } ?>>14</option>
                             <option value="18"<? if($xml->Prefs[0]->subtitleSize == '18') { echo ' selected="selected"'; } ?>>18</option>
                        </select>
                   	</td>
				  </tr>
                  <tr>
				   	<td class="key"><label><?php echo JText::_('FONT_SIZE_DESC'); ?>:</label></td>
				   	<td>
                        <select name="prefs[bodySize]">
                             <option value="10"<? if($xml->Prefs[0]->bodySize == '10') { echo ' selected="selected"'; } ?>>10</option>
                             <option value="11"<? if($xml->Prefs[0]->bodySize == '11') { echo ' selected="selected"'; } ?>>11</option>
                             <option value="12"<? if($xml->Prefs[0]->bodySize == '12') { echo ' selected="selected"'; } ?>>12</option>
                             <option value="13"<? if($xml->Prefs[0]->bodySize == '13') { echo ' selected="selected"'; } ?>>13</option>
                             <option value="14"<? if($xml->Prefs[0]->bodySize == '14') { echo ' selected="selected"'; } ?>>14</option>
                        </select>
                   	</td>
				  </tr>
			 </tbody>
				</table>
			</fieldset>
		         
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savedata" />
            <?php 	echo $pane->endPanel(); ?>
            <?php echo $pane->startPanel( 'Images', 'cpanel-panel-images' );?>	
            <iframe width="100%" height="500" name="filer" id="filer" src="index3.php?option=<?php echo $option; ?>&amp;task=media"></iframe>
             <?php 	echo $pane->endPanel(); ?>
            <?php echo $pane->endPane(); ?>
            </div>
		<div class="clr"></div>			
    </form>
   
	<?php
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function media( &$sconfig) 
	{
		?>
		<form action="index3.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
				<fieldset style="border:none;">
				<div id="themanager" class="manager">
                  <p style="color:#666666;width:80%;"><?php echo JText::_('UPLOAD_TIPS'); ?></p>
                <h4>dir = <?php echo JPATH_ROOT.$sconfig->parameters['uploadpath']; ?></h4>
					<iframe src="index3.php?option=com_xflash&amp;task=list" name="imgManager" id="imgManager" width="95%" height="180"></iframe>
				</div>
			</fieldset>
			
			<fieldset style="border:none;">
				<input type="file" name="upload" id="upload" />
				<input type="submit" value="Upload" />

				<input type="hidden" name="option" value="com_xflash" />
				<input type="hidden" name="task" value="upload" />
			</fieldset>
		</form>
		<?php
	}

	//-----------

	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if($lastSlash == strlen($dir)-1){
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------
	
	public function draw_no_results()
	{
		echo '<p style="text-indent:1em;">'.JText::_('NO_FILES').'</p>'.n;
	}

	//-----------

	public function draw_no_dir() 
	{
		global $BASE_DIR, $BASE_ROOT;

		echo '<p class="alert">'.JText::_('CONFIG_PROBLEM').': &quot;'. $BASE_DIR.$BASE_ROOT .'&quot; '.JText::_('NOT_EXIST').'.</p>'.n;
	}

	//-----------

	public function draw_table_header() 
	{
		echo t.t.'<form action="index2.php" method="post" name="filelist" id="filelist">'.n;
		echo t.t.'<table border="0" cellpadding="0" cellspacing="0">'.n;
	}

	//-----------

	public function draw_table_footer() 
	{
		echo t.t.'</table>'.n;
		echo t.t.'</form>'.n;
	}

	//-----------

	public function show_doc($doc, $icon) 
	{
		?>
		 <tr>
		  <td><img src="<?php echo $icon; ?>" alt="<?php echo $doc; ?>" width="16" height="16" /></td>
		  <td width="100%" style="padding-left: 0;"><?php echo $doc; ?></td>
		  <td><a href="index3.php?option=com_xflash&amp;task=deletefile&amp;delFile=<?php echo $doc; ?>" target="filer" style="border:none;" onclick="return deleteImage('<?php echo $doc; ?>');" title="<?php echo JText::_('DELETE_THIS'); ?>"><img src="components/com_media/images/edit_trash.gif" width="15" height="15" style="border:none;" alt="<?php echo JText::_('DELETE'); ?>" /></a></td>
		 </tr>
		<?php
	}

	//-----------

	public function parse_size($size)
	{
		if($size < 1024) {
			return $size.' bytes';
		} else if($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' Kb';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
		}
	}

	//-----------

	public function num_files($dir)
	{
		$total = 0;

		if(is_dir($dir)) {
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if(substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}
	
	//-----------
	
	public function imageStyle()
	{
		?>
		<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for(i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)== '<?php if (isset($listdir)) { echo $listdir ;} else { echo '/';}  ?>')
				{
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file)
		{
			if(confirm("Delete file \""+file+"\"?"))
				return true;

			return false;
		}
		
		function deleteFolder(folder, numFiles)
		{
			if(numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}
	
			if(confirm('Delete folder "'+folder+'"?'))
				return true;
	
			return false;
		}
		</script>
		<?php
	}
}
?>