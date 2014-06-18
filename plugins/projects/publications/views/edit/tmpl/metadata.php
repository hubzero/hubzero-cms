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

// Determine pane title
$ptitle = '';
if ($this->version == 'dev')
{
	$ptitle .= $this->last_idx > $this->current_idx && $this->row->metadata
			? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_EDIT_METADATA'))
			: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_ADD_METADATA')) ;
}
else
{
	$ptitle .= ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_METADATA'));
}

// Parse data
$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->row->metadata, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $this->htmlHelper->_txtUnpee($match[2]);
	}
}

$customFields = $this->customFields && $this->customFields != '{"fields":[]}' ? $this->customFields : '{"fields":[{"default":"","name":"citations","label":"Citations","type":"textarea","required":"0"}]}';

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');

$elements 	= new PublicationsElements($data, $customFields);
$fields 	= $elements->render();
$schema 	= $elements->getSchema();

$canedit = (
	$this->pub->state == 3
	|| $this->pub->state == 4
	|| $this->pub->state == 5
	|| in_array($this->active, $this->mayupdate))
	? 1 : 0;

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form">
	<?php echo $this->project->provisioned == 1
				? $this->helper->showPubTitleProvisioned( $this->pub, $this->route)
				: $this->helper->showPubTitle( $this->pub, $this->route, $this->title); ?>
		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
			<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
			<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="step" value="metadata" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
			<?php if($this->project->provisioned == 1 ) { ?>
			<input type="hidden" name="task" value="submit" />
			<?php } ?>
		</fieldset>
<?php
	// Draw status bar
	$this->contribHelper->drawStatusBar($this, 'metadata', $this->typeParams->get('show_metadata', 0));

	if ($this->move) {
		$panel_number = 1;
		while ($panel = current($this->panels)) {
		    if ($panel == $this->active) {
		        $panel_number = key($this->panels) + 1;
		    }
		    next($this->panels);
		}
	}
// Section body starts:
?>
	<div id="pub-editor" class="pane-desc">
	  <div id="c-pane" class="columns">
		 <div class="c-inner">
			<?php if ($canedit) { ?>
			<span class="c-submit"><input type="submit" class="btn" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" <?php if(count($this->checked['description']) == 0) { echo 'class="disabled"'; } ?> class="c-continue" id="c-continue" /></span>
			<?php } ?>
		<h4><?php echo $ptitle; ?></h4>

		<?php if ($canedit) { ?>
					<?php
					if ($fields) { ?>
						<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_METADATA_WRITE'); ?></p>
						<p class="hint rightfloat"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PLEASE_USE'); ?> <a href="/wiki/Help:WikiFormatting" rel="external" class="popup"><?php echo JText::_('WIKI_FORMATTING'); ?></a>. <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NOTICE_NO_HTML_ALLOWED'); ?></p>
						<div class="metadata-compose">
						<?php echo $fields; ?>
						</div>
					<?php } else { ?>
						<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_NO_METADATA_COLLECTED'); ?></p>
					<?php } ?>
			<?php } else {
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'parser.php');

				$parser = WikiHelperParser::getInstance();
				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => '',
					'pagename' => 'projects',
					'pageid'   => '',
					'filepath' => '',
					'domain'   => ''
				);

				$metadata = $this->htmlHelper->processMetadata(
					$this->row->metadata,
					$this->_category,
					$parser,
					$wikiconfig,
					0
				);
			?>
				<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>

			<?php 	echo $metadata['html']
				? $metadata['html']
				: '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
			} ?>
		 </div>
	   </div>
	</div>
</form>
