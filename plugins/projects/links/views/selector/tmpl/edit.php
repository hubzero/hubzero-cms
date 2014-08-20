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

$route = $this->project->provisioned == 1
		? 'index.php?option=com_publications&task=submit&pid=' . $this->publication->id
		: 'index.php?option=com_projects&alias=' . $this->project->alias;

// Save Selection URL
$url = $this->project->provisioned ? JRoute::_( $route) : JRoute::_( 'index.php?option=com_projects&alias='
	. $this->project->alias . '&active=publications&pid=' . $this->publication->id);

$config = JComponentHelper::getParams( 'com_publications' );
$citationFormat = $config->get('citation_format', 'apa');

// Use new curation flow?
$useBlocks  = $config->get('curation', 0);

?>
<div id="abox-content" class="citation-edit">
<script src="/plugins/projects/links/js/selector.js"></script>
<h3><?php echo $this->row->id ? JText::_('PLG_PROJECTS_LINKS_SELECTOR_EDIT_CITATION') : JText::_('PLG_PROJECTS_LINKS_SELECTOR_ADD_CITATION'); ?>
	    <span class="abox-controls">
			<a class="btn btn-success active" id="b-add"><?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_SAVE_CITATION'); ?></a>
			<?php if ($this->ajax) { ?>
			<a class="btn btn-cancel" id="cancel-action"><?php echo JText::_('PLG_PROJECTS_LINKS_CANCEL'); ?></a>
			<?php } ?>
		</span></h3>
		<form id="add-cite" class="add-cite" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias); ?>">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
				<input type="hidden" name="alias" value="<?php echo $this->project->alias; ?>" />
				<?php if ($useBlocks) { ?>
				<input type="hidden" name="p" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="additem" />
				<?php if ($this->project->provisioned == 1) { ?>
					<input type="hidden" name="task" value="submit" />
					<input type="hidden" name="ajax" value="0" />
				<?php }  ?>
				<?php } else { ?>
					<input type="hidden" name="active" value="links" />
					<input type="hidden" name="action" value="savecite" />
				<?php } ?>
				<input type="hidden" name="cite[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="cite[affiliated]" value="1" />
			</fieldset>
			<?php if (!$this->row->id) { ?>
			<p class="requirement"><?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_ADD_CITATION_NOTE'); ?></p>
			<?php } ?>
			<div id="status-box"></div>
			<div class="cite-wrap">
				<label for="type">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_TYPE'); ?>: <span class="required"><?php echo JText::_('PLG_PROJECTS_LINKS_REQUIRED'); ?></span>
					<select name="cite[type]" id="type" class="inputrequired">
						<option value=""> - <?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_SELECT_TYPE'); ?> &mdash;</option>
						<?php
							foreach ($this->types as $t) {
								$sel = ($this->row->type == $t['id']) ? "selected=\"selected\"" : "";
								echo "<option {$sel} value=\"{$t['id']}\">{$t['type_title']}</option>";
							}
						?>
					</select>
				</label>
				<label for="title">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_TITLE'); ?>:  <span class="required"><?php echo JText::_('PLG_PROJECTS_LINKS_REQUIRED'); ?></span>
					<input type="text" name="cite[title]" id="title" size="30" maxlength="250" value="<?php echo $this->row->title; ?>" />
				</label>
				<div class="grid">
					<div class="col span6">
						<label for="year">
							<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_YEAR'); ?>:
							<input type="text" name="cite[year]" id="year" size="4" maxlength="4" value="<?php echo $this->row->year; ?>" />
						</label>
					</div>
					<div class="col span6 omega">
						<label for="month">
							<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_MONTH'); ?>:
							<input type="text" name="cite[month]" id="month" size="11" maxlength="50" value="<?php echo $this->row->month; ?>" />
						</label>
					</div>
				</div>

				<label for="author">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_AUTHORS'); ?>:
					<input type="text" name="cite[author]" id="author" size="30" value="<?php echo $this->row->author; ?>" />
					<span class="hint"><?php echo JText::_('Lastname, Firstname; Lastname, Firstname; Lastname ...'); ?></span>
				</label>

				<label for="journal">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_JOURNAL'); ?>:
					<input type="text" name="cite[journal]" id="journal" size="30" maxlength="250" value="<?php echo $this->row->journal; ?>" />
				</label>

				<label for="booktitle">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_BOOK_TITLE'); ?>:
					<input type="text" name="cite[booktitle]" id="booktitle" size="30" maxlength="250" value="<?php echo $this->row->booktitle; ?>" />
				</label>

				<div class="grid">
					<div class="col span4">
						<label for="volume">
							<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_VOLUME'); ?>:
							<input type="text" name="volume" id="volume" size="11" maxlength="11" value="<?php echo $this->row->volume; ?>" />
						</label>
					</div>
					<div class="col span4">
						<label for="number">
							<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_ISSUE'); ?>:
							<input type="text" name="number" id="number" size="11" maxlength="50" value="<?php echo $this->row->number; ?>" />
						</label>
					</div>
					<div class="col span4 omega">
						<label for="pages">
							<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_PAGES'); ?>:
							<input type="text" name="pages" id="pages" size="11" maxlength="250" value="<?php echo $this->row->pages; ?>" />
						</label>
					</div>
				</div>

				<label for="eprint">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_EPRINT'); ?>:
					<input type="text" name="cite[eprint]" id="eprint" size="30" maxlength="250" value="<?php echo $this->row->eprint; ?>" />
					<span class="hint"><?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_EPRINT_EXPLANATION'); ?></span>
				</label>

				<div class="grid">
					<div class="col span6">
						<label for="isbn">
							<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_ISBN'); ?>:
							<input type="text" name="cite[isbn]" id="isbn" size="11" maxlength="50" value="<?php echo $this->row->isbn; ?>" />
						</label>
					</div>
					<div class="col span6 omega">
						<label for="doi">
							<abbr title="<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_DOI'); ?>"><?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_DOI'); ?></abbr>:
							<input type="text" name="cite[doi]" id="doi" size="30" maxlength="250" value="<?php echo $this->row->doi; ?>" />
						</label>
					</div>
				</div>

				<label for="abstract">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_ABSTRACT'); ?>:
					<textarea name="cite[abstract]" id="abstract" rows="4" cols="10"><?php echo stripslashes($this->row->abstract); ?></textarea>
				</label>

				<label for="series">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_SERIES'); ?>:
					<input type="text" name="cite[series]" id="series" size="30" maxlength="250" value="<?php echo $this->row->series; ?>" />
				</label>

				<label for="edition">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_EDITION'); ?>:
					<input type="text" name="cite[edition]" id="edition" size="30" maxlength="250" value="<?php echo $this->row->edition; ?>" />
					<span class="hint"><?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_EDITION_EXPLANATION'); ?></span>
				</label>

				<label for="publisher">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_PUBLISHER'); ?>:
					<input type="text" name="cite[publisher]" id="publisher" size="30" maxlength="250" value="<?php echo $this->row->publisher; ?>" />
				</label>

				<label for="uri">
					<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_URL'); ?>:
					<input type="text" name="cite[uri]" id="uri" size="30" maxlength="250" value="<?php echo $this->row->url; ?>" />
				</label>
			</div>
			<div class="formatted-cite">
				<?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_FORMATTED'); ?>:
				<label for="formatted">
					<textarea name="cite[formatted]" id="formatted" rows="4" cols="10"><?php echo stripslashes($this->row->formatted); ?></textarea>
					<span class="hint"><?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_FORMATTED_EXPLANATION'); ?> <?php echo JText::_('PLG_PROJECTS_LINKS_SELECTOR_CITE_FORMATTED_FORMAT'); ?> <?php echo strtoupper($citationFormat); ?></span>
				</label>
			</div>
		</form>
</div>