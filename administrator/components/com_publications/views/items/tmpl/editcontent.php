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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$tmpl = JRequest::getVar('tmpl', '');

if ($tmpl != 'component')
{
	JToolBarHelper::title(JText::_('COM_PUBLICATIONS').': [ ' . JText::_('COM_PUBLICATIONS_EDIT_CONTENT_FOR_PUB') . ' #'
	. $this->pub->id . ' (v.' . $this->pub->version_label . ')' . ' ]', 'groups.png');
	JToolBarHelper::save('savecontent');
	JToolBarHelper::cancel();
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);

}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<p class="crumbs"><a href="<?php echo 'index.php?option=' . $this->option . '&amp;controller='
. $this->controller; ?>"><?php echo JText::_('COM_PUBLICATIONS_PUBLICATION_MANAGER'); ?></a> &raquo; <a href="<?php echo 'index.php?option='
. $this->option . '&amp;controller=' . $this->controller . '&amp;task=edit&amp;id[]='. $this->pub->id . '&version=' . $this->pub->version_number; ?>"><?php echo JText::_('COM_PUBLICATIONS_PUBLICATION') . ' #' . $this->pub->id; ?></a> &raquo; <?php echo JText::_('COM_PUBLICATIONS_EDIT_CONTENT_INFO'); ?></p>

<form action="index.php" method="post" name="adminForm" id="item-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div style="float: right">
			<?php if (!$this->getError()) { ?>
			<button type="button" onclick="submitbutton('savecontent');"><?php echo JText::_( 'JSAVE' );?></button>
			<?php } ?>
			<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_( 'Cancel' );?></button>
		</div>
		<div class="configuration" >
			<?php echo JText::_('COM_PUBLICATIONS_EDIT_CONTENT') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="task" value="savecontent" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version_number; ?>" />
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_EDIT_CONTENT_INFO'); ?></span></legend>
			<?php if ($this->getError()) {
				echo '<p class="error">' . $this->getError() . '</p>';
			} else { ?>

			<input type="hidden" name="el" value="<?php echo $this->elementId; ?>" />
			<?php
			$element		= $this->element->element;
			$block			= $this->element->block;
			$elName   		= 'element' . $this->elementId;
			// Customize title
			$defaultTitle	= $element->params->title
							? str_replace('{pubtitle}', $this->pub->title,
							$element->params->title) : NULL;
			$defaultTitle	= $element->params->title
							? str_replace('{pubversion}', $this->pub->version_label,
							$defaultTitle) : NULL;

			$attachments = $this->pub->_attachments;
			$attachments = isset($attachments['elements'][$this->elementId])
						 ? $attachments['elements'][$this->elementId] : NULL;

			// Get version params and extract bundle name
			$versionParams 	= new JParameter( $this->pub->params );
			$bundleName		= $versionParams->get($elName . 'bundlename', $defaultTitle);

			$multiZip 		= (isset($element->params->typeParams->multiZip)
							&& $element->params->typeParams->multiZip == 0)
							? false : true;

			?>
			<?php if (count($attachments) > 1 && $multiZip) { ?>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_BUNDLE_NAME'); ?>:</label>
				<input type="text" name="params[element<?php echo $this->elementId; ?>bundlename]" maxlength="250" value="<?php echo $bundleName; ?>" />
			</div>
			<?php } ?>
			<?php if ($attachments) { ?>
			<?php foreach ($attachments as $attach) { ?>
				<div class="input-wrap withdivider">
					<p>[<?php echo $attach->type; ?>] <?php echo $attach->path; ?></p>
					<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_ATTACHMENT_TITLE'); ?>:</label>
					<input type="text" name="attachments[<?php echo $attach->id; ?>][title]" maxlength="250" value="<?php echo $attach->title; ?>" />
				</div>
				<?php } ?>
			<?php } else { ?>
				<p class="notice"><?php echo JText::_('COM_PUBLICATIONS_NO_CONTENT'); ?></p>
			<?php } ?>
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('COM_PUBLICATIONS_ELEMENT_ID'); ?></td>
					<td>
						<?php echo $this->elementId; ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PUBLICATIONS_ELEMENT_TYPE'); ?></td>
					<td>
						<?php echo $element->params->type; ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PUBLICATIONS_ELEMENT_ROLE'); ?></td>
					<td>
						<?php echo $element->params->role == 1 ? JText::_('COM_PUBLICATIONS_ELEMENT_ROLE_PRIMARY') : JText::_('COM_PUBLICATIONS_ELEMENT_ROLE_SECOND'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>