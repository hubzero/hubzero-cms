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

$remotes = array();
$sendTo = $this->sendTo;

?>
<div id="abox-content">
<h3><?php echo $sendTo == 'local' ? JText::_('COM_PROJECTS_UNSHARE_PROJECT_FILES') : JText::_('COM_PROJECTS_SHARE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" class="" action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->project->id); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="shareit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="remotedir" value="<?php echo $this->remotedir; ?>" />
		<input type="hidden" name="remoteid" value="<?php echo $this->remoteid; ?>" />
		<input type="hidden" name="sendto" value="<?php echo $sendTo; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p class="send_to"><span class="<?php echo $sendTo == 'local' ? 'send_to_local' : 'send_to_remote'; ?>">
			<span>&nbsp;</span></span>
		</p>

		<ul class="sample">
		<?php foreach ($this->items as $element)
		{
			$remote = NULL;
			foreach ($element as $type => $item)
			{
				// Get type and item name
			}

			// Remote file?
			foreach ($this->services as $servicename)
			{
				$remoteFile = preg_match("/" . $servicename . ":/", $item) ? preg_replace("/" . $servicename . ":/", "", $item) : '';

				if ($remoteFile)
				{
					$remote = $this->connect->getRemoteItem($servicename, $remoteFile);
					$remotes[] = $remote;
				}
			}

			// Cam only send remote to local or local to remote
			if ($sendTo == 'remote' && $remote)
			{
				continue;
			}
			elseif ($sendTo == 'local' && !$remote)
			{
				continue;
			}

			// Display list item with file data
			$this->view('default', 'selected')
			     ->set('connect', $this->connect)
			     ->set('item', $item)
			     ->set('remote', $remote)
			     ->set('type', $type)
			     ->set('action', 'delete')
			     ->set('multi', 'multi')
			     ->display();
		} ?>
		</ul>

		<?php if ($sendTo == 'remote')
			{
				$i = 0;

				// Local files
				foreach ($this->services as $servicename)
				{
					$configs = $this->connect->getConfigs($servicename);
		?>
			<label class="sharing-option">
				<input type="radio" name="service" value="<?php echo $servicename; ?>" <?php if($i == 0) { echo 'checked="checked"'; } ?> />
				<?php echo JText::_('COM_PROJECTS_SHARE_FILES_WITH')
					. ' <span class="' . $servicename . '">' . $configs['servicename'] . '</span>'; ?>
			</label>

			<?php
			// Extra options for Google
			if ($servicename == 'google') {
			?>
			<div class="sharing-option-extra">
				<label class="sharing-option">
					<input type="radio" name="convert" value="1" />
					<?php echo JText::_('COM_PROJECTS_FILES_SHARE_GOOGLE_CONVERT'); ?>
				</label>
				<span class="faded ipadded mini block"><?php echo JText::_('COM_PROJECTS_FILES_SHARE_GOOGLE_CONVERT_NOTE'); ?></span>
				<label class="sharing-option">
					<input type="radio" name="convert" value="0" checked="checked" />
					<?php echo JText::_('COM_PROJECTS_FILES_SHARE_GOOGLE_NO_CONVERT'); ?>
				</label>
			</div>
			<?php }
				$i++;
			}
		}
		elseif (count($remotes) == 1) {
			$remote = $remotes[0];
			$remote_resource = json_decode($remote->remote_resource);

			if ($remote->service == 'google')
			{
				// Do we deal with Google format?
				if ($remote_resource->googleFormat)
				{
					// Get all available export formats for the MIME type
					$formats = ProjectsGoogleHelper::getGoogleConversionFormat($remote_resource->mimeType, true);
					if (!empty($formats))
					{
			?>
				<h4><?php echo JText::_('COM_PROJECTS_FILES_SHARING_CHOOSE_CONVERSION_FORMAT'); ?></h4>
				<div class="sharing-option-extra">
			<?php
						$i = 0;
						foreach ($formats as $format)
						{
			?>
							<label>
								<input type="radio" name="format" value="<?php echo $format; ?>" <?php if($i == 0) { echo 'checked="checked"'; }?> />
								<?php echo $format; ?>
							</label>
			<?php
						$i++;
						}
			?>
				</div>
			<?php
					}
				}
	 		}
		 } else { // multiple ?>
			<p class="notice"><?php echo JText::_('COM_PROJECTS_FILES_SHARING_MULTIPLE_NOTE_CONVERSION'); ?></p>
		<?php } ?>
		<p class="submitarea">
			<input type="submit" value="<?php echo $sendTo == 'local'
			? JText::_('COM_PROJECTS_FILES_ACTION_UNSHARE')
			: JText::_('COM_PROJECTS_FILES_ACTION_SHARE'); ?>" id="submit-ajaxform" class="btn" />
			<input type="reset" id="cancel-action"  class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
<?php } ?>
</div>