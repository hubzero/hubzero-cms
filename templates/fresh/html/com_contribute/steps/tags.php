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
?>


<?php
if(isset($_POST['submit'])) {
foo();
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
	<form action="index.php" method="post" id="hubForm" name="tagform">
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_TAGS_WHAT_ARE_TAGS'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_TAGS_EXPLANATION'); ?></p>
			
			<h4>Key Identifiers</h4>
			<p> Additionally NEESAcademy provides a set of special identifiers to help you indicate your resources intended audience and topic area. Select from the list keywords to describe your resource.<br/> Be sure to press "Add/Update" to confirm your changes.</p>
		</div>
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />

			<h3><?php echo JText::_('COM_CONTRIBUTE_TAGS_ADD'); ?></h3>
<?php if (count($this->fats) > 0) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_CONTRIBUTE_TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
				<?php
				foreach ($this->fats as $key => $value) 
				{
					if ($key && $value) {
						echo '<label><input class="option" type="radio" name="tagfa" value="' . $value . '"';
						if ($this->tagfa == $value) {
							echo ' checked="checked "';
						}
						echo ' /> '.$key.'</label>'."\n";
					}
				}
				?>
			</fieldset>
<?php } ?>				
			<label>
				<?php echo JText::_('COM_CONTRIBUTE_TAGS_ASSIGNED'); ?>:
				<?php
				JPluginHelper::importPlugin( 'tageditor' );
				$dispatcher =& JDispatcher::getInstance();
				
				$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$this->tags,'')) );
				
				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<textarea name="tags" id="tags-men" rows="6" cols="35">'. $this->tags .'</textarea>'."\n";
				}
				?>
			</label>
			<p><?php echo JText::_('COM_CONTRIBUTE_TAGS_NEW_EXPLANATION'); ?></p>
		</fieldset>
		
		<script language="javascript" type="text/javascript">
		function addtagtext(element) {
			var newtext = document.getElementById(element).value;
			var field = document.getElementById("maininput");
			field.value += newtext + " ";
			field.focus();
			field.blur();
		}
		
		</script>
		<fieldset>
				<h3>Category</h3>
				<p>Categories  (You may open and choose as many options from this list as you like - they will be appended to the tags list at the top)</p>
				<select id="category" name="category" onchange="addtagtext('category');">
					<option value="" />None</option>
					<option value="data management" />Data Management</option>
					<option value="downloadable" />Downloadable</option>
					<option value="EOT" />Education, Outreach and Training</option>
					<option value="Simulation" />Simulation</option>
					<option value="telepresence" />Telepresence</option>
					<option value="visualization" />Visualization</option>
				</select>
				<!-- <input type="submit" value="Add/Update"/>  -->
			</fieldset>
		<?php if ($this->type == 10) { //notes (in the news)?>
		<fieldset>	
		<h3>In the News?</h3>
		<p><b>(Important)</b> Place this item in the news? Click to add to the news list</p>
				<input type="button" id="type" multiple="none" onClick="addtagtext('type');" value="highlight"></input>
		</fieldset>	
		<?php } else {?>
		<fieldset>
			<h3>Audience</h3>
			<p>Audience Levels (You may open and choose as many options from this list as you like - they will be appended to the tags list at the top)</p>
			<select id="audience" onchange="addtagtext('audience');">
				<option value="" />None</option>
				<option value="grades k-6" />Grades K-6</option>
				<option value="grades k-12" />Grades 6-12</option>
				<option value="undergraduate" />Undergraduate</option>
				<option value="graduate" />Graduate</option>
				<option value="professional" />Professional</option>
			</select>
			<!-- <input type="submit" value="Add/Update"/>  -->
		</fieldset>
		<fieldset>	
			<h3>Topic</h3>
			<p>Topics (You may open and choose as many options from this list as you like - they will be appended to the tags list at the top)</p>
				<select id="topic" onchange="addtagtext('topic');">
					<option value="" />None</option>
					<option value="topics solved by engineering">Topics Solved By Engineering</option>
					  <option value="science questions">Science Questions</option>
					  <option value="practical activities">Practical Activities</option>
					  <option value="homework activities">Homework Activities</option>
				</select>
		</fieldset>	
		<?php } ?>
		<?php if ($this->type == 67) { //multimedia?>
		<fieldset>			
		<h3>Multimedia Type</h3>
		<p>Multimedia Type (You may open and choose as many options from this list as you like - they will be appended to the tags list at the top)</p>
				<select id="type" onchange="addtagtext('type');">
					<option value="" />None</option>
			 	  <option value="webcast archive">Webcast Archive</option>
				  <option value="image gallery">Image Gallery</option>
				  <option value="youtube archive">Youtube Archive</option>
				</select> 
		</fieldset>		
		<?php } ?>
		
		<div class="clear"></div>
		<p id="nextsubmit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" id="nextbutton"/>
		</p>
	</form>
</div><!-- / .main section -->