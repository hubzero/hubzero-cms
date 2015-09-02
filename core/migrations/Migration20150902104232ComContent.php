<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating Discover page contents
 **/
class Migration20150902104232ComContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__content'))
		{
			$query = "SELECT * FROM `#__content` WHERE `alias` = 'discover' LIMIT 1";
			$this->db->setQuery($query);
			$row = $this->db->loadObject();

			if ($row && $row->id)
			{
				$old = $this->previous;

				$olddespaced = str_replace(array('\r', '\n', '\t', "\n", "\r", "\t"), '', $old);
				$olddespaced = trim($olddespaced);

				$despaced = str_replace(array('\r', '\n', '\t', "\n", "\r", "\t"), '', $row->introtext);
				$despaced = trim($despaced);

				if ($row->introtext == $old || $despaced == $olddespaced)
				{
					$query = "UPDATE `#__content` SET `introtext` = " . $this->db->quote($this->updated) . " WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__content'))
		{
			$query = "SELECT * FROM `#__content` WHERE `alias` = 'discover' LIMIT 1";
			$this->db->setQuery($query);
			$row = $this->db->loadObject();

			if ($row && $row->id)
			{
				$old = $this->updated;

				$olddespaced = str_replace(array('\r', '\n', '\t', "\n", "\r", "\t"), '', $old);
				$olddespaced = trim($olddespaced);

				$despaced = str_replace(array('\r', '\n', '\t', "\n", "\r", "\t"), '', $row->introtext);
				$despaced = trim($despaced);

				if ($row->introtext == $old || $despaced == $olddespaced)
				{
					$query = "UPDATE `#__content` SET `introtext` = " . $this->db->quote($this->previous) . " WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Previous Discover page content
	 *
	 * @var  string
	 */
	public $previous = '{xhub:include type="stylesheet" filename="pages/discover.css"}

<div class="grid">
	<div class="col span-quarter">
		<h2>Do More</h2>
	</div>

	<div class="col span-quarter">
		<div class="presentation">
			<h3><a href="/resources">Resources</a></h3>
			<p>Find the latest cutting-edge research in our <a href="/resources">resources</a>.</p>
		</div>
	</div>
	<div class="col span-quarter">
		<div class="quote">
			<h3><a href="/citations">Citations</a></h3>
			<p>See who has <a href="/citations">cited</a> our content in their work.</p>
		</div>
	</div>
	<div class="col span-quarter omega">
		<div class="tag">
			<h3><a href="/tags">Tags</a></h3>
			<p>Explore all our content through <a href="/tags">tags</a> or even tag content yourself.</p>
		</div>
	</div>
</div>

<div class="grid below">
	<div class="col span-quarter offset-quarter">
		<div class="blog">
			<h3><a href="/blog">Blog</a></h3>
			<p>Read the <a href="/blog">latest entry</a> or browse the archive for articles of interest.</p>
		</div>
	</div>
	<div class="col span-quarter">
		<div class="wiki">
			<h3><a href="/wiki">Wiki</a></h3>
			<p>Browse our user-generated <a href="/wiki">wiki pages</a> or write your own.</p>
		</div>
	</div>
	<div class="col span-quarter omega">
		<div class="feedback">
			<h3><a href="/feedback">Feedback</a></h3>
			<p>Like something? Having trouble? <a href="/feedback">Let us know what you think!</a></p>
		</div>
	</div>
</div>

<div class="grid">
	<div class="col span-quarter">
		<h2>Services</h2>
	</div>
	<div class="col span-quarter">
		<div class="contribute">
			<h3><a href="/resources/new">Upload</a></h3>
			<p><a href="/resources/new">Publish</a> your own tools, seminars, and other content on this site.</p>
		</div>
	</div>
	<div class="col span-quarter">
		<div class="tool">
			<h3><a href="/tools">Tool Forge</a></h3>
			<p>The <a href="/tools">development area</a> for simulation tools. Sign up and manage your own software project!</p>
		</div>
	</div>
	<div class="col span-quarter omega">
		<div class="cart">
			<h3><a href="/store">Store</a></h3>
			<p><a href="/store">Purchase items</a> such as t-shirts using points you earn by helping out.</p>
		</div>
	</div>
</div>

<div class="grid">
	<div class="col span-quarter">
		<h2>What\'s Happening</h2>
	</div>
	<div class="col span-quarter">
		<div class="event">
			<h3><a href="/events">Events</a></h3>
			<p>Find information about the many upcoming <a href="/events">public meetings and scientific symposia</a>.</p>
		</div>
	</div>
	<div class="col span-quarter">
		<div class="new">
			<h3><a href="/whatsnew">What\'s New</a></h3>
			<p>Find the latest content posted on the site with our <a href="/whatsnew">What\'s New</a> section.</p>
		</div>
	</div>
	<div class="col span-quarter omega">
		<div class="poll">
			<h3><a href="/poll">Poll</a></h3>
			<p>Respond to our poll questions and <a href="/poll">see what everyone else is thinking</a>.</p>
		</div>
	</div>
</div>';

	/**
	 * PUpdated Discover page content
	 *
	 * @var  string
	 */
	public $updated = '{xhub:include type="stylesheet" filename="pages/discover.css"}

<section class="odd">
	<h2>Do More</h2>

	<div class="grid">
		<div class="col span4">
			<div class="block block-resources">
				<h3><a href="/resources">Resources</a></h3>
				<p>Find the latest cutting-edge research in our <a href="/resources">resources</a>.</p>
			</div>
		</div>

		<div class="col span4">
			<div class="block block-citations">
				<h3><a href="/citations">Citations</a></h3>
				<p>See who has <a href="/citations">cited</a> our content in their work.</p>
			</div>
		</div>

		<div class="col span4 omega">
			<div class="block block-tags">
				<h3><a href="/tags">Tags</a></h3>
				<p>Explore all our content through <a href="/tags">tags</a> or even tag content yourself.</p>
			</div>
		</div>
	</div>

	<div class="grid">
		<div class="col span4">
			<div class="block block-blog">
				<h3><a href="/blog">Blog</a></h3>
				<p>Read the <a href="/blog">latest entry</a> or browse the archive for articles of interest.</p>
			</div>
		</div>

		<div class="col span4">
			<div class="block block-wiki">
				<h3><a href="/wiki">Wiki</a></h3>
				<p>Browse our user-generated <a href="/wiki">wiki pages</a> or write your own.</p>
			</div>
		</div>

		<div class="col span4 omega">
			<div class="block block-feedback">
				<h3><a href="/feedback">Feedback</a></h3>
				<p>Like something? Having trouble? <a href="/feedback">Let us know what you think!</a></p>
			</div>
		</div>
	</div>
</section>

<section class="even">
	<h2>Services</h2>

	<div class="grid">
		<div class="col span4">
			<div class="block block-contribute">
				<h3><a href="/resources/new">Upload</a></h3>
				<p><a href="/resources/new">Publish</a> your own tools, seminars, and other content on this site.</p>
			</div>
		</div>

		<div class="col span4">
			<div class="block block-tools">
				<h3><a href="/tools">Tool Forge</a></h3>
				<p>The <a href="/tools">development area</a> for simulation tools. Sign up and manage your own software project!</p>
			</div>
		</div>

		<div class="col span4 omega">
			<div class="block block-store">
				<h3><a href="/store">Store</a></h3>
				<p><a href="/store">Purchase items</a> such as t-shirts using points you earn by helping out.</p>
			</div>
		</div>
	</div>
</section>

<section class="odd">
	<h2>What&#39;s Happening</h2>

	<div class="grid">
		<div class="col span4">
			<div class="block block-events">
				<h3><a href="/events">Events</a></h3>
				<p>Find information about the many upcoming <a href="/events">public meetings and scientific symposia</a>.</p>
			</div>
		</div>

		<div class="col span4">
			<div class="block block-whatsnew">
				<h3><a href="/whatsnew">What&#39;s New</a></h3>
				<p>Find the latest content posted on the site with our <a href="/whatsnew">What&#39;s New</a> section.</p>
			</div>
		</div>

		<div class="col span4 omega">
			<div class="block block-polls">
				<h3><a href="/poll">Poll</a></h3>
				<p>Respond to our poll questions and <a href="/poll">see what everyone else is thinking</a>.</p>
			</div>
		</div>
	</div>
</section>';
}