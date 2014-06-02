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

$htmlHelper	  = new PublicationsHtml();

// Get hub config
$juri 	 = JURI::getInstance();
$jconfig = JFactory::getConfig();
$site 	 = $jconfig->getValue('config.live_site') 
	? $jconfig->getValue('config.live_site') 
	: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

$dateFormat = '%d %b %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = false;
}

$text = ($this->task == 'edit' 
	? JText::_('Edit') . ' #' . $this->pub->id . ' (v.' . $this->row->version_label . ')' 
	: JText::_('New'));

JToolBarHelper::title(JText::_('Publication') . ': [ ' . $text . ' ]', 'addedit.png');
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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');

$elements 	= new PublicationsElements($data, $customFields);
$fields 	= $elements->render();
$schema 	= $elements->getSchema();

// Admins can always edit
$canedit 	= 1;
$editor 	= JFactory::getEditor();

$now 		= JFactory::getDate()->toSql();

$status 	= $htmlHelper->getPubStateProperty($this->row, 'status');
$class 		= $htmlHelper->getPubStateProperty($this->row, 'class');

$v = $this->version == 'default' ? '' : '?v=' . $this->version;

// Build the path for publication attachments
$base_path 	= $this->config->get('webpath');
$path 		= $htmlHelper->buildPath($this->pub->id, $this->pub->version_id, $base_path, $this->pub->secret);	

// Instantiate the sliders object
jimport('joomla.html.pane');
$tabs = JPane::getInstance('sliders');

$rating = $this->pub->rating == 9.9 ? 0.0 : $this->pub->rating;

switch ($this->row->state) 
{
	case 1:
	case 0: 
	default:   
		$date_label = JText::_('Published');         
		break;
	case 4:  
		$date_label = JText::_('Finalized');         
		break;
	case 6:  
		$date_label = JText::_('Archived');         
		break;
}

$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}

$params = new $paramsClass($this->row->params);

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

