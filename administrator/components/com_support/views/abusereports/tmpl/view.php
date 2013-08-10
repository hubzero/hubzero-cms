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
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'REPORT_ABUSE' ).' ]</small></small>', 'support.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

$reporter =& JUser::getInstance($this->report->created_by);

$link = '';

if (is_object($this->reported)) {
	$author =& JUser::getInstance($this->reported->author);

	if (is_object($author) && $author->get('username')) {
		$this->title .= ' by '.$author->get('username');
	} else {
		$this->title .= ' by '.JText::_('UNKNOWN');
	}
	$this->title .= ($this->reported->anon) ? '('.JText::_('ANONYMOUS').')':'';

	$link = '../'.$this->reported->href;
}

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

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('ITEM_REPORTED_AS_ABUSIVE'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<h4><?php echo '<a href="'.$link.'">'.$this->escape($this->title).'</a>: ' ?></h4>
							<p><?php echo (is_object($this->reported)) ? stripslashes($this->reported->text) : ''; ?></p>
		                    <?php if (is_object($this->reported) && isset($this->reported->subject) && $this->reported->subject!='') {
								echo '<p>'.$this->escape(stripslashes($this->reported->subject)) .'</p>';
							} ?>
						</td>
					</tr>
					<tr>
						<td>
							<p style="color:#999;">
								<?php echo JText::_('REPORTED_BY'); ?> <?php echo (is_object($reporter) && $reporter->get('username')) ? $reporter->get('username') : JText::_('UNKNOWN'); ?>, <?php echo JText::_('RECEIVED'); ?> <?php echo JHTML::_('date', $this->report->created, $dateFormat, $tz); ?>: 
								<?php 
								if ($this->report->report) {
									echo '<br /><br />' . $this->escape(stripslashes($this->report->report));
								} else {
									echo '<br /><br />' . $this->escape(stripslashes($this->report->subject));
								}
								?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('TAKE_ACTION'); ?></span></legend>

<?php if ($this->report->state==0) { ?>
			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<label for="field-task-release"><input type="radio" name="task" id="field-task-release" value="release" /> <?php echo JText::_('RELEASE_ITEM'); ?></label><br />
						</td>
					</tr>
					<tr>
						<td>
							<label for="field-task-remove"><input type="radio" name="task" id="field-task-remove" value="remove" /> <?php echo JText::_('DELETE_ITEM'); ?> <?php echo JText::_('(Append explanation below - optional)'); ?></label><br />
							<label><textarea name="note" id="note" rows="5" cols="25" style="width: 100%;"></textarea></label><br />
						</td>
					</tr>
					<tr>
						<td>
							<label for="field-task-cancel"><input type="radio" name="task" value="cancel" id="field-task-cancel" checked="checked" /> <?php echo JText::_('DECIDE_LATER'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>
<?php } else { ?>
			<p class="warning"><?php echo JText::_('Action already taken.'); ?></p>
			<input type="hidden" name="task" value="view" />
<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="id" value="<?php echo $this->report->id ?>" />
	<input type="hidden" name="parentid" value="<?php echo $this->parentid ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
