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
defined('_JEXEC') or die( 'Restricted access' );

$base = rtrim(JURI::getInstance()->base(true), '/');

$this->css('create.css')
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
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_ATTACH_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_ATTACH_ATTACHMENTS'); ?></legend>

			<div class="field-wrap">
				<div class="asset-uploader">
			<?php if (JPluginHelper::isEnabled('system', 'jquery')) { ?>
					<div class="grid">
						<div class="col span-half">
							<div id="ajax-uploader" data-action="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;task=save&amp;pid=<?php echo $this->id; ?>" data-list="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;pid=<?php echo $this->id; ?>">
							</div>
							<script src="<?php echo $base; ?>/media/system/js/jquery.fileuploader.js"></script>
							<script src="<?php echo $base; ?>/components/com_resources/assets/js/fileupload.js"></script>
						</div><!-- / .col span-half -->
						<div class="col span-half omega">
							<div id="link-adder" data-action="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;task=create&amp;pid=<?php echo $this->id; ?>&amp;url=" data-list="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;pid=<?php echo $this->id; ?>">
							</div>
						</div><!-- / .col span-half omega -->
					</div>
				<?php } ?>
					<iframe width="100%" height="500" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $this->option; ?>&amp;controller=attachments&amp;id=<?php echo $this->id; ?>&amp;tmpl=component"></iframe>
				</div><!-- / .asset-uploader -->
			</div><!-- / .field-wrap -->

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		</fieldset><div class="clear"></div>
		<div class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" />
		</div>
	</form>
</section><!-- / .main section -->
