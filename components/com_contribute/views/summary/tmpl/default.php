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

$xhub =& XFactory::getHub();
$hubShortName = $xhub->getCfg('hubShortName');

$imagedir = DS.'components'.DS.$option.DS.'images'.DS;
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">
<div class="threecolumn leftmiddle">
	<?php echo ($this->getError()) ? $this->getError() : ''; ?>
	
	<div id="startcontributing">
		<p id="startbutton"><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=start'); ?>">Start a contribution &rsaquo;</a></p>
		<p>
			Become a contributor and share your work with the <?php echo $hubShortName; ?> community! Contributing content is easy. Our step-by-step 
			forms will guide you through the process.
		</p>
		<div class="clear"></div>
	</div>
	
	<h3>Shared infrastructure</h3>
	<p>
		The <?php echo $hubShortName; ?> is shared infrastructure to help individuals in the community collaborate and disseminate the results of their 
		work. Individuals, research groups, and even major research centers can disseminate their work by uploading it to the <?php echo $hubShortName; ?>.
	</p>
	
	<h4>Contribute content</h4>
	<p>
		There are several different types of content hosted on the <?php echo $hubShortName; ?>. The following links tell you how to prepare and submit
		materials for each type:
	</p>
	
	<ul>
		<li><a href="/contribute/animations">Animations</a></li>
		<li><a href="/contribute/downloads">Downloads</a></li>
		<li><a href="/contribute/publications">Publications</a></li>
		<li><a href="/contribute/presentations">Online Presentations</a></li>
		<li><a href="/contribute/teachingmaterials">Teaching Materials</a></li>
		<li><a href="/contribute/tools">Simulation Tools</a></li>
	</ul>
	
	<h4>Present your work</h4>
	<p>
		Your contributions will become part of the <?php echo $hubShortName; ?> and your colleagues and general <?php echo $hubShortName; ?> users will be able to locate 
		them there.
	</p>
	
	<h4>Intellectual Property Considerations</h4>
	<p>
		All materials contributed must have clearly defined rights and privileges. Online presentations and
		instructional material are normally licensed under <a href="legal/cc/" title="Learn more about Creative Commons">Creative Commons 2.5</a>. 
		Read <a href="legal/licensing/">more details</a> about our licensing policies.
	</p>

	<h4>Questions about contributing?</h4>
	<p>
		We hope that our self-service upload process is intuitive and easy to use. If you encounter any problems during the 
		upload process or need assistance of any kind, please <a href="feedback/report_problems/">file a trouble report</a>.
	</p>

	<h2>What can I contribute?</h2>

	<div class="withimages">
		<h3><a href="/contribute/tools/">Tools</a></h3>
		<img src="<?php echo $imagedir; ?>contribute-tool.jpg" alt="" />
		<p>A simulation tool is software that allows users to run a specific type of calculation. Most of these tools are 
		built with our <a href="http://www.rappture.org">Rappture toolkit</a>, so they have a consistent, user-friendly interface.</p>
					
		<h3><a href="/contribute/presentations/">Online Presentations</a></h3>
		<img src="<?php echo $imagedir; ?>contribute-presentation.jpg" alt="" />
		<p>An Online Presentation can be a research seminar,
		a graduate or undergraduate level seminar, or lectures for a complete class. An online presentation consists of an abstract, 
		short bio about the author, presentation slides (PDF, PPT, etc.) as well as a voiced presentation (Macromedia Breeze).</p>
	
		<h3><a href="/contribute/teachingmaterials/">Teaching Materials</a></h3>
		<img src="<?php echo $imagedir; ?>contribute-teachingmaterial.jpg" alt="" />
		<p>Teaching Materials are supplementary materials that don't quite fit into any of the previous categories, such
		as homework assignments, study notes, guides, etc.</p>

		<h3><a href="/contribute/animations/">Animations</a></h3>
		<img src="<?php echo $imagedir; ?>contribute-animation.jpg" alt="" />
		<p>An animation is a short (usually <a href="http://www.adobe.com/products/flashplayer/">Flash</a>-based) video 
		that illustrates a concept. Browse through <a href="resources/animations/">available animations</a> in our resources section.</p>
		
		<h3><a href="/contribute/publications/">Publications</a></h3>
		<img src="<?php echo $imagedir; ?>contribute-publication.jpg" alt="" />
		<p>A Publication is a paper you have written that has been published in some manner. The topic of the paper 
		should be relevant to the community and may even include references from the <?php echo $hubShortName; ?>.</p>
		<p><i>NOTE:</i> Please don't upload any publication if the copyright is owned by a publisher without explicit 
		permission from the publisher. However, if you have a preprint or an unpublished work, it can be uploaded here 
		to help explain the details for a related tool, or as a reference for an online lecture.</p>

		<h3><a href="/contribute/downloads/">Downloads</a></h3>
		<img src="<?php echo $imagedir; ?>contribute-download.jpg" alt="" />
		<p>A download is a type of resource that users can download and use on their own computer. It could be source 
		code for a tool you developed or a set of data files.</p>
	</div>
</div>
<div class="threecolumn farright">
	<div id="submissions">
		<h3>Submissions in progress:</h3>
		<?php
		$module = JModuleHelper::getModule('mod_mysubmissions');
		echo JModuleHelper::renderModule($module);
		?>
	</div>
	<div class="whatisthis">
		<h4>What's this?</h4>
		<p>Once you've started a new contribution, you can proceed at your leisure. Stop half-way through and 
		watch a presentation, go to lunch, even close the browser and come back a different day! Your 
		contribution will be waiting just as you left it, ready to continue at any time.</p>
	</div>
</div>
<div class="clear"></div>
</div>