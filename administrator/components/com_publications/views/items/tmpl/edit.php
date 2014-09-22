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

$htmlHelper	  = new PublicationsAdminHtml();

// Get hub config
$juri 	 = JURI::getInstance();
$jconfig = JFactory::getConfig();
$site 	 = $jconfig->getValue('config.live_site')
	? $jconfig->getValue('config.live_site')
	: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

$text = ($this->task == 'edit'
	? JText::_('JACTION_EDIT') . ' #' . $this->pub->id . ' (v.' . $this->row->version_label . ')'
	: JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATION') . ': [ ' . $text . ' ]', 'addedit.png');
JToolBarHelper::spacer();
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

$database = JFactory::getDBO();

// Get pub category
$rt = new PublicationCategory( $database );
$rt->load( $this->pub->category );

// Parse data
$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->row->metadata, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $htmlHelper->_txtUnpee($match[2]);
	}
}

$customFields = $rt->customFields && $rt->customFields != '{"fields":[]}' ? $rt->customFields : '{"fields":[{"default":"","name":"citations","label":"Citations","type":"textarea","required":"0"}]}';

if ($this->useBlocks)
{
	$customFields = $this->pub->_curationModel->getMetaSchema();
}


include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');

$elements 	= new PublicationsElements($data, $customFields);
$fields 	= $elements->render();
$schema 	= $elements->getSchema();

$canedit 	= 1;
$now 		= JFactory::getDate()->toSql();
$status 	= $htmlHelper->getPubStateProperty($this->row, 'status');

$v = $this->version == 'default' ? '' : '?v=' . $this->version;
$rating = $this->pub->rating == 9.9 ? 0.0 : $this->pub->rating;
$params = new JParameter($this->row->params, JPATH_COMPONENT . DS . 'publications.xml');

// Available panels and default config
$panels = array(
	'authors'		=> $this->typeParams->get('show_authors', 2),
	'audience'		=> $this->typeParams->get('show_audience', 0),
	'gallery'		=> $this->typeParams->get('show_gallery', 1),
	'tags'			=> $this->typeParams->get('show_tags', 1),
	'license'		=> $this->typeParams->get('show_license', 2),
	'notes'			=> $this->typeParams->get('show_notes', 1),
	'metadata'		=> $this->typeParams->get('show_metadata', 1),
	'submitter'		=> $this->typeParams->get('show_submitter', 0)
);

JPluginHelper::importPlugin('hubzero');
$dispatcher = JDispatcher::getInstance();

?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'resetrating') {
		if (confirm('<?php echo JText::_('COM_PUBLICATIONS_CONFIRM_RATINGS_RESET'); ?>')) {
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if (pressbutton == 'publish') {
		form.admin_action.value = 'publish';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'revert') {
		form.admin_action.value = 'revert';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'message') {
		form.admin_action.value = 'message';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'unpublish') {
		form.admin_action.value = 'unpublish';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'republish') {
		form.admin_action.value = 'republish';
		submitform( 'save' );
		return;
	}

	// do field validation
	if (form.title.value == '') {
		alert('<?php echo JText::_('COM_PUBLICATIONS_ERROR_MISSING_TITLE'); ?>');
	}
	else {
		submitform( pressbutton );
	}
}

function popratings()
{
	window.open('index.php?option=<?php echo $this->option; ?>&task=ratings&id=<?php echo $this->row->id; ?>&no_html=1', 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
	return false;
}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form" class="editform">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_CATEGORY'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo $this->lists['category']; ?>
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
				<input type="text" name="alias" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->pub->alias)); ?>" />
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_SYNOPSIS'); ?>:</label>
				<textarea name="abstract" id="pub-abstract" cols="40" rows="3" class="pubinput"><?php echo $this->row->abstract; ?></textarea>
			</div>
			<div class="input-wrap">
				<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_DESCRIPTION'); ?>:</label>
				<?php
					$editor = JFactory::getEditor();
					echo $editor->display('description', $this->escape(stripslashes($this->row->description)), '', '', '40', '10', false, 'pub_description');
				?>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELD_METADATA'); ?></span></legend>
			<div class="input-wrap">
				<?php echo $fields; ?>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELD_NOTES'); ?></span></legend>
			<div class="input-wrap">
				<textarea name="release_notes" id="release_notes" cols="40" rows="5" class="pubinput"><?php echo $this->row->release_notes; ?></textarea>
			</div>
	</fieldset>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_AUTHORS'); ?></span> <span class="sidenote add"><a href="index.php?option=com_publications&amp;task=addauthor&amp;controller=items&amp;pid=<?php echo $this->row->publication_id; ?>&amp;vid=<?php echo $this->row->id; ?>"><?php echo JText::_('COM_PUBLICATIONS_ADD_AUTHOR'); ?></a></span></legend>
		<fieldset>
		<div class="input-wrap" id="publiction-authors">
			<?php echo $this->lists['authors']; ?>
		</div>
		</fieldset>
	</fieldset>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_TAGS'); ?></span></legend>
		<fieldset>
		<div class="input-wrap">
			<?php
			$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)) );
			if (count($tf) > 0) {
				echo $tf[0];
			} else { ?>
				<input type="text" name="tags" id="actags" value="<?php echo $this->tags; ?>" />
			<?php } ?>
		</div>
		</fieldset>
	</fieldset>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_LICENSE'); ?></span></legend>
		<fieldset>
		<div class="input-wrap">
			<label for="license_type"><?php echo JText::_('COM_PUBLICATIONS_FIELD_LICENSE_TYPE'); ?>:</label>
			<?php echo $this->lists['licenses']; ?>
		</div>
		<div class="input-wrap">
			<label for="license_text"><?php echo JText::_('COM_PUBLICATIONS_FIELD_LICENSE_TEXT'); ?>:</label>
			<textarea name="license_text" id="license_text" cols="40" rows="5" class="pubinput"><?php echo $this->row->license_text; ?></textarea>
		</div>
		</fieldset>
	</fieldset>
