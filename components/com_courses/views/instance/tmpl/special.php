<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

//links used in special menu drop down
$links = array(
	'/' => 'Home',
	'/about' => 'About',
	'/members' => 'Members',
	'/courses' => 'Courses',
	'/resources' => 'Resources',
	'/answers' => 'Questions &amp; Answers',
	'/citations' => 'Citations'
);

//get site config
$config =& JFactory::getConfig(); 

$no_html = JRequest::getVar("no_html", 0);
if(!$no_html) {
?>
<div id="special-course-pane">
	<div id="special-course-container">
		<div class="three columns first">
			<h1>
				<a href="/" title="<?php echo $config->getValue('sitename'); ?> Home"><?php echo $config->getValue('sitename'); ?></a>
			</h1>
			<p class="intro">
				You are currently viewing “<?php echo $this->course->get('description'); ?>” course powered by <?php echo $config->getValue('sitename'); ?>. The course areas for these partners have their own URL and additional capabilities not found in our standard courses.
			</p>
			
			<h2>Interested in Becoming a Partner?</h2>
			<p>
				Use <?php echo $config->getValue('sitename'); ?> to create a web presence for your center. <a href="/about/contact">Contact us</a> to get more details about establishing a partnership.
			</p>
		</div>
		<div class="three columns second">
			<h2>What is <?php echo $config->getValue('sitename'); ?>?</h2>
			<p>This web site is a resource for research, education and collaboration in your science area. It hosts various resources which will help you learn about your science area, including Online Presentations, Courses, Learning Modules, Animations, Teaching Materials, and more. These resources come from contributors in our scientific community, and are used by visitors from all over the world.</p>
			<p>Most importantly, <?php echo $config->getValue('sitename'); ?> offers simulation tools which you can access from your web browser, so you can not only learn about but also simulate your science area.</p>
		</div>
		<div class="three columns third">
			<h2>Explore other <?php echo $config->getValue('sitename'); ?> Content!</h2>
			<ul class="site-menu">
				<?php foreach($links as $link => $name) { ?>
					<li><a href="<?php echo $link; ?>"><?php echo $name; ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<br class="clear" />
	</div>
</div><!-- / #special-course-pane -->
<?php } ?> 
<?php
	//define the default course template
	$default_path = 'components' . DS . 'com_courses' . DS . 'views' . DS . 'view' . DS . 'tmpl' . DS .'default.php';

	//load in the special course template
	$temp_path 	= 'site' . DS . 'courses' . DS . $this->course->get('gidNumber') . DS . 'template' . DS . 'default.php';
	$css_path 	= 'site' . DS . 'courses' . DS . $this->course->get('gidNumber') . DS . 'template' . DS . 'default.css';
	$js_path 	= 'site' . DS . 'courses' . DS . $this->course->get('gidNumber') . DS . 'template' . DS . 'default.js';

	//start the output buffer
	ob_start();

	//if the template file exists use their custom template
	if(is_file(JPATH_ROOT . DS . $temp_path)) {

		//partner template exists
		$tmpl = true;

		//get the document
		$doc =& JFactory::getDocument();

		//if the css file exists push to the page
		if(is_file(JPATH_ROOT . DS . $css_path)) {
			$doc->addStyleSheet( DS . $css_path );
		}

		//if the js file exists push to the page
		if(is_file(JPATH_ROOT . DS . $js_path)) {
			$doc->addScript( DS . $js_path );
		}

		//include the courses template
		include JPATH_ROOT . DS . $temp_path;

	} else {
		//partner template doesnt exist
		$tmpl = false;

		//include the default template
		include JPATH_ROOT . DS . $default_path;
	}

	//get the buffer contents
	$content = ob_get_contents();

	//clean the buffer
	ob_end_clean();

	//display the content
	echo $content;
?>
	
<?php 
	//return link for login and logout
	$return = JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn'));
	$return = base64_encode($return);
?>

<?php if($tmpl && !$no_html) { ?>
	<div id="special_management">
		<?php if(!$this->user->get('guest')) { ?>
			<ul>
				<?php if($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
					<li>Manage this course: </li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&task=edit'); ?>">Edit</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&task=customize'); ?>">Customize</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&task=managepages'); ?>">Manage Course Pages</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&task=invite'); ?>">Invite Users</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&task=delete'); ?>">Delete</a></li>
				<?php } else { ?>
					<li>Welcome <?php echo $this->user->get('name'); ?>, </li>
				<?php } ?>
				<li><a href="/logout?return=<?php echo $return; ?>" class="logout">Logout</a>
			</ul>
		<?php } else { ?>
			Want to edit this course? <a href="/login?return=<?php echo $return; ?>">Login now!</a>
		<?php } ?>
	</div><!-- /#special_management -->
<?php } ?>