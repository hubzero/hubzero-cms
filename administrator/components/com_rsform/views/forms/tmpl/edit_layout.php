<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table border="1" width="100%" class="adminform">
	<tr>
		<td><strong><?php echo JText::_('RSFP_CLASSIC_LAYOUTS'); ?></strong></td>
	</tr>
	<tr>
		<td valign="top">
			<div class="rsform_layout_box">
				<label for="formLayoutInline">
					<input type="radio" id="formLayoutInline" name="FormLayoutName" value="inline" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>','inline');" <?php if ($this->form->FormLayoutName == 'inline') echo 'checked="checked"'; ?> /><?php echo JText::_('RSFP_LAYOUT_INLINE');?><br/>
					<img src="components/com_rsform/assets/images/layouts/inline.gif" width="175"/>
				</label>
			</div>
			<div class="rsform_layout_box">
				<label for="formLayout2lines">
					<input type="radio" id="formLayout2lines" name="FormLayoutName" value="2lines" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>','2lines')" <?php if ($this->form->FormLayoutName == '2lines') echo 'checked="checked"'; ?>/><?php echo JText::_('RSFP_LAYOUT_2LINES');?><br/>
					<img src="components/com_rsform/assets/images/layouts/2lines.gif" width="175"/>
				</label>
			</div>
			<div class="rsform_layout_box">
				<label for="formLayout2colsinline">
					<input type="radio" id="formLayout2colsinline" name="FormLayoutName" value="2colsinline" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>','2colsinline')" <?php if ($this->form->FormLayoutName == '2colsinline') echo 'checked="checked"'; ?> /><?php echo JText::_('RSFP_LAYOUT_2COLSINLINE');?><br/>
					<img src="components/com_rsform/assets/images/layouts/2colsinline.gif" width="175"/>
				</label>
			</div>
			<div class="rsform_layout_box">
				<label for="formLayout2cols2lines">
					<input type="radio" id="formLayout2cols2lines" name="FormLayoutName" value="2cols2lines" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>','2cols2lines')" <?php if ($this->form->FormLayoutName == '2cols2lines') echo 'checked="checked"'; ?>/><?php echo JText::_('RSFP_LAYOUT_2COLS2LINES');?><br/>
					<img src="components/com_rsform/assets/images/layouts/2cols2lines.gif" width="175"/>
				</label>
			</div>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo JText::_('RSFP_XHTML_LAYOUTS'); ?></strong></td>
	</tr>
	<tr>
		<td valign="top">
		<div class="rsform_layout_box">
			<label for="formLayoutInlineXhtml">
				<input type="radio" id="formLayoutInlineXhtml" name="FormLayoutName" value="inline-xhtml" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>','inline-xhtml');" <?php if ($this->form->FormLayoutName == 'inline-xhtml') echo 'checked="checked"'; ?> /><?php echo JText::_('RSFP_LAYOUT_INLINE_XHTML');?><br/>
				<img src="components/com_rsform/assets/images/layouts/inline-xhtml.gif" width="175"/>
			</label>
		</div>
		<div class="rsform_layout_box">
				<label for="formLayout2linesXhtml">
					<input type="radio" id="formLayout2linesXhtml" name="FormLayoutName" value="2lines-xhtml" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>','2lines-xhtml')" <?php if ($this->form->FormLayoutName == '2lines-xhtml') echo 'checked="checked"'; ?>/><?php echo JText::_('RSFP_LAYOUT_2LINES_XHTML');?><br/>
					<img src="components/com_rsform/assets/images/layouts/2lines-xhtml.gif" width="175"/>
				</label>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<button type="button" onclick="generateLayout('<?php echo $this->form->FormId; ?>');"><?php echo JText::_('RSFP_GENERATE_LAYOUT'); ?></button>
			<label for="FormLayoutAutogenerate"><?php echo JText::_('RSFP_AUTOGENERATE_LAYOUT');?> <input type="checkbox" name="FormLayoutAutogenerate" id="FormLayoutAutogenerate" value="1" <?php echo $this->form->FormLayoutAutogenerate ? 'checked="checked"' : ''; ?> onclick="changeFormAutoGenerateLayout('<?php echo $this->form->FormId; ?>');" /></label>
		</td>
	</tr>
</table>

<table width="100%" style="clear:both;">
	<tr>
		<td width="1%" valign="top">
		   <table width="100%" style="clear:both;">
				<tr>
					<td width="1%">
						<textarea name="FormLayout" id="formLayout" <?php echo $this->form->FormLayoutAutogenerate ? 'readonly="readonly"' : '';?>><?php echo $this->escape($this->form->FormLayout); ?></textarea>
					</td>
					<td valign="top">
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<button type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd1">
				<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
				<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
				<?php if(!empty($this->quickfields))
					foreach($this->quickfields as $quickfield) { ?>
						<strong><?php echo $quickfield;?></strong><br/>
						<pre>{<?php echo $quickfield; ?>:caption}</pre>
						<pre>{<?php echo $quickfield; ?>:body}</pre>
						<pre>{<?php echo $quickfield; ?>:validation}</pre>
						<pre>{<?php echo $quickfield; ?>:description}</pre>
						<br/>
				<?php } ?>
			</div>
		</td>
	</tr>
</table>