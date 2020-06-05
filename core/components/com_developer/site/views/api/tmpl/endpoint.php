<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
$available = [];
$endpoints = [];
foreach ($this->documentation['sections'][$this->active] as &$endpoint)
{
	$version = str_replace('_', '.', $endpoint['_metadata']['version']);
	$version = number_format((float)$version, 1);

	$endpoint['_metadata']['version'] = $version;

	$available[] = $version;

	if (!isset($endpoints[$version]))
	{
		$endpoints[$version] = array();
	}
}

foreach (array_keys($endpoints) as $version)
{
	foreach ($this->documentation['sections'][$this->active] as $endpoint)
	{
		if ($endpoint['_metadata']['version'] > $version)
		{
			continue;
		}

		$key = (isset($endpoint['_metadata']['controller']) ? $endpoint['_metadata']['controller'] : '') . $endpoint['_metadata']['method'];

		if (!isset($endpoints[$version][$key])
		 || $endpoint['_metadata']['version'] > $endpoints[$version][$key]['_metadata']['version'])
		{
			$endpoints[$version][$key] = $endpoint;
		}
	}
}

// pull list of versions from doc
$versions = array_unique($available);
asort($versions);
$versions = array_reverse($versions);
$done = [];

// either the request var or the first version (newest)
$activeVersion = Request::getString('version', reset($versions));
$activeVersion = str_replace('_', '.', $activeVersion);
$activeVersion = number_format((float)$activeVersion, 1);

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
			</li>
		</ul>
	</div>
</header>

<section class="section api docs">
	<div class="section-inner hz-layout-with-aside">
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
					<?php if (!empty($versions)) : ?>
						<div class="btn-group dropdown">
							<a class="btn" href="<?php echo Route::url('index.php?option=com_developer&controller=api&task=docs&version=' . $activeVersion); ?>"><?php echo $activeVersion; ?></a>
							<span class="btn dropdown-toggle"></span>
							<ul class="dropdown-menu">
								<?php foreach ($versions as $version) : ?>
									<li>
										<a href="<?php echo Route::url($base . '&task=endpoint&active=' . $this->active . '&version=' . $version); ?>">
											<?php echo $version; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</h2>
				<?php foreach ($endpoints[$activeVersion] as $endpoint): ?>
					<?php
						$key = $endpoint['_metadata']['component'] . '-' . $endpoint['_metadata']['method'];

						if ($endpoint['_metadata']['version'] > $activeVersion)
						{
							continue;
						}

						$inherited = '';
						if ($endpoint['_metadata']['version'] < $activeVersion)
						{
							$inherited = ' ' . Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_INHERITED', $endpoint['_metadata']['version']);
						}
					?>
					<div class="doc-section endpoint" id="<?php echo $key; ?>">
						<h3><?php echo $endpoint['name'] . $inherited; ?></h3>

						<?php if ($endpoint['description']) : ?>
							<p><?php echo $endpoint['description']; ?></p>
						<?php endif; ?>

						<?php if ($endpoint['method'] && $endpoint['uri']) : ?>
							<pre><code class="http"><?php echo $endpoint['method']; ?> <?php echo $endpoint['uri']; ?></code></pre>
						<?php endif; ?>

						<?php if (!empty($endpoint['replaces'])) : ?>
							<p class="info"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_REPLACES', $endpoint['replaces']); ?></p>
						<?php endif; ?>

						<?php if (!empty($endpoint['deprecated'])) : ?>
							<p class="warning"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_DEPRECATED', $endpoint['deprecated']); ?></p>
						<?php endif; ?>

						<?php
						foreach ($endpoint as $k => $v) :
							if (in_array($k, array('name', 'method', 'description', 'replaces', 'deprecated', 'uri', 'parameters', 'return', '_metadata'))):
								continue;
							endif;
							?>
							<p><strong><?php echo $k; ?>:</strong> <?php echo $v; ?></p>
							<?php
						endforeach;
						?>

						<?php if (count($endpoint['parameters']) > 0) : ?>
							<table>
								<caption><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETERS'); ?></caption>
								<thead>
									<tr>
										<th scope="col"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_NAME'); ?></th>
										<th scope="col"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_TYPE'); ?></th>
										<th scope="col"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DESC'); ?></th>
										<th scope="col"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_DEFAULT'); ?></th>
										<th scope="col"><?php echo Lang::txt('COM_DEVELOPER_API_DOC_ENDPOINT_PARAMETER_ACCEPTED_VALUES'); ?></th>
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
											</td>
											<td>
												<code class="nohighlight"><?php echo (!is_null($param['default'])) ? $param['default'] : 'null'; ?></code>
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
