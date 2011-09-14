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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<div id="content-header">
	<h2>New Group Features</h2>
</div>

<div id="features_banner">
	<h3>Introducing the new face of groups</h3>
	<p>Groups are a great way for users to collaborate on the HUB, and we wanted to make collaborating even easier. So we took our old groups and totally revamped them. Not only did we give groups a new face, but we also added many new features that can be read about below.</p>
	<p>We will be rolling out the new group functionality over the next few weeks, so you should see them soon. We hope you will like the new groups as much we do. Any and all feedback is welcome!</p>
	
	<div id="banner_screen_shot">
		<img src="/components/com_groups/assets/img/new_group_banner.png">
	</div>
</div>

<div class="section features">

	<div class="four columns first">
		<h2>New Design</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="three columns first">
			<p><a class="screenshot" href="/components/com_groups/assets/img/screen_shot1.jpg"><img src="/components/com_groups/assets/img/screen_shot1.jpg" alt="New Group Design" width="100%" /></a></p>
		</div><!-- / .three columns first -->
		<div class="three columns second">
			<p><a class="screenshot" href="/components/com_groups/assets/img/screen_shot2.jpg"><img src="/components/com_groups/assets/img/screen_shot2.jpg" alt="New Group Design" width="100%" /></a></p>
		</div><!-- / .two columns second -->
		<div class="three columns third">
			<p><a class="screenshot" href="/components/com_groups/assets/img/screen_shot3.jpg"><img src="/components/com_groups/assets/img/screen_shot3.jpg" alt="New Group Design" width="100%" /></a></p>
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
	<div class="four columns first">
		<h2>New Features</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<h3>Group Identity</h3>
			<ul>
				<li>Groups will have the ability to upload a group logo and use it as their group identity</li>
			</ul>
			<p><img src="/components/com_groups/assets/img/group_identity.png" width="50%" /></p>
		</div><!-- / .three columns first -->
		<div class="two columns second">
			<h3>Group Content Pages</h3>
			<ul>
				<li>Ability to customize the content that appears on the group overview page.</li>
				<li>The overview page is defaulted to the public/private description of the group and a random selection of members.</li>
				<li>Group Managers can choose to leave default or customize with their own content. This page supports wiki markup.</li>
				<li>Also have the ability to create any number of extra content pages appearing as sub pages of the overview tab. These pages support wiki markup, so they can contain links, images, videos, etc.</li>
				<li>Managers can turn these pages on and off at any time and also reorder the way they appear in the menu.</li>
			</ul>
		</div><!-- / .three columns second -->
		<br class="clear" />
		<div class="two columns first bt">
			<h3>Group Modules</h3>
			<ul>
				<li>On the overview tab as well as all extra content pages, groups can have any number or modules that appear on the right.</li>
				<li>Managers can pick from a number of already built modules, including an information module which shows basic information about the group, or a twitter module, where they enter their Twitter username, and it will display their latest Tweets.</li>
				<li>They can also create custom modules, which support wiki markup just like the group pages.</li>
				<li>Modules can be turned on or off at anytime, and also reordered.</li>
			</ul>
		</div><!-- / .three columns second -->
		<div class="two columns second bt">
			<h3>Group Invites</h3>
			<ul>
				<li>Better tracking of invites sent to email addresses to improve invite acceptance rate.</li>
				<li>Added autocomplete to the logins/email field to assist in finding users.</li>
			</ul>
		</div><!-- / .three columns first -->
		<br class="clear" />
		
		<div class="two columns first bt">
			<h3>Group Updates</h3>
			<ul>
				<li>This will allow for automatic emails sent out to group members giving an update of group activity.</li>
				<li>Each member will be able to opt out for this feature in their hub email settings.</li>
				<li>Updates will also be available in more detail in the new group activity plugin.</li>
			</ul>
		</div><!-- / .three columns second -->
		<div class="two columns second bt">
			<h3>New Wiki Macros</h3>
			<ul>
				<li><strong>Youtube </strong><br> Embeds a Youtube video on any page supporting wiki markup.</li>
				<li><strong>Slideshow </strong><br> Displays a slideshow of images on any page supporting wiki markup.</li>
			</ul>
		</div><!-- / .three columns second -->
		
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
	<div class="four columns first">
		<h2>Group Privacy</h2>
		<p><em>Groups will have two separate privacy settings, one for discoverability, and one for access.</em></p>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<h3>Discoverability (Search-ability)</h3>
			<ul>
				<li>Can be set to visible or hidden.</li>
				<li>Visible means just that, it can be see by the world and is included in search results and lists of groups.</li>
				<li>Hidden means that it is NOT shown in search results or lists of groups. The only way it can be found is if a non group member knows the full URL of the group and accesses it that way.</li>
			</ul>
		</div><!-- / .three columns first -->
		<div class="two columns second">
			<h3>Access</h3>
			<ul>
				<li>Each plugin(messages,members,etc) will have an access level setting for it.</li>
				<li>The access levels are Disabled, Anyone, Registered, and Members.</li>
				<li>Disabled means that no one, no matter if you are a group member or manager can access that plugin.</li>
				<li>Anyone means that anyone, even a guest(non logged in) user can view the plugin.</li>
				<li>Registered means that any registered user of the hub can access the plugin.</li>
				<li>Group Members means that only group members can access the plugin, otherwise the user is given a message as to why they cant access the plugin.</li>
				<li>Each plugin will decide what to display to the user based on the access level and what they are in the group(guest, member, manager, etc).
					<ul>
						<li>For example: If the user is not a group member but a logged in HUB user and the access level for the members plugin is set to members only, then they would see just a list of the members. If they were a member of the group then they might see a list of all members and also links to message any of them. If the user was a group manager, then they would see everything that the member sees, plus links to demote and promote members, and possible more.</li>
					</ul>
				</li>
			</ul>
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
	<div class="four columns first">
		<h2>Group Plugin Changes</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<h3>Members</h3>
			<ul>
				<li>Allow classification of members into groups such as 'Team leaders', 'Project Managers', etc.</li>
				<li>There is no difference in group functionality but just as a way to classify members.</li>
			</ul>
		</div><!-- / .three columns first -->
		<div class="two columns second">
			<h3>Messages</h3>
			<ul>
				<li>Allow for managers to send emails to one of the group classifications described above in members or to any number or group members.</li>
				<li>The 'To' field will also have an autocomplete to assist in adding members.</li>
			</ul>
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
	
	
	<div class="four columns first">
		<h2>New Group Plugins</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<h3>Group Calendar</h3>
			<ul>
				<li>Works independently from the Hub calendar and allows for groups to have events</li>
			</ul>
		</div><!-- / .three columns first -->
		<div class="two columns second">
			<h3>Group Activity</h3>
			<ul>
				<li>Displays all group activity, similar in fashion to Facebook</li>
			</ul>
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

</div><!-- / .section -->