</div>
<div class="col width-40 fltrt">
<table class="meta">
	<tbody>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_ID'); ?></th>
			<td><?php echo $this->pub->id; ?></td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_CREATED'); ?></th>
			<td>
				<?php echo JHTML::_('date', $this->row->created, JText::_('DATE_FORMAT_LC2')); ?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_CREATOR'); ?></th>
			<td>
				<?php echo $this->escape($this->row->created_by_name); ?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_PROJECT'); ?></th>
			<td>
				<?php echo $this->pub->project_title; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_TYPE'); ?></th>
			<td>
				<?php echo $this->pub->_type->type; ?>
			</td>
		</tr>
		<?php if ($this->row->state == 1 || $this->row->state == 0) { ?>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_RANKING'); ?>:</th>
			<td><?php echo $this->pub->ranking; ?>/10
				<?php if ($this->pub->ranking != '0') { ?>
					<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" />
				<?php } ?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_RATING'); ?>:</th>
			<td><?php echo $rating.'/5.0 ('.$this->pub->times_rated.' reviews)'; ?>
			<?php if ( $rating != '0.0' ) { ?>
				<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" />
			<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<fieldset class="adminform">
	<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_VERSION'); ?></span></legend>
<table>
	<tbody>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_VERSION_ID'); ?></th>
			<td>
				<?php echo $this->row->id; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_VERSION'); ?></th>
			<td>
				<?php echo $this->pub->version_label.' ('.$status.')'; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_URL'); ?></th>
			<td><a href="<?php echo trim($site, DS) . '/publications/' . $this->pub->id . $v; ?>" target="_blank"><?php echo trim($site, DS) . '/publications/' . $this->pub->id . $v; ?></a></td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_MODIFIED'); ?></th>
			<td><?php echo JHTML::_('date', $this->row->modified, JText::_('DATE_FORMAT_LC2')); ?></td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_MODIFIED_BY'); ?></th>
			<td><?php echo $this->row->modified_by_name; ?></td>
		</tr>
	</tbody>
</table>
</fieldset>
<fieldset class="adminform">
	<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_PUBLISHING'); ?></span></legend>
	<div class="input-wrap">
		<label for="field-published"><?php echo JText::_('COM_PUBLICATIONS_FIELD_STATUS'); ?>:</label><br />
		<select name="state" id="field-published">
			<option value="3"<?php echo ($this->row->state == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_DRAFT'); ?></option>
			<option value="4"<?php echo ($this->row->state == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_READY'); ?></option>
			<option value="5"<?php echo ($this->row->state == 5) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_PENDING'); ?></option>
			<option value="7"<?php echo ($this->row->state == 7) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_WIP'); ?></option>
			<option value="10"<?php echo ($this->row->state == 10) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_PRESERVING'); ?></option>
			<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></option>
			<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></option>
			<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_DELETED'); ?></option>
		</select>
	</div>
	<div class="input-wrap">
		<label for="access"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ACCESS'); ?>:</label>
		<?php echo $this->lists['access']; ?>
	</div>
	<div class="input-wrap">
		<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_GROUP_OWNER'); ?>:</label>
		<?php echo $this->lists['groups']; ?>
	</div>
	<div class="input-wrap">
		<label for="publish_up"><?php echo JText::_('COM_PUBLICATIONS_FIELD_PUBLISH_DATE'); ?>:</label><br />
			<?php echo JHTML::_('calendar', ($this->row->published_up != '0000-00-00 00:00:00' ? $this->escape(JHTML::_('date', $this->row->published_up, 'Y-m-d H:i:s')) : ''), 'published_up', 'published_up'); ?>
	</div>
		<div class="input-wrap">
			<label for="publish_down"><?php echo JText::_('COM_PUBLICATIONS_FIELD_UNPUBLISH_DATE'); ?>:</label><br />
			<?php
				$down = 'Never';
				if (strtolower($this->row->published_down) != 'never')
				{
					$down = $this->row->published_down != '0000-00-00 00:00:00'
						? JHTML::_('date', $this->row->published_down, 'Y-m-d H:i:s') : NULL;
				}
			?>
			<?php echo JHTML::_('calendar', $down, 'published_down', 'published_down'); ?>
		</div>
	<div class="input-wrap">
		<label><?php echo JText::_('COM_PUBLICATIONS_FIELD_DOI'); ?>:</label>
		<input type="text" id="doi" name="doi" value="<?php echo $this->row->doi; ?>" />
	</div>

	<div class="input-wrap">
		<table class="admintable">
			<tbody>
		<?php if (isset($this->submitter) && $this->submitter) { ?>
				<tr>
					<td class="paramlist_key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_SUBMITTER'); ?>:</td>
					<td><?php echo $this->submitter->name; ?></td>
				</tr>
		<?php } ?>
		<?php if ($this->row->state == 5) { ?>
				<tr>
					<td class="paramlist_key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_SUBMITTED'); ?>:</td>
					<td><?php echo $this->row->submitted; ?></td>
				</tr>
		<?php } else if ($this->row->state == 1 || $this->row->state == 0)  { ?>
			<?php if ($this->row->submitted != '0000-00-00 00:00:00') { ?>
					<tr>
						<td class="paramlist_key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_SUBMITTED'); ?></td>
						<td><?php echo $this->row->submitted; ?></td>
					</tr>
			<?php } ?>
			<?php if ($this->row->accepted != '0000-00-00 00:00:00') { ?>
					<tr>
						<td class="paramlist_key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ACCEPTED'); ?></td>
						<td><?php echo $this->row->accepted; ?></td>
					</tr>
			<?php } ?>
		<?php }  ?>
			<tr>
				<td class="paramlist_key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ARCHIVAL'); ?></td>
				<td>	<?php if (file_exists($this->archPath)) { ?>
						<a href="<?php echo str_replace(JPATH_ROOT, '', $this->archPath); ?>" class="archival"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ARCHIVAL'); ?></a> &nbsp;&nbsp;<a href="index.php?option=com_publications&amp;task=archive&amp;controller=items&amp;pid=<?php echo $this->row->publication_id; ?>&amp;vid=<?php echo $this->row->id; ?>&amp;version=<?php echo $this->version; ?>">[<?php echo JText::_('COM_PUBLICATIONS_REPACKAGE'); ?>]</a>
					<?php  }  else { ?>
					<a href="index.php?option=com_publications&amp;task=archive&amp;controller=items&amp;pid=<?php echo $this->row->publication_id; ?>&amp;vid=<?php echo $this->row->id; ?>&amp;version=<?php echo $this->version; ?>" class="archival"><?php echo JText::_('COM_PUBLICATIONS_PRODUCE_ARCHIVAL'); ?></a>
					<?php } ?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</fieldset>
<fieldset class="adminform">
	<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELD_MANAGEMENT_OPTIONS'); ?></span></legend>
	<div class="input-wrap">
		<textarea name="message" id="message" rows="5" cols="50"></textarea>
		<input type="hidden" name="admin_action" value="" />
		<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_ACTION_SEND_MESSAGE'); ?>" class="btn" id="do-message" onclick="submitbutton('message')" />
		<?php if ($this->row->state == 1) { ?>
			<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_ACTION_UNPUBLISH_VERSION'); ?>" class="btn" id="do-unpublish" onclick="submitbutton('unpublish')" />
		<?php } else if ($this->row->state == 0) { ?>
			<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_ACTION_REPUBLISH_VERSION'); ?>" class="btn" id="do-republish" onclick="submitbutton('republish')" />
		<?php } else if ($this->row->state == 5) { ?>
			<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_ACTION_APPROVE_AND_PUBLISH'); ?>" class="btn" id="do-publish" onclick="submitbutton('publish')" />
			<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_ACTION_REVERT_TO_DRAFT'); ?>" class="btn" id="do-revert" onclick="submitbutton('revert')" />
		<?php } ?>
	</div>
</fieldset>

<?php
	echo JHtml::_('sliders.start', 'content-panel');
	echo JHtml::_('sliders.panel', JText::_('COM_PUBLICATIONS_FIELDSET_PARAMETERS'), 'params-page');
?>
	<table class="admintable">
		<tbody>
			<?php
				foreach ($panels as $panel => $val)
				{
					?>
					<tr>
						<td class="key"><?php echo ucfirst($panel); ?>:</td>
						<td>
							<select name="params[show_<?php echo $panel; ?>]">
								<option value="0" <?php echo ($params->get('show_'.$panel, $val) == 0) ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_HIDE'); ?></option>
								<option value="1" <?php echo ($params->get('show_'.$panel, $val) > 0) ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_SHOW'); ?></option>
							</select>
						</td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
<?php echo JHtml::_('sliders.end'); ?>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELDSET_CONTENT'); ?></span></legend>
		<fieldset>
			<div class="input-wrap">
				<?php echo $this->lists['content']; ?>
			</div>
		</fieldset>
	</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
	<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
	<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
