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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die;
?>
<div id="step">
        <div class="far-right">
<?php if ($this->document->direction == 'ltr') : ?>
                <div class="button1-right"><div class="prev"><a href="index.php?view=language" onclick="return Install.goToPage('language');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a></div></div>
                <div class="button1-left"><div class="next"><a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a></div></div>
<?php elseif ($this->document->direction == 'rtl') : ?>
		<div class="button1-right"><div class="prev"><a href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a></div></div>
                <div class="button1-left"><div class="next"><a href="index.php?view=language" onclick="return Install.goToPage('language');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a></div></div>
<?php endif; ?>
        </div>
        <h2><?php echo JText::_('INSTL_INSTALLKEY'); ?></h2>
</div>

<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="t">
			<div class="t">
				<div class="t"></div>
			</div>
		</div>
		<div class="m">
			<h3><?php echo JText::_('INSTL_INSTALLKEY_TITLE'); ?></h3>
			<div class="install-text">
				<?php echo $this->form->getLabel('installkey'); ?>
			</div>
			<div class="install-body">
				<div class="t">
					<div class="t">
						<div class="t"></div>
					</div>
				</div>
				<div class="m">
					<fieldset>
						<?php echo $this->form->getInput('installkey'); ?>
					</fieldset>
				</div>
				<div class="b">
					<div class="b">
						<div class="b"></div>
					</div>
				</div>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
		</div>
		<div class="b">
			<div class="b">
				<div class="b"></div>
			</div>
		</div>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="task" value="setup.installkey" />
	<?php echo JHtml::_('form.token'); ?>
</form>
