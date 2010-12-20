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
<script type="text/javascript" src="media/system/js/jquery-1.4.2.js"></script>
<script src="http://cdn.jquerytools.org/1.2.4/jquery.tools.min.js"></script>
<script type="text/javascript">var $jQ = jQuery.noConflict();</script>
<link rel="stylesheet" type="text/css" media="screen" href="/templates/fresh/css/main.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="/templates/fresh/html/com_resources/resources.css"/>
<div id="pretext">
<h1>Resource Creation</h1>
<p>
	Do you with to add your resource? You will need to <strong>title</strong> your resource, and have an <strong>abstract</strong> handy (or write one as you contribute).
	Please have a <strong>picture</strong> representation of your contribution <em>(preferrably jpeg format)</em> if you want a image to appear when people find your resource as well as any <strong>pdfs</strong> or other documents you want users to download and view.
</p>
<!-- https://nees12.neeshub.org/contribute/?task=quickcontribute&type=36&tmpl=component -->
<script type="text/javascript">
function DoNext()
{
	$jQ('#placement').append('<iframe style="width:100%; height:100%;" id="contributionframe" src="/contribute/?step=1&type=36&tmpl=component"></iframe>');
	$jQ('#pretext').fadeOut();
}
</script>
<p style="align:center">	<CENTER>
	<form id="hubform">
		<input  style="width:200px;height:40px;" type="button" value="I'm ready, lets start" ONCLICK="DoNext()"/>
	</form>
</p> </CENTER>
</div>
<div style="height:700px"  id="placement"></div>

