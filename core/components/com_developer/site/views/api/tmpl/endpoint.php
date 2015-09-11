<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$host = $_SERVER['HTTP_HOST'];
list($base, ) = explode('.', $host);
$url = 'https://' . $host . '/api';

// include needed css
$this->css('docs')
     ->css();

// add highlight lib
//Document::addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/github.min.css');
//Document::addScript('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js');

// pull list of versions from doc
$versions = $this->documentation['versions']['available'];
$versions = array_reverse($versions);

// either the request var or the first version (newest)
$activeVersion = Request::getVar('version', reset($versions));

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_DOCS') . ': ' . Lang::txt('COM_DEVELOPER_API_ENDPOINT'); ?></h2>

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
	<div class="section-inner">
		<aside class="aside">
			<?php 
			$this->view('_menu')
				 ->set('documentation', $this->documentation)
				 ->set('active', $this->active)
				 ->set('version', $activeVersion)
				 ->display();
			?>
		</aside>
		<div class="subject">
				<h2 class="doc-section-header" id="<?php echo $this->active; ?>">
					<?php echo ucfirst($this->active); ?>
				</h2>
				<?php foreach ($this->documentation['sections'][$this->active] as $endpoint) : ?>
					<?php
						$key = implode('-', $endpoint['_metadata']);

						if ($endpoint['_metadata']['version'] != $activeVersion)
						{
							continue;
						}

					?>
					<div class="doc-section endpoint" id="<?php echo $key; ?>">
						<h3><?php echo $endpoint['name']; ?></h3>

						<?php if ($endpoint['description']) : ?>
							<p><?php echo $endpoint['description']; ?></p>
						<?php endif; ?>

						<?php if ($endpoint['method'] && $endpoint['uri']) : ?>
							<pre><code class="http"><?php echo $endpoint['method']; ?> <?php echo $endpoint['uri']; ?></code></pre>
						<?php endif; ?>

						<?php if (count($endpoint['parameters']) > 0) : ?>
							<table>
								<caption><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETERS'); ?></caption>
								<thead>
									<tr>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_NAME'); ?></th>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_TYPE'); ?></th>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DESC'); ?></th>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DEFAULT'); ?></th>
										<th><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_ACCEPTED_VALUES'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($endpoint['parameters'] as $param) : ?>
										<tr>
											<td><?php echo $param['name']; ?></td>
											<td><?php echo (isset($param['type'])) ? $param['type'] : ' '; ?></td>
											<td>
												<?php echo ($param['required']) ? '<span class="required">' . Lang::txt('JREQUIRED') . '</span>.' : ''; ?> 
												<?php echo $param['description']; ?>
												<!-- <br /><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DEFAULT'); ?>: <code class="nohighlight"><?php echo ($param['default']) ? $param['default'] : 'null'; ?></code>
												<?php if (isset($param['allowedValues'])) : ?>
													<br /><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_ACCEPTED_VALUES'); ?>: <code class="nohighlight"><?php echo $param['allowedValues']; ?></code>
												<?php endif; ?> -->
											</td>
											<td>
												<code class="nohighlight"><?php echo ($param['default']) ? $param['default'] : 'null'; ?></code>
											</td>
											<td>
												<?php if (isset($param['allowedValues'])) : ?>
													<code class="nohighlight"><?php echo $param['allowedValues']; ?></code>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
		</div>
	</div>
</section>
