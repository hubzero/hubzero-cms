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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();
$jconfig = JFactory::getConfig();

// Get parameters
$rparams = new JRegistry($this->resource->params);
$params = $this->config;
$params->merge($rparams);

$this->css('create.css')
     ->css('resources.css', 'com_resources')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo JText::_('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->resource)
		     ->set('progress', $this->progress)
		     ->display();
	?>

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=' . $this->task); ?>" method="post" id="hubForm">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="step" value="<?php echo $this->step; ?>" />

<?php if ($this->progress['submitted'] == 1) { ?>
		<div class="explaination">
			<p class="help">
				<?php echo JText::_('COM_CONTRIBUTE_PASSED_REVIEW'); ?> <a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->id); ?>"><?php echo JText::_('COM_CONTRIBUTE_VIEW_HERE'); ?></a>
			</p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_LICENSING_LEGEND'); ?></legend>

			<label for="license">
				<?php echo JText::_('COM_CONTRIBUTE_LICENSE_LABEL'); ?>
				<select name="license" id="license">
					<option value=""><?php echo JText::_('COM_CONTRIBUTE_SELECT_LICENSE'); ?></option>
				<?php
				$l = array();
				$c = false;
				$preview = JText::_('COM_CONTRIBUTE_LICENSE_PREVIEW');
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
					?>
					<option value="custom"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
					<?php
						$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(nl2br($license->text)) . '" />';
						$c = $this->escape(nl2br($license->text));
					}
				}
				if (!$c && $this->config->get('cc_license_custom'))
				{
					?>
					<option value="custom"><?php echo JText::_('COM_CONTRIBUTE_CUSTOM_LICENSE'); ?></option>
					<?php
					$c = $this->escape(JText::_('COM_CONTRIBUTE_ENTER_LICENSE_HERE'));
					$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(JText::_('COM_CONTRIBUTE_ENTER_LICENSE_HERE')) . '" />';
				}
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
						continue;
					}
					else
					{
					?>
					<option value="<?php echo $this->escape($license->name); ?>"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo $this->escape($license->title); ?></option>
					<?php
					}
					$l[] = '<input type="hidden" id="license-' . $this->escape($license->name) . '" value="' . $this->escape(nl2br($license->text)) . '" />';
					if ($params->get('license') == $license->name)
					{
						$preview = nl2br($this->escape($license->text));
					}
				}
				?>
				</select>
				<div id="license-preview" style="display:none;"><?php echo $preview; ?></div>
				<?php echo implode("\n", $l); ?>
			</label>
			<?php if ($this->config->get('cc_license_custom')) { ?>
			<textarea name="license-text" id="license-text" cols="35" rows="10" style="display:none;"><?php echo $c; ?></textarea>
			<?php } ?>

			<input type="hidden" name="published" value="1" />
			<input type="hidden" name="authorization" value="1" />
		</fieldset><div class="clear"></div>
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_SAVE'); ?>" />
		</p>
	</form>
<?php } else { ?>
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT'); ?></h4>
		<?php if ($this->config->get('autoapprove', 0) != 1) { ?>
			<p>
				<?php echo JText::sprintf(
					'COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT_ANSWER',
					'<a href="' . JRoute::_('index.php?option=' . $this->option) . '">' . JText::_('resources') . '</a>',
					'<a href="' . JRoute::_('index.php?option=com_whatsnew') . '">' . JText::_('What\'s New') . '</a>'
				); ?>
			</p>
		<?php } else { ?>
			<p>
				<?php echo JText::sprintf(
					'COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT_AUTOAPPROVED_ANSWER',
					'<a href="' . JRoute::_('index.php?option=' . $this->option) . '">' . JText::_('resources') . '</a>',
					'<a href="' . JRoute::_('index.php?option=com_whatsnew') . '">' . JText::_('What\'s New') . '</a>'
				); ?>
			</p>
		<?php } ?>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_AUTHORIZATION_LEGEND'); ?></legend>

			<label for="authorization">
				<input class="option" type="checkbox" name="authorization" id="authorization" value="1" />
				<span class="required"><?php echo JText::_('COM_CONTRIBUTE_REQUIRED'); ?></span>
				<?php echo JText::sprintf(
					'COM_CONTRIBUTE_AUTHORIZATION_LABEL',
					$jconfig->getValue('config.sitename'),
					$jconfig->getValue('config.sitename')
				); ?><br /><br />
				<?php echo JText::sprintf(
					'COM_CONTRIBUTE_AUTHORIZATION_MUST_ATTRIBUTE',
					$jconfig->getValue('config.sitename'),
					'<a class="popup 760x560" href="' . JURI::base(true) . '/legal/license">' . JText::_('COM_CONTRIBUTE_THE_FULL_LICENSE') . '</a>'
				); ?>
			</label>
	<?php if ($this->config->get('cc_license')) { ?>
			<label for="license">
				<?php echo JText::_('COM_CONTRIBUTE_LICENSE_LABEL'); ?>
				<select name="license" id="license">
					<option value=""><?php echo JText::_('COM_CONTRIBUTE_SELECT_LICENSE'); ?></option>
			<?php
				$l = array();
				$c = false;
				$preview = JText::_('COM_CONTRIBUTE_LICENSE_PREVIEW');
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
					?>
						<option value="custom"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
					<?php
						$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(nl2br($license->text)) . '" />';
						$c = $this->escape(nl2br($license->text));
					}
				}
				if (!$c && $this->config->get('cc_license_custom'))
				{
					?>
						<option value="custom"><?php echo JText::_('COM_CONTRIBUTE_CUSTOM_LICENSE'); ?></option>
					<?php
					$c = $this->escape(JText::_('COM_CONTRIBUTE_ENTER_LICENSE_HERE'));
					$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(JText::_('COM_CONTRIBUTE_ENTER_LICENSE_HERE')) . '" />';
				}
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
						continue;
					}
					else
					{
					?>
						<option value="<?php echo $this->escape($license->name); ?>"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo $this->escape($license->title); ?></option>
					<?php
					}
					$l[] = '<input type="hidden" id="license-' . $this->escape($license->name) . '" value="' . $this->escape(nl2br($license->text)) . '" />';
					if ($params->get('license') == $license->name)
					{
						$preview = nl2br($this->escape($license->text));
					}
				}
			?>
				</select>
				<div id="license-preview" style="display:none;"><?php echo $preview; ?></div>
				<?php echo implode("\n", $l); ?>
			</label>
		<?php if ($this->config->get('cc_license_custom')) { ?>
			<textarea name="license-text" id="license-text" cols="35" rows="10" style="display:none;"><?php echo $c; ?></textarea>
		<?php } ?>
	<?php } ?>

			<input type="hidden" name="published" value="0" />
		</fieldset><div class="clear"></div>
		<div class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_SUBMIT_CONTRIBUTION'); ?>" />
		</div>
	</form>

	<h1 id="preview-header"><?php echo JText::_('COM_CONTRIBUTE_REVIEW_PREVIEW'); ?></h1>
	<div id="preview-pane">
		<iframe id="preview-frame" name="preview-frame" width="100%" frameborder="0" src="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->id . '&tmpl=component&mode=preview'); ?>"></iframe>
	</div>
<?php } ?>
</section><!-- / .main section -->
