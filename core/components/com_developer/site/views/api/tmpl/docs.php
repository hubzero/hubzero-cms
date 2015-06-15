<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$host = $_SERVER['HTTP_HOST'];
list($base, ) = explode('.', $host);
$url = 'https://' . $host . '/api';

// include needed css
$this->css('docs')
     ->css()
     ->js();

// add highlight lib
//Document::addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/github.min.css');
//Document::addScript('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js');

// pull list of versions from doc
$versions = $this->documentation['versions']['available'];
$versions = array_reverse($versions);

// either the request var or the first version (newest)
$activeVersion = Request::getVar('version', reset($versions));
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_DOCS'); ?></h2>
	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-cog" href="<?php echo Route::url('index.php?option=com_developer&controller=api'); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_HOME'); ?>
				</a>
			<?php if (!empty($versions)) : ?>
				<div class="btn-group dropdown">
					<a class="btn" href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs&version=' . $activeVersion); ?>"><?php echo $activeVersion; ?></a>
					<span class="btn dropdown-toggle"></span>
					<ul class="dropdown-menu">
						<?php foreach ($versions as $version) : ?>
							<li>
								<a href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs&version=' . $version); ?>">
									<?php echo $version; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			</li>
		</ul>
	</div>
</header>

<section class="section api docs">
	<div class="grid">
		<div class="col span9">
			
			<?php 
				echo $this->view('_docs_overview')
						  ->set('url', $url)
						  ->set('base', $base)
						  ->display();

				echo $this->view('_docs_oauth')
						  ->set('url', $url)
						  ->display();
			?>

			<?php foreach ($this->documentation['sections'] as $component => $endpoints) : ?>
				<h2 class="doc-section-header" id="<?php echo $component; ?>">
					<?php echo ucfirst($component); ?>
				</h2>
				<?php foreach ($endpoints as $endpoint) : ?>
					<?php
						$key = implode('-', $endpoint['_metadata']);

						if ($endpoint['_metadata']['version'] != $activeVersion)
						{
							continue;
						}

					?>
					<div class="doc-section endpoint" id="<?php echo $key; ?>">
						<h3><?php echo $endpoint['name']; ?></h3>
						<p><?php echo $endpoint['description']; ?></p>
						<pre><code class="http"><?php echo $endpoint['method']; ?> <?php echo $endpoint['uri']; ?></code></pre>
						
						<?php if (count($endpoint['parameters']) > 0) : ?>
							<h4><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETERS'); ?></h4>
							<table>
								<thead>
									<tr>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_NAME'); ?></th>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_TYPE'); ?></th>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DESC'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($endpoint['parameters'] as $param) : ?>
										<tr>
											<td><?php echo $param['name']; ?></td>
											<td><?php echo (isset($param['type'])) ? $param['type'] : ' '; ?></td>
											<td>
												<?php echo ($param['required']) ? '<span class="required">Required</span>.' : ''; ?> 
												<?php echo $param['description']; ?>
												<br /><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DEFAULT'); ?>: <code class="nohighlight"><?php echo ($param['default']) ? $param['default'] : 'null'; ?></code>
												<?php if (isset($param['allowedValues'])) : ?>
													<br /><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_ACCEPTED_VALUES'); ?>: <code class="nohighlight"><?php echo $param['allowedValues']; ?></code>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>

		<div class="col span3 omega">
			<div class="toc">
				<h3 class="toc-header label"><?php echo Lang::txt('Table of Contents'); ?></h3>
				<h3 class="toc-header" data-section="overview" data-index="0"><?php echo Lang::txt('Overview'); ?></h3>
				<div class="toc-content">
					<ul>
						<li><a href="#overview-schema"><?php echo Lang::txt('Schema'); ?></a></li>
						<li><a href="#overview-errormessages"><?php echo Lang::txt('Error Messages'); ?></a></li>
						<li><a href="#overview-httpverbs"><?php echo Lang::txt('HTTP Verbs'); ?></a></li>
						<li><a href="#overview-versioning"><?php echo Lang::txt('Versioning'); ?></a></li>
						<li><a href="#overview-ratelimiting"><?php echo Lang::txt('Rate Limiting'); ?></a></li>
						<li><a href="#overview-jsonp"><?php echo Lang::txt('JSON-P'); ?></a></li>
						<li><a href="#overview-expanding"><?php echo Lang::txt('Expanding Objects'); ?></a></li>
					</ul>
				</div>
				<h3 class="toc-header" data-section="oauth" data-index="1"><?php echo Lang::txt('Authentication (OAuth2)'); ?></h3>
				<div class="toc-content">
					<ul>
						<li><a href="#oauth-authorizationcode"><?php echo Lang::txt('Web Application Flow'); ?></a></li>
						<li><a href="#oauth-usercredentials"><?php echo Lang::txt('User Credentials Flow'); ?></a></li>
						<li><a href="#oauth-refreshtoken"><?php echo Lang::txt('Refresh Token Flow'); ?></a></li>
						<li><a href="#oauth-sessiontoken"><?php echo Lang::txt('Session Token Flow'); ?></a></li>
						<li><a href="#oauth-toolsessiontoken"><?php echo Lang::txt('Tool Session Token Flow'); ?></a></li>
						<li><a href="#oauth-authenticating"><?php echo Lang::txt('Using the Token'); ?></a></li>
					</ul>
				</div>

				<h3 class="toc-header divider"><?php echo Lang::txt('API Endpoints'); ?></h3>
				<?php $i = 2; foreach ($this->documentation['sections'] as $component => $endpoints) :?>
					<h3 class="toc-header" data-section="<?php echo $component; ?>" data-index="<?php echo $i; ?>"><?php echo ucfirst($component); ?></h3>
					<div class="toc-content">
						<ul>
							<?php foreach ($endpoints as $endpoint) : ?>
								<?php
									$key = implode('-', $endpoint['_metadata']);
									if ($endpoint['_metadata']['version'] != $activeVersion)
									{
										continue;
									}
								?>
								<li><a href="#<?php echo $key; ?>"><?php echo $endpoint['name']; ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; $i++; ?>
			</div>
		</div>
	</div>
</section>
