<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Module_Helper');
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">

	 <div class="three columns first">
		<div id="kb" class="mainsection">
			<h3><a href="/kb/">Knowledge Base</a></h3>
	 		<p>Have a question or looking for more information about a <a href="/kb/tools/">tool</a> 
			or resource? Try browsing our <a href="/kb/">knowledge base</a>! 
			You can find answers to frequently asked questions, helpful tips, and any 
			other information we thought might be useful.</p>
		</div>
		<?php echo Hubzero_Module_Helper::renderModules( 'tcleft' ); ?>
	</div>

	<div class="three columns second">
		<div id="na" class="mainsection">
			<h3><a href="answers/">Answers</a></h3>
			<p>Couldn't find an answer to your question in our <a href="/kb/">knowledge base</a>? 
			Try asking your fellow hub members! <a href="/answers/question/new/">Ask questions</a>, post answers, or <a href="/answers/search/">search</a> 
			for answered questions. You just may find answers to questions you didn't 
			even know you had.</p>
		</div>
		<?php echo Hubzero_Module_Helper::renderModules( 'tcmiddle' ); ?>
	</div>

	<div class="three columns third">
		<div id="rp" class="mainsection">
			<h3><a href="/feedback/report_problems/">Report Problems</a></h3>
			<p><a href="/feedback/report_problems/">Report problems</a> with our form and have your problem entered into our <a href="/support/tickets/">ticket tracking 
			system</a>. We guarantee a response! After submitting a report you can track its 
			progress, even add comments or notes, and we will always notify you of any 
			updates!</p>
		</div>

		<h3>Feedback</h3>
		<p>Have an idea for how we can improve? Feel we could be doing something better? 
		Send us your suggestions and comments with our feedback form. We want to hear from you!</p>
		<ul>
			<li><a href="/feedback/report_problems/">Report a problem</a></li>
			<li><a href="/feedback/success_story/">Share a success story</a></li>
			<li><a href="/feedback/suggestions/">Send us your suggestions</a></li>
		</ul>
	</div>

	<div class="clear"></div>
</div><!-- / .section -->

