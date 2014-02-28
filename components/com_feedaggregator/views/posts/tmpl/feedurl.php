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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Document');

?>

<script type="text/javascript">
jQuery(document).ready(function() 
{	
	jQuery('.fancybox-inline').fancybox();
});
</script>

<style>
img { display:block; margin-bottom:10px; }
.postpreview p { word-wrap:break-word;}
.postpreview h1 { font-family: san-serif; font-size: x-large; }
.postpreview { padding: 20px;}
</style>

<style>
.myButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #dcecfb;
	-webkit-box-shadow:inset 0px 1px 0px 0px #dcecfb;
	box-shadow:inset 0px 1px 0px 0px #dcecfb;
	background-color:#bedbfa;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #84bbf3;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:arial;
	font-size:15px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #528ecc;
}
.myButton:hover {
	background-color:#80b5ea;
}
.myButton:active {
	position:relative;
	top:1px;
}
.myButton:visted {
 color: white;
 }
</style>

<div id="content-header">
<h2><?php echo $this->title; ?></h2>
</div>
<div id="introduction" class="section">
<div class="aside">
	<h3>Questions?</h3>
	<ul>
		<li>
			<a class="fancybox-inline" href="#helpbox">Need Help?</a>
		</li>
	</ul>
</div>
<!-- / .aside -->
<div class="subject">
	<div class="grid">
		<div class="col span-half">
			<h3>What is the Feed Aggregator?</h3>
			<p>The Feed Aggregator is a component which allows feed managers 
			to collect articles from several RSS feeds and to selectively combine them into one site-sponsored RSS feed.</p>
		</div>
		<div class="col span-half omega">
			<h3>How do I read the aggregated feed?</h3>
			<p>Registered users may read the feed by simply clicking the button below. <br>The URL is importable into any RSS feed reader.</p>
			<p><a href="#feedbox" style="color: white; background-color: green;" class="myButton fancybox-inline">Generate RSS Feed</a></p>
		</div>
	</div>
</div>
<!-- / .subject -->
	<div class="clear">
	</div>
</div>

<div class="main section">
<div id="page-main" style="padding-bottom:50px;">
<br><br>
	
<!-- Help Dialog -->
<div style="display:none">		
	<div class="postpreview" id="helpbox">
	<h1>Feed Aggregator Info</h1>
	<p>In order to have the ability to access the administrative/managerial functions of the Feed Aggregator,
the user must be added to a group with an access level higher than a registered user. For instance, the user must be either
an author, editor, or publisher.</p>
	</div>
</div>

<div style="display:none">		
	<div class="postpreview" id="feedbox">
	<p>Generates URL to visit.</p>
	</div>
</div>

</div><!-- /.main section -->
</div> <!--  main page -->
