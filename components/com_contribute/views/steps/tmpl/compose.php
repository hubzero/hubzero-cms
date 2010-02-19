<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$this->row->fulltext = ($this->row->fulltext) ? stripslashes($this->row->fulltext): stripslashes($this->row->introtext);

$type = new ResourcesType( $this->database );
$type->load( $this->row->type );

$fields = array();
if (trim($type->customFields) != '') {
	$fs = explode("\n", trim($type->customFields));
	foreach ($fs as $f) 
	{
		$fields[] = explode('=', $f);
	}
} else {
	$flds = $config->get('tagstool');
	$flds = explode(',',$flds);
	foreach ($flds as $fld) 
	{
		$fields[] = array($fld, $fld, 'textarea', 0);
	}
}

if (!empty($fields)) {
	for ($i=0, $n=count( $fields ); $i < $n; $i++) 
	{
		preg_match("#<nb:".$fields[$i][0].">(.*?)</nb:".$fields[$i][0].">#s", $this->row->fulltext, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$fields[$i][0].'>','',$match);
			$match = str_replace('</nb:'.$fields[$i][0].'>','',$match);
		} else {
			$match = '';
		}
		
		// Explore the text and pull out all matches
		array_push($fields[$i], $match);
		
		// Clean the original text of any matches
		$this->row->fulltext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$this->row->fulltext);
	}
	$this->row->fulltext = trim($this->row->fulltext);
}
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php
$view = new JView( array('name'=>'steps','layout'=>'steps') );
$view->option = $this->option;
$view->step = $this->step;
$view->steps = $this->steps;
$view->id = $this->id;
$view->progress = $this->progress;
$view->display();
?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_EXPLANATION'); ?></p>

			<p><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_ABSTRACT_HINT'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_ABOUT'); ?></h3>
			<label>
				<?php echo JText::_('COM_CONTRIBUTE_COMPOSE_TITLE'); ?>: <span class="required"><?php echo JText::_('COM_CONTRIBUTE_REQUIRED'); ?></span>
				<input type="text" name="title" maxlength="250" value="<?php echo htmlentities(stripslashes($this->row->title), ENT_QUOTES); ?>" />
			</label>
		
			<label>
				<?php echo JText::_('COM_CONTRIBUTE_COMPOSE_ABSTRACT'); ?>:
				<textarea name="fulltext" cols="50" rows="20"><?php echo htmlentities(stripslashes($this->row->fulltext)); ?></textarea>
			</label>
		</fieldset><div class="clear"></div>

		<div class="explaination">
			<p><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('COM_CONTRIBUTE_COMPOSE_DETAILS'); ?></h3>
<?php 
//foreach ($allnbtags as $tagname => $tagcontent) 
foreach ($fields as $field)
{ 
	$tagcontent = preg_replace('/<br\\s*?\/??>/i', "", end($field));
?>
			<label>
				<?php echo stripslashes($field[1]); ?>: <?php echo ($field[3] == 1) ? '<span class="required">'.JText::_('COM_CONTRIBUTE_REQUIRED').'</span>': ''; ?>
				<?php if ($field[2] == 'text') { ?>
				<input type="text" name="<?php echo 'nbtag['.$field[0].']'; ?>"><?php echo htmlentities(stripslashes($tagcontent), ENT_QUOTES); ?></input>
				<?php } else { ?>
				<textarea name="<?php echo 'nbtag['.$field[0].']'; ?>" cols="50" rows="6"><?php echo htmlentities(stripslashes($tagcontent)); ?></textarea>
				<?php } ?>
			</label>
<?php 
} 
?>
			<input type="hidden" name="published" value="<?php echo $this->row->published; ?>" />
			<input type="hidden" name="standalone" value="1" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="type" value="<?php echo $this->row->type; ?>" />
			<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
			<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
			<input type="hidden" name="publish_up" value="<?php echo $this->row->publish_up; ?>" />
			<input type="hidden" name="publish_down" value="<?php echo $this->row->publish_down; ?>" />
	 
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
		</fieldset><div class="clear"></div>
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" />
		</p>
	</form>
</div><!-- / .main section -->