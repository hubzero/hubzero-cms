<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command\Repository;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Content\Migration\Base as Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Repository flavor class
 **/
class Flavor extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Set the flavor
	 *
	 * @return void
	 **/
	public function set()
	{
		if (!$flavor = $this->arguments->getOpt(3))
		{
			$this->output->error('Please provide the flavor you would like to use');
		}

		$database  = \JFactory::getDbo();
		$migration = new Migration($database);

		switch ($flavor)
		{
			case 'amazon':
				// Disable com_tools
				$migration->disableComponent('com_tools');
				$this->output->addLine('Disabling com_tools');

				// Disable tool-related modules
				$migration->disableModule('mod_mytools');
				$this->output->addLine('Disabling mod_mytools');
				$migration->disableModule('mod_mycontributions');
				$this->output->addLine('Disabling mod_contributions');
				$migration->disableModule('mod_mysessions');
				$this->output->addLine('Disabling mod_mysessions');

				$defaults = array(
					'{"module":44,"col":1,"row":1,"size_x":1,"size_y":2}',
					'{"module":35,"col":1,"row":3,"size_x":1,"size_y":2}',
					'{"module":38,"col":1,"row":5,"size_x":1,"size_y":2}',
					'{"module":39,"col":1,"row":7,"size_x":1,"size_y":2}',
					'{"module":33,"col":2,"row":1,"size_x":1,"size_y":2}',
					'{"module":42,"col":2,"row":3,"size_x":1,"size_y":2}',
					'{"module":34,"col":2,"row":5,"size_x":1,"size_y":2}',
					'{"module":37,"col":3,"row":1,"size_x":1,"size_y":2}'
				);

				$params = array(
					"allow_customization" => "1",
					"position"            => "memberDashboard",
					"defaults"            => '[' . implode(',', $defaults) . ']'
				);

				$migration->savePluginParams('members', 'dashboard', $params);
				$this->output->addLine('Updating default members dashboard configuration');

				// Update kb articles
				$query = "UPDATE `#__faq_categories` SET `state` = 2 WHERE `alias` = 'tools'";
 				$database->setQuery($query);
				$database->query();
				$query = "UPDATE `#__faq` SET `state` = 2 WHERE `alias` = 'webdav'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Deleting tool and webdav related KB articles');

				// Set amazon param in welcome template
				$params = array('flavor' => 'amazon', 'template' => 'hubbasic2013');
				$query  = "UPDATE `#__template_styles` SET `params` = " . $database->quote(json_encode($params)) . " WHERE `template` = 'welcome'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Setting amazon flavor flag in welcome template');

				// Delete tools resource type
				$query = "DELETE FROM `#__resource_types` WHERE `alias` = 'tools'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Deleting tools resource type');

				// Update default content page(s)
				$this->output->addLine('Updating default content pages');
				$this->output->addLine('Updating content page id (22)');
				$query  = "UPDATE `#__content` SET `introtext` = '{xhub:include type=\"stylesheet\" filename=\"pages/discover.css\"}\r\n<div class=\"grid\">\r\n    <div class=\"col span-quarter\">\r\n        <h2>Do More</h2>\r\n    </div>\r\n\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"presentation\">\r\n            <h3><a href=\"/resources\">Resources</a></h3>\r\n            <p>Find the latest cutting-edge research in our <a href=\"/resources\">resources</a>.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"quote\">\r\n            <h3><a href=\"/citations\">Citations</a></h3>\r\n            <p>See who has <a href=\"/citations\">cited</a> our content in their work.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"tag\">\r\n            <h3><a href=\"/tags\">Tags</a></h3>\r\n            <p>Explore all our content through <a href=\"/tags\">tags</a> or even tag content yourself.</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"grid below\">\r\n    <div class=\"col span-quarter offset-quarter\">\r\n        <div class=\"blog\">\r\n            <h3><a href=\"/blog\">Blog</a></h3>\r\n            <p>Read the <a href=\"/blog\">latest entry</a> or browse the archive for articles of interest.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"wiki\">\r\n            <h3><a href=\"/wiki\">Wiki</a></h3>\r\n            <p>Browse our user-generated <a href=\"/wiki\">wiki pages</a> or write your own.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"feedback\">\r\n            <h3><a href=\"/feedback\">Feedback</a></h3>\r\n            <p>Like something? Having trouble? <a href=\"/feedback\">Let us know what you think!</a></p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"grid\">\r\n    <div class=\"col span-quarter\">\r\n        <h2>Services</h2>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"contribute\">\r\n            <h3><a href=\"/resources/new\">Upload</a></h3>\r\n            <p><a href=\"/resources/new\">Publish</a> your own tools, seminars, and other content on this site.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"cart\">\r\n            <h3><a href=\"/store\">Store</a></h3>\r\n            <p><a href=\"/store\">Purchase items</a> such as t-shirts using points you earn by helping out.</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"grid\">\r\n    <div class=\"col span-quarter\">\r\n        <h2>What\'s Happening</h2>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"event\">\r\n            <h3><a href=\"/events\">Events</a></h3>\r\n            <p>Find information about the many upcoming <a href=\"/events\">public meetings and scientific symposia</a>.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"new\">\r\n            <h3><a href=\"/whatsnew\">What\'s New</a></h3>\r\n            <p>Find the latest content posted on the site with our <a href=\"/whatsnew\">What\'s New</a> section.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"poll\">\r\n            <h3><a href=\"/poll\">Poll</a></h3>\r\n            <p>Respond to our poll questions and <a href=\"/poll\">see what everyone else is thinking</a>.</p>\r\n        </div>\r\n    </div>\r\n</div>'";
				$query .= " WHERE `id` = '22' AND `alias` = 'discover'";
				$database->setQuery($query);
				$database->query();

				$this->output->addLine('Updating content page id (32)');
				$query  = "UPDATE `#__content` SET `introtext` = '{xhub:include type=\"stylesheet\" filename=\"pages/gettingstarted.css\"}\r\n\r\n<div class=\"explore-section\">\r\n	<div class=\"wrap\">\r\n		<h2>Getting To Know Your Hub</h2>\r\n\r\n		<div class=\"grid\">\r\n			<div class=\"col span-third\">\r\n				<div class=\"docblock use\">\r\n					<h3>Utilize</h3>\r\n\r\n					<p>View tutorials and read about how to use the numerous features of a HUB. We show you how to add content, customize your dashboard, create groups, and more.</p>\r\n\r\n					<p class=\"btn-container\"><a class=\"btn\" href=\"http://hubzero.org/documentation/current/users\" rel=\"external\">User documentation</a></p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span-third\">\r\n				<div class=\"docblock manage\">\r\n					<h3>Manage</h3>\r\n\r\n					<p>Read the manual for managing the content and functionality of a HUB. It progresses step-by-step through various common tasks and familiarizes you with the administrative interface.</p>\r\n\r\n					<p class=\"btn-container\"><a class=\"btn\" href=\"http://hubzero.org/documentation/current/managers\" rel=\"external\">Manager documentation</a></p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span-third omega\">\r\n				<div class=\"docblock customize\">\r\n					<h3>Extend</h3>\r\n\r\n					<p>Build your own extensions and discover how to extend or tailor the existing ones to your needs. We try to guide you through the creation steps and provide examples for download.</p>\r\n\r\n					<p class=\"btn-container\"><a class=\"btn\" href=\"http://hubzero.org/documentation/current/webdevs\" rel=\"external\">Developer documentation</a></p>\r\n				</div>\r\n			</div>\r\n		</div>\r\n	</div>\r\n</div>\r\n\r\n<div class=\"todo-section\">\r\n	<div class=\"wrap\">\r\n\r\n		<h2>Setting Up Your Hub</h2>\r\n		<p class=\"begin\">We\'ve provided a default set of content with a menu of commonly used extensions and pages but there is still some setup and spots needing filled in that we, unfortunately, couldn\'t do for you.</p>\r\n\r\n		<div class=\"grid\">\r\n			<div class=\"col span6\">\r\n				<h2>To-Do List</h2>\r\n				<ul>\r\n					<li class=\"about\">\r\n						<h3>About You</h3>\r\n						<p>Here\'s a page for telling your story and letting your visitors know all about you. You just have to fill in the details!</p>\r\n					</li>\r\n					<li class=\"contact\">\r\n						<h3>How to Contact</h3>\r\n						<p>Sometimes your visitors will need to get ahold of you. We\'ve provided a page to detail contact information to help make that happen.\"</p>\r\n					</li>\r\n					<li class=\"terms\">\r\n						<h3>Terms of Use</h3>\r\n						<p>Although we provided a generic Terms of Use, it will need some information filled in and further refinement for your hub.</p>\r\n					</li>\r\n				</ul>\r\n			</div>\r\n			<div class=\"col span6 omega\">\r\n				<h2>Recommended</h2>\r\n				<ul>\r\n					<li class=\"logins\">\r\n						<h3>Facebook, Google, LinkedIn</h3>\r\n						<p>Every hub comes with the ability for users to authenticate with popular services like Facebook, Google, or LinkedIn. Turn one or all of these on with a click of a button!</p>\r\n					</li>\r\n					<li class=\"analytics\">\r\n						<h3>Set up Analytics</h3>\r\n						<p>We highly recommend setting up Google Analytics on your hub. Luckily, we provide a module for doing just that.</p>\r\n					</li>\r\n					<li class=\"captcha\">\r\n						<h3>Use ReCAPTCHA</h3>\r\n						<p>While we provide basic image and text CAPTCHAs to help guard against spam bots, we recommend ReCAPTCHA for stronger protection and larger feature set.</p>\r\n					</li>\r\n\r\n					</li><li class=\"contact\">\r\n						<h3> Email </h3><p>Hubs use outgoing email for account setup and a couple other things. Outgoing email from an Amazon hosted server often gets marked as SPAM by many email providers. It is often necessary to setup your hub to use external email services (such as Mandrill)  to ensure that email makes it to users\' inboxes. </p> \r\n					</li>\r\n\r\n\r\n				</ul>\r\n			</div>\r\n		</div>\r\n\r\n	</div>\r\n</div>\r\n\r\n<div class=\"highlight-section\">\r\n	<p>Ready to go? Jump to the <a href=\"/administrator\" rel=\"external\">administration</a> or learn how to <a href=\"http://hubzero.org/documentation/current/managers/articles.homepage\" rel=\"external\">change this page</a>.</p>\r\n</div>\r\n<!-- / .highlight-section -->\r\n\r\n<div class=\"support-section\">\r\n	<div class=\"wrap\">\r\n\r\n		<h2>Where to Find Help</h2>\r\n\r\n		<p class=\"begin\">We try to make using and configuring a hub a simple, smooth process. Sometimes, however, you may have questions or issues not easily answered by the available documentation. If you need help or support while developing your hub, feel free to contact us.</p>\r\n\r\n		<div class=\"grid\">\r\n			<div class=\"col span4\">\r\n				<div class=\"docblock question\">\r\n					<h3>I have a question!</h3>\r\n\r\n					<p>Have a question on how to do something? If the <a href=\"http://hubzero.org/documentation\" rel=\"external\">documentation</a> doesn\'t seem to be of help, you can try <a href=\"http://hubzero.org/answers\" rel=\"external\">asking the community</a>.</p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span4\">\r\n				<div class=\"docblock idea\">\r\n					<h3>I have an idea!</h3>\r\n\r\n					<p>Think something can be done better or is missing? Post your <a href=\"http://hubzero.org/wishlist\" rel=\"external\">ideas or feature requests</a>. We&#39;d love to hear from you.</p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span4 omega\">\r\n				<div class=\"docblock err\">\r\n					<h3>I have an error!</h3>\r\n\r\n					<p>We continually test and refine the code for an error-free experience but, sadly, we can make mistakes. If you found a bug, <a href=\"http://hubzero.org/support/ticket/new\" rel=\"external\">let us know</a>.</p>\r\n				</div>\r\n			</div>\r\n		</div>\r\n\r\n	</div>\r\n</div>'";
				$query .= " WHERE `id` = '32' AND `alias` = 'gettingstarted'";
				$database->setQuery($query);
				$database->query();

				break;

			case 'default':
			case 'vanilla':
			case 'grape':
				// Enable com_tools
				$migration->enableComponent('com_tools');
				$this->output->addLine('Enabling com_tools');

				// Enable tool-related modules
				$migration->enableModule('mod_mytools');
				$this->output->addLine('Enabling mod_mytools');
				$migration->enableModule('mod_mycontributions');
				$this->output->addLine('Enabling mod_mycontributions');
				$migration->enableModule('mod_mysessions');
				$this->output->addLine('Enabling mod_mysessions');

				$defaults = array(
					'{"module":44,"col":1,"row":1,"size_x":1,"size_y":2}',
					'{"module":35,"col":1,"row":3,"size_x":1,"size_y":2}',
					'{"module":38,"col":1,"row":5,"size_x":1,"size_y":2}',
					'{"module":39,"col":1,"row":7,"size_x":1,"size_y":2}',
					'{"module":33,"col":2,"row":1,"size_x":1,"size_y":2}',
					'{"module":42,"col":2,"row":3,"size_x":1,"size_y":2}',
					'{"module":34,"col":2,"row":5,"size_x":1,"size_y":2}',
					'{"module":41,"col":3,"row":1,"size_x":1,"size_y":2}',
					'{"module":36,"col":3,"row":3,"size_x":1,"size_y":2}',
					'{"module":37,"col":3,"row":5,"size_x":1,"size_y":2}'
				);

				$params = array(
					"allow_customization" => "1",
					"position"            => "memberDashboard",
					"defaults"            => '[' . implode(',', $defaults) . ']'
				);

				$migration->savePluginParams('members', 'dashboard', $params);
				$this->output->addLine('Restoring default members dashboard configuration');

				// Update kb articles
				$query = "UPDATE `#__faq_categories` SET `state` = 1 WHERE `alias` = 'tools'";
				$database->setQuery($query);
				$database->query();
				$query = "UPDATE `#__faq` SET `state` = 1 WHERE `alias` = 'webdav'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Restoring tool and webdav related KB articles');

				// Set flavor param in welcome template
				$params = array('flavor' => '', 'template' => 'hubbasic2013');
				$query  = "UPDATE `#__template_styles` SET `params` = " . $database->quote(json_encode($params)) . " WHERE `template` = 'welcome'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Unsetting flavor flag in welcome template');

				// Add back tools resource type
				$query = "SELECT * FROM `#__resource_types` WHERE `alias` = 'tools'";
				$database->setQuery($query);
				if (!$database->loadObjectList())
				{
					$query  = "INSERT INTO `#__resource_types` VALUES ('7', 'tools', 'Tools', '27',";
					$query .= "'Simulation and modeling tools that can be accessed via a web browser.', '1',";
					$query .= "'poweredby=Powered by=textarea=0\ncredits=Credits=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0',";
					$query .= "'plg_citations=1\nplg_questions=1\nplg_recommendations=1\nplg_related=1\nplg_reviews=1\nplg_usage=1\nplg_versions=1\nplg_favorite=1\nplg_share=1\nplg_wishlist=1\nplg_supportingdocs=1\nplg_about=0\nplg_abouttool=1')";
					$database->setQuery($query);
					$database->query();
					$this->output->addLine('Adding tools resource type');
				}

				// Update default content page(s)
				$this->output->addLine('Updating default content pages');
				$this->output->addLine('Updating content page id (22)');
				$query  = "UPDATE `#__content` SET `introtext` = '{xhub:include type=\"stylesheet\" filename=\"pages/discover.css\"}\r\n<div class=\"grid\">\r\n    <div class=\"col span-quarter\">\r\n        <h2>Do More</h2>\r\n    </div>\r\n\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"presentation\">\r\n            <h3><a href=\"/resources\">Resources</a></h3>\r\n            <p>Find the latest cutting-edge research in our <a href=\"/resources\">resources</a>.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"quote\">\r\n            <h3><a href=\"/citations\">Citations</a></h3>\r\n            <p>See who has <a href=\"/citations\">cited</a> our content in their work.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"tag\">\r\n            <h3><a href=\"/tags\">Tags</a></h3>\r\n            <p>Explore all our content through <a href=\"/tags\">tags</a> or even tag content yourself.</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"grid below\">\r\n    <div class=\"col span-quarter offset-quarter\">\r\n        <div class=\"blog\">\r\n            <h3><a href=\"/blog\">Blog</a></h3>\r\n            <p>Read the <a href=\"/blog\">latest entry</a> or browse the archive for articles of interest.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"wiki\">\r\n            <h3><a href=\"/wiki\">Wiki</a></h3>\r\n            <p>Browse our user-generated <a href=\"/wiki\">wiki pages</a> or write your own.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"feedback\">\r\n            <h3><a href=\"/feedback\">Feedback</a></h3>\r\n            <p>Like something? Having trouble? <a href=\"/feedback\">Let us know what you think!</a></p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"grid\">\r\n    <div class=\"col span-quarter\">\r\n        <h2>Services</h2>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"contribute\">\r\n            <h3><a href=\"/resources/new\">Upload</a></h3>\r\n            <p><a href=\"/resources/new\">Publish</a> your own tools, seminars, and other content on this site.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"tool\">\r\n            <h3><a href=\"/tools\">Tool Forge</a></h3>\r\n            <p>The <a href=\"/tools\">development area</a> for simulation tools. Sign up and manage your own software project!</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"cart\">\r\n            <h3><a href=\"/store\">Store</a></h3>\r\n            <p><a href=\"/store\">Purchase items</a> such as t-shirts using points you earn by helping out.</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"grid\">\r\n    <div class=\"col span-quarter\">\r\n        <h2>What\'s Happening</h2>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"event\">\r\n            <h3><a href=\"/events\">Events</a></h3>\r\n            <p>Find information about the many upcoming <a href=\"/events\">public meetings and scientific symposia</a>.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter\">\r\n        <div class=\"new\">\r\n            <h3><a href=\"/whatsnew\">What\'s New</a></h3>\r\n            <p>Find the latest content posted on the site with our <a href=\"/whatsnew\">What\'s New</a> section.</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"col span-quarter omega\">\r\n        <div class=\"poll\">\r\n            <h3><a href=\"/poll\">Poll</a></h3>\r\n            <p>Respond to our poll questions and <a href=\"/poll\">see what everyone else is thinking</a>.</p>\r\n        </div>\r\n    </div>\r\n</div>'";
				$query .= " WHERE `id` = '22' AND `alias` = 'discover'";
				$database->setQuery($query);
				$database->query();

				$this->output->addLine('Updating content page id (32)');
				$query  = "UPDATE `#__content` SET `introtext` = '{xhub:include type=\"stylesheet\" filename=\"pages/gettingstarted.css\"}\r\n\r\n<div class=\"explore-section\">\r\n	<div class=\"wrap\">\r\n		<h2>Getting To Know Your Hub</h2>\r\n\r\n		<div class=\"grid\">\r\n			<div class=\"col span-third\">\r\n				<div class=\"docblock use\">\r\n					<h3>Utilize</h3>\r\n\r\n					<p>View tutorials and read about how to use the numerous features of a HUB. We show you how to add content, customize your dashboard, create groups, and more.</p>\r\n\r\n					<p class=\"btn-container\"><a class=\"btn\" href=\"http://hubzero.org/documentation/current/users\" rel=\"external\">User documentation</a></p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span-third\">\r\n				<div class=\"docblock manage\">\r\n					<h3>Manage</h3>\r\n\r\n					<p>Read the manual for managing the content and functionality of a HUB. It progresses step-by-step through various common tasks and familiarizes you with the administrative interface.</p>\r\n\r\n					<p class=\"btn-container\"><a class=\"btn\" href=\"http://hubzero.org/documentation/current/managers\" rel=\"external\">Manager documentation</a></p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span-third omega\">\r\n				<div class=\"docblock customize\">\r\n					<h3>Extend</h3>\r\n\r\n					<p>Build your own extensions and discover how to extend or tailor the existing ones to your needs. We try to guide you through the creation steps and provide examples for download.</p>\r\n\r\n					<p class=\"btn-container\"><a class=\"btn\" href=\"http://hubzero.org/documentation/current/webdevs\" rel=\"external\">Developer documentation</a></p>\r\n				</div>\r\n			</div>\r\n		</div>\r\n	</div>\r\n</div>\r\n\r\n<div class=\"todo-section\">\r\n	<div class=\"wrap\">\r\n\r\n		<h2>Setting Up Your Hub</h2>\r\n		<p class=\"begin\">We\'ve provided a default set of content with a menu of commonly used extensions and pages but there is still some setup and spots needing filled in that we, unfortunately, couldn\'t do for you.</p>\r\n\r\n		<div class=\"grid\">\r\n			<div class=\"col span6\">\r\n				<h2>To-Do List</h2>\r\n				<ul>\r\n					<li class=\"about\">\r\n						<h3>About You</h3>\r\n						<p>Here\'s a page for telling your story and letting your visitors know all about you. You just have to fill in the details!</p>\r\n					</li>\r\n					<li class=\"contact\">\r\n						<h3>How to Contact</h3>\r\n						<p>Sometimes your visitors will need to get ahold of you. We\'ve provided a page to detail contact information to help make that happen.\"</p>\r\n					</li>\r\n					<li class=\"terms\">\r\n						<h3>Terms of Use</h3>\r\n						<p>Although we provided a generic Terms of Use, it will need some information filled in and further refinement for your hub.</p>\r\n					</li>\r\n				</ul>\r\n			</div>\r\n			<div class=\"col span6 omega\">\r\n				<h2>Recommended</h2>\r\n				<ul>\r\n					<li class=\"logins\">\r\n						<h3>Facebook, Google, LinkedIn</h3>\r\n						<p>Every hub comes with the ability for users to authenticate with popular services like Facebook, Google, or LinkedIn. Turn one or all of these on with a click of a button!</p>\r\n					</li>\r\n					<li class=\"analytics\">\r\n						<h3>Set up Analytics</h3>\r\n						<p>We highly recommend setting up Google Analytics on your hub. Luckily, we provide a module for doing just that.</p>\r\n					</li>\r\n					<li class=\"captcha\">\r\n						<h3>Use ReCAPTCHA</h3>\r\n						<p>While we provide basic image and text CAPTCHAs to help guard against spam bots, we recommend ReCAPTCHA for stronger protection and larger feature set.</p>\r\n					</li>\r\n				</ul>\r\n			</div>\r\n		</div>\r\n\r\n	</div>\r\n</div>\r\n\r\n<div class=\"highlight-section\">\r\n	<p>Ready to go? Jump to the <a href=\"/administrator\" rel=\"external\">administration</a> or learn how to <a href=\"http://hubzero.org/documentation/current/managers/articles.homepage\" rel=\"external\">change this page</a>.</p>\r\n</div>\r\n<!-- / .highlight-section -->\r\n\r\n<div class=\"support-section\">\r\n	<div class=\"wrap\">\r\n\r\n		<h2>Where to Find Help</h2>\r\n\r\n		<p class=\"begin\">We try to make using and configuring a hub a simple, smooth process. Sometimes, however, you may have questions or issues not easily answered by the available documentation. If you need help or support while developing your hub, feel free to contact us.</p>\r\n\r\n		<div class=\"grid\">\r\n			<div class=\"col span4\">\r\n				<div class=\"docblock question\">\r\n					<h3>I have a question!</h3>\r\n\r\n					<p>Have a question on how to do something? If the <a href=\"http://hubzero.org/documentation\" rel=\"external\">documentation</a> doesn\'t seem to be of help, you can try <a href=\"http://hubzero.org/answers\" rel=\"external\">asking the community</a>.</p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span4\">\r\n				<div class=\"docblock idea\">\r\n					<h3>I have an idea!</h3>\r\n\r\n					<p>Think something can be done better or is missing? Post your <a href=\"http://hubzero.org/wishlist\" rel=\"external\">ideas or feature requests</a>. We&#39;d love to hear from you.</p>\r\n				</div>\r\n			</div>\r\n			<div class=\"col span4 omega\">\r\n				<div class=\"docblock err\">\r\n					<h3>I have an error!</h3>\r\n\r\n					<p>We continually test and refine the code for an error-free experience but, sadly, we can make mistakes. If you found a bug, <a href=\"http://hubzero.org/support/ticket/new\" rel=\"external\">let us know</a>.</p>\r\n				</div>\r\n			</div>\r\n		</div>\r\n\r\n	</div>\r\n</div>'";
				$query .= " WHERE `id` = '32' AND `alias` = 'gettingstarted'";
				$database->setQuery($query);
				$database->query();

				break;

			default:
				$this->output->error('Flavor provided is unknown.');
				break;
		}

		$this->output->addLine("Successfully updated to the {$flavor} flavor!", 'success');
	}



	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview(
		         'Repository management functions used to set the "flavor" of the hub.
		         Use this command to setup/convert your hub to one of the predefined
		         flavors. This often includes configuration changes and enabling/disabling
		         certain components based on the needs and limitations of the given
		         environement.'
		     )
		     ->noArgsSection()
		     ->addSection('Usage')
		     ->addArgument(
		         'muse repository:flavor set [flavor_name]'
		     )
		     ->addSpacer()
		     ->addSection('Flavors')
		     ->addArgument(
		         'amazon',
		         'This flavor customizes the hub uniquely for use in the Amazon EC2
		         environement. This primarily includes disabling tools and tool related
		         functions and content.'
		     )
		     ->addArgument(
		         'default',
		         'This is the default hub install.'
		     )
		     ->render();
	}
}
