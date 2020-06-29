<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$pubconfig = Component::params('com_publications');

$submit_url = Route::url('index.php?option=com_publications&task=submit&action=choose');
if (User::isGuest())
{
  // Could be a problem here - imagining possible issue with this
	$submit_url = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($submit_url));
}
?>
<div class="contribute">
	<div class="submit-resource-wrap">
		<aside>
			<div class="software-instructions-wrapper">
				<div class="arrow-right"></div>
				<div class="software-instructions">
					<h4>Got a Shiny App?</h4>
					<p>Please read these <a href="https://docs.google.com/document/d/1TtRbOhlUfkD2a-Ii35py-55mlYsNhHzyexSJ9FsA70M/edit?usp=sharing" target="_blank">special instructions</a>. For other types of software, <span class="helpme"><a href="/support/">contact us</a></span> for help.</p>
			</div>

			<div class="kb-links">
				<h5>Related Articles</h5>
				<ul>
					<li>[Article] <a href="/kb/resources/postresource">Using Publications to Post Resources on QUBES</a></li>
					<li>[Article] <a href="/authorinfo">Information for Authors and Submitters</a></li>
				</ul>
			</div>
		</aside>

		<div class="submit-a-resource-wrap">
			<h3>Quick and Easy. Get started today!</h3>
			<a id="submit-resource" href="<?php echo $submit_url;?>" class="btn submit-resource-btn">Submit a Resource</a>
		</div>

		<div class="submit-partner-resource-wrap">
			<h5>Submit a Partner Resource</h5>

			<div class="resource-type-wrap">
				<a href="/qubesresources/publications/submit?action=publication&base=niblseresource" class="resource-type">
					<img src="/app/site/media/images/partners/NIBLSEGraphic.png" alt="Partner logo" class="partner-resource-logo">
				</a>

				<a href="/qubesresources/publications/submit?action=publication&base=mmhubresource" class="resource-type">
					<img src="/app/site/media/images/partners/math_modeling_hub_logotype.png" alt="Partner logo" class="partner-resource-logo">
				</a>
			</div>
		</div>
	</div>
	<div>
		<?php if (User::isGuest())
		{
			$this->view('intro')
			     ->set('project', $this->project)
			     ->set('pub', $this->pub)
			     ->display();
		}
		else
		{
			$filters = array();

			// Get user projects
			$filters['projects']  = $this->project->table()->getUserProjectIds(User::get('id'), 0, 1);

			$filters['mine']	= User::get('id');
			$filters['dev']		= 1;
			$filters['sortby']	= 'mine';
			$filters['limit'] 	= Request::getInt('limit', Config::get('list_limit'));
			$filters['start'] 	= Request::getInt('limitstart', 0);

			// Get publications created by user
			$mypubs = $this->pub->entries( 'list', $filters );
			$total  = $this->pub->entries( 'count', $filters );
			?>
			<form action="<?php echo Route::url('index.php?option=com_publications&task=submit'); ?>" method="post" id="browseForm" >
			<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_PUBLICATIONS'); ?></h3>
			<?php if (!empty($mypubs)) { ?>
			<ul class="mypubs">
				<?php

				foreach ($mypubs as $row)
				{
					$this->view('_item')
					     ->set('row', $row)
					     ->set('project', $this->project)
					     ->set('pub', $this->pub)
					     ->display();
				}
				?>
			</ul>
			<?php // Pagination
			$pageNav = new \Hubzero\Pagination\Paginator(
				$total,
				$filters['start'],
				$filters['limit']
			);
			echo $pageNav->render();
			?>
			</form>
			<?php } else { ?>
				<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND'); ?></p>
			<?php } ?>
		<?php } ?>
	</div>
</div>
