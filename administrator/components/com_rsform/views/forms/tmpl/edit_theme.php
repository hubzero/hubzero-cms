<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table class="adminlist">
<thead>
	<tr>
		<th width="5" class="title"><?php echo JText::_( 'Num' ); ?></th>
		<th class="title"><?php echo JText::_( 'Name' ); ?></th>
		<th width="10%" align="center"><?php echo JText::_( 'Version' ); ?></th>
		<th width="15%" class="title"><?php echo JText::_( 'Date' ); ?></th>
		<th width="25%"  class="title"><?php echo JText::_( 'Author' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	$i = 0;
	foreach ($this->themes as $theme) { ?>
	<tr class="row<?php echo $k; ?>">
		<td width="5">
			<input type="radio" id="theme<?php echo $i; ?>" name="ThemeName" value="<?php echo $theme->directory; ?>" <?php echo $this->form->ThemeParams->get('name') == $theme->directory ? 'checked="checked"' : ''; ?> />
			<?php if (isset($theme->css)) foreach ($theme->css as $css) { ?>
				<input type="hidden" name="ThemeCSS[<?php echo $theme->directory; ?>][]" value="<?php echo $this->escape($css); ?>" />
			<?php } ?>
			<?php if (isset($theme->js)) foreach ($theme->js as $js) { ?>
				<input type="hidden" name="ThemeJS[<?php echo $theme->directory; ?>][]" value="<?php echo $this->escape($js); ?>" />
			<?php } ?>
		</td>
		<td class="editlinktip hasTip" title="<?php echo $theme->name;?>::
<img border=&quot;1&quot; src=&quot;<?php echo $theme->img_path; ?>&quot; name=&quot;imagelib&quot; alt=&quot;<?php echo JText::_( 'No preview available' ); ?>&quot; width=&quot;206&quot; height=&quot;145&quot; />"><label for="theme<?php echo $i; ?>"><?php echo $theme->name;?></label></td>
		<td align="center"><?php echo $theme->version; ?></td>
		<td><?php echo $theme->creationdate; ?></td>
		<td class="editlinktip hasTip" title="<?php echo JText::_( 'Author Information' );?>::<?php echo $theme->authorEmail; ?><br /><?php echo $theme->authorUrl; ?>"><?php echo $theme->author != '' ? $theme->author : '&nbsp;'; ?></label></td>
	</tr>
	<?php $i++; ?>
	<?php } ?>
	</tbody>
</table>