?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'resetrating') {
		if (confirm('Are you sure you want to reset the Rating to Unrated? \nAny unsaved changes to this content will be lost.')){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	if(pressbutton == 'publish') {
		form.admin_action.value = 'publish';
		submitform( 'save' );
		return;
	}
	
	if(pressbutton == 'revert') {
		form.admin_action.value = 'revert';
		submitform( 'save' );
		return;
	}
	
	if(pressbutton == 'message') {
		form.admin_action.value = 'message';
		submitform( 'save' );
		return;
	}
	
	if(pressbutton == 'unpublish') {
		form.admin_action.value = 'unpublish';
		submitform( 'save' );
		return;
	}
	
	if(pressbutton == 'republish') {
		form.admin_action.value = 'republish';
		submitform( 'save' );
		return;
	}

	// do field validation
	if (form.title.value == ''){
		alert( 'Content item must have a title' );
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
<form action="index.php" method="post" name="adminForm" id="resourceForm" class="editform">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
	<table class="admintable">
		<tr>
			<th>
				Publications &raquo;
				Publication #<?php echo $this->pub->id; ?> &raquo; 
				Version <?php echo $this->pub->version_label.' ('.$status.')'; ?>
			</th>
			<th></th>
		</tr>
		<tr>
			<td>
		<table class="statustable">
			<tbody>
				<tr>
					<td class="key"><label for="title">Title:</label></td>
					<td><?php if($canedit) { ?><input type="text" name="title" id="title" size="80" maxlength="250" value="<?php echo htmlentities(stripslashes($this->row->title), ENT_COMPAT, 'UTF-8', ENT_QUOTES); ?>" /><?php } else { echo htmlentities(stripslashes($this->row->title), ENT_COMPAT, 'UTF-8', ENT_QUOTES); }?></td>
				</tr>
				<tr>
					<td class="key"><label>Category:</label></td>
					<td><?php echo $this->lists['category']; ?>
						&nbsp; <label>Master Type:</label>
						<?php echo $this->pub->base; ?>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="alias">Alias:</label></td>
					<td><input type="text" name="alias" id="alias" size="80" maxlength="250" value="<?php echo htmlentities(stripslashes($this->pub->alias), ENT_COMPAT, 'UTF-8', ENT_QUOTES); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label>Project:</label></td>
					<td><?php echo $this->pub->project_title; ?></td>
				</tr>
				<tr>
					<td class="key"><label>URL:</label></td>
					<td><a href="<?php echo trim($site, DS) .'/publications/' . $this->pub->id . $v; ?>" target="_blank"><?php echo trim($site, DS) .'/publications/' . $this->pub->id . $v; ?></a></td>
				</tr>
			</tbody>
		</table>
		
		<table class="statustable">
			<tbody>
				<tr>
					<td>
						<label>Synopsis (250 chars. max):
						<textarea name="abstract" id="pub_abstract" cols="40" rows="3" class="pubinput"><?php echo $this->row->abstract; ?></textarea>
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<label>Abstract/Description
						<textarea name="description" id="pub_description" cols="40" rows="10" class="pubinput"><?php echo $this->row->description; ?></textarea>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<table class="statustable metadata">
			<caption><?php echo JText::_('Metadata'); ?></caption>
			<tbody>
				<tr>
					<td>
						<?php echo $fields; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="statustable">
			<tbody>
				<tr>
					<td>
						<label><?php echo JText::_('Release Notes'); ?>  - <?php echo JText::_('Version').' '.$this->row->version_label; ?> (Release #<?php echo $this->row->version_number; ?>)
						<textarea name="release_notes" id="release_notes" cols="40" rows="10" class="pubinput"><?php echo $this->row->release_notes; ?></textarea>	
						</label>						
					</td>
				</tr>
			</tbody>
		</table>
	</td>
	</tr>
	</table>
	</fieldset>
</div>
<div class="col width-50 fltrt">
<?php 
		echo $tabs->startPane("content-pane");
		echo $tabs->startPanel('Publication Management','publish-page');
?>
		<table class="paramlist admintable">
			<tbody>
				<tr>
					<td class="paramlist_key"><label>Status:</label></td>
					<td class="status">
						<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
						<?php if($this->row->state == 3 || $this->row->state == 4 || $this->row->state == 5 ) { 
							if($this->pub_allowed) {
								echo '<span class="allowed">'.JText::_('Required fields are complete, publication allowed').'</span>';
							}
							else { ?>
								<span class="disallowed"><?php echo JText::_('Publication cannot be released. Missing '); 
									$missing = '';	
									foreach ($this->checked as $key=>$value) {
										if($value ==  0) {
											$missing .= '<strong>'.$key.'</strong>, ';
										}
									}
									$missing = substr($missing,0,strlen($missing) - 2);
									echo $missing.'.';
								?>
								</span>
						<?php	}
						 } ?>
					</td>
				</tr>
				<?php if ($this->row->state == 1 || $this->row->state == 5) { ?>
				<tr>
					<td class="key"><?php echo ($this->row->published_up > $now || $this->row->state == 5) 
					? JText::_('Will publish') : JText::_('Released') ; ?></td>
					<td>
						<label>
							<input type="text" id="published_up" name="published_up" value="<?php echo $this->row->published_up; ?>" />
						</label>
					</td>
				</tr>	
				<?php } ?>
				<tr>
					<td class="key"><?php echo JText::_('Message to pub creator and authors'); ?>:</td>
					<td>
						<textarea name="message" id="message" rows="5" cols="50"></textarea>				
					</td>
				</tr>
				<tr>
					<td class="paramlist_key"><?php echo JText::_('Options'); ?>:</td>
					<td class="puboptions">
						<input type="hidden" name="admin_action" value="" />
						<input type="submit" value="<?php echo JText::_('Send message'); ?>" class="btn" id="do-message" onclick="javascript: submitbutton('message')" /> 
					<?php if($this->row->state == 1) { ?>
						<input type="submit" value="<?php echo JText::_('Unpublish Version'); ?>" class="btn" id="do-unpublish" onclick="javascript: submitbutton('unpublish')" />
					<?php } else if($this->row->state == 0) { ?>
						<input type="submit" value="<?php echo JText::_('Re-publish Version'); ?>" class="btn" id="do-republish" onclick="javascript: submitbutton('republish')" />
					<?php } else if($this->row->state == 5) { ?>
						<input type="submit" value="<?php echo JText::_('Approve &amp; Publish'); ?>" class="btn" id="do-publish" <?php if($this->pub_allowed) { ?> onclick="javascript: submitbutton('publish')" <?php } ?> />
						<input type="submit" value="<?php echo JText::_('Revert to Draft'); ?>" class="btn" id="do-revert" onclick="javascript: submitbutton('revert')" />
					<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">Submitter:</td>
					<td><?php echo $this->submitter->name; ?></td>
				</tr>
		<?php if($this->row->state == 5) { ?>
				<tr>
					<td class="paramlist_key">Submitted:</td>
					<td><?php echo $this->row->submitted; ?></td>
				</tr>
		<?php } else if($this->row->state == 1 || $this->row->state == 0)  { ?>
			<?php if($this->row->submitted != '0000-00-00 00:00:00') { ?>
					<tr>
						<td class="paramlist_key">Submitted</td>
						<td><?php echo $this->row->submitted; ?></td>
					</tr>
			<?php } ?>
			<?php if($this->row->accepted != '0000-00-00 00:00:00') { ?>
					<tr>
						<td class="paramlist_key">Accepted</td>
						<td><?php echo $this->row->accepted; ?></td>
					</tr>
			<?php } ?>
		<?php } else  { ?>
				<tr>
					<td class="paramlist_key"><?php echo $date_label; ?></td>
					<td>
						<?php echo $this->row->published_up != NULL && $this->row->published_up != '0000-00-00 00:00:00' 
						? $this->row->published_up
						: 'N/A'; ?>
					</td>
				</tr>
		<?php } ?>
		<?php if($this->row->state == 0) { ?>
			<tr>
				<td class="paramlist_key">Unpublished</td>
				<td>
					<?php echo $this->row->published_down != NULL && $this->row->published_down != '0000-00-00 00:00:00' 
					? JHTML::_('date', $this->row->published_down, $dateFormat, $tz)
					: 'N/A'; ?>
				</td>
			</tr>
		<?php } ?>
		<?php if($this->row->doi) { ?>
				<tr>
					<td class="paramlist_key">DOI</td>
					<td><?php echo $this->row->doi ? 'doi:'.$this->row->doi : 'N/A'; ?></td>
				</tr>
		<?php } ?>
		<?php if($this->config->get('issue_arch') && $this->row->ark) { ?>
				<tr>
					<td class="paramlist_key">ARK</td>
					<td><?php echo $this->row->ark ? 'ark:'.$this->row->ark : 'N/A'; ?></td>
				</tr>
		<?php } ?>
		<?php if ($this->row->doi || $this->row->ark || $this->row->state != 3) { ?>
				<tr>
					<td colspan="2" class="archival-package">
						<?php if (file_exists($this->archPath)) { ?>
							<a href="<?php echo str_replace(JPATH_ROOT, '', $this->archPath); ?>" class="archival">Archival package</a> &nbsp;&nbsp;<a href="index.php?option=com_publications&amp;task=archive&amp;controller=items&amp;pid=<?php echo $this->row->publication_id; ?>&amp;vid=<?php echo $this->row->id; ?>&amp;version=<?php echo $this->version; ?>">[repackage]</a>
						<?php  }  else { ?>
						<a href="index.php?option=com_publications&amp;task=archive&amp;controller=items&amp;pid=<?php echo $this->row->publication_id; ?>&amp;vid=<?php echo $this->row->id; ?>&amp;version=<?php echo $this->version; ?>" class="archival">Produce archival package</a>
						<?php } ?>
					</td>
				</tr>
		<?php } ?>
				<tr>
					<td class="paramlist_key">Created:</td>
					<td><input type="hidden" name="created_by_id" value="<?php echo $this->row->created_by; ?>" /><?php echo ($this->row->created != '0000-00-00 00:00:00') ? $this->row->created.'</td></tr><tr><td class="paramlist_key">Created By:</td><td>'.$this->row->created_by_name : 'New resource'; ?></td>
				</tr>
				<tr>
					<td class="paramlist_key">Modified:</td>
					<td><input type="hidden" name="modified_by_id" value="<?php echo $this->row->modified_by; ?>" /><?php echo ($this->row->modified != '0000-00-00 00:00:00') ? $this->row->modified.'</td></tr><tr><td class="paramlist_key">Modified By:</td><td>'.$this->row->modified_by_name : 'Not modified';?></td>
				</tr>
				<?php if($this->row->state == 1 || $this->row->state == 0) { ?>
				<tr>
					<td class="paramlist_key">Ranking:</td>
					<td>
						<?php echo $this->pub->ranking; ?>/10
						<?php if ($this->pub->ranking != '0') { ?>
							<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" /> 
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">Rating:</td>
					<td>
						<?php echo $rating.'/5.0 ('.$this->pub->times_rated.' reviews)'; ?>
						<?php if ( $rating != '0.0' ) { ?>
							<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" /> 
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
?>	
	<fieldset class="adminform">
		<legend>Content</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td><?php echo $this->lists['content']; ?></td>
					</tr>
				</tbody>
			</table>
	</fieldset>
	<fieldset class="adminform">
		<legend>Authors</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td><?php echo $this->lists['authors']; ?></td>
					</tr>
				</tbody>
			</table>
	</fieldset>
	<fieldset class="adminform">
		<legend>Tags</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td><input type="text" name="tags" id="tags" value="<?php echo $this->lists['tags']; ?>" size="80" /></td>
					</tr>
				</tbody>
			</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_PUBLICATIONS_SHOW_HIDE'); ?></legend>
		<p class="hint"><?php echo JText::_('COM_PUBLICATIONS_SHOW_HIDE_HINT'); ?></p>
			<table class="admintable showhide">				
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
	</fieldset>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
	<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
	<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	
	<div class="clr"></div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
