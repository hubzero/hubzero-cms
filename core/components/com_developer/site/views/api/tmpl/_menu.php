<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>
			<nav class="toc">
				<h3 class="toc-header" data-section="overview" data-index="0"><?php echo Lang::txt('Using the API'); ?></h3>
				<div class="toc-content">
					<ul>
						<li class="<?php echo ($this->active ? 'inactive' : 'active'); ?>">
							<a href="<?php echo Route::url($base . '&task=docs'); ?>"><?php echo Lang::txt('Overview'); ?></a>
							<ul>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-schema'); ?>"><?php echo Lang::txt('Schema'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-errormessages'); ?>"><?php echo Lang::txt('Error Messages'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-httpverbs'); ?>"><?php echo Lang::txt('HTTP Verbs'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-versioning'); ?>"><?php echo Lang::txt('Versioning'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-ratelimiting'); ?>"><?php echo Lang::txt('Rate Limiting'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-jsonp'); ?>"><?php echo Lang::txt('JSON-P'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#overview-expanding'); ?>"><?php echo Lang::txt('Expanding Objects'); ?></a></li>
							</ul>
						</li>
			<!-- 	</div>

				<h3 class="toc-header" data-section="oauth" data-index="1"><?php echo Lang::txt('Authentication (OAuth2)'); ?></h3>
				<div class="toc-content"> -->
						<li class="<?php echo ($this->active ? 'inactive' : 'active'); ?>">
							<a href="<?php echo Route::url($base . '&task=docs#oauth'); ?>"><?php echo Lang::txt('Authentication (OAuth2)'); ?></a>
							<ul>
								<li><a href="<?php echo Route::url($base . '&task=docs#oauth-authorizationcode'); ?>"><?php echo Lang::txt('Web Application Flow'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#oauth-usercredentials'); ?>"><?php echo Lang::txt('User Credentials Flow'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#oauth-refreshtoken'); ?>"><?php echo Lang::txt('Refresh Token Flow'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#oauth-sessiontoken'); ?>"><?php echo Lang::txt('Session Token Flow'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#oauth-toolsessiontoken'); ?>"><?php echo Lang::txt('Tool Session Token Flow'); ?></a></li>
								<li><a href="<?php echo Route::url($base . '&task=docs#oauth-authenticating'); ?>"><?php echo Lang::txt('Using the Token'); ?></a></li>
							</ul>
						</li>
					</ul>
				</div>

				<h3 class="toc-header" data-section="endpoints" data-index="2"><?php echo Lang::txt('API Endpoints'); ?></h3>
				<div class="toc-content">
					<ul>
				<?php $i = 2; foreach ($this->documentation['sections'] as $component => $endpoints) :?>
						<li class="<?php echo ($component == $this->active ? 'active' : 'inactive'); ?>">
							<a href="<?php echo Route::url($base . '&task=endpoint&active=' . $component); ?>"><?php echo ucfirst($component); ?></a>
							<?php if (count($endpoints)) : ?>
								<ul>
									<?php foreach ($endpoints as $endpoint) : ?>
										<?php
											$key = implode('-', $endpoint['_metadata']);
											if ($endpoint['_metadata']['version'] != $this->version)
											{
												continue;
											}
										?>
										<li><a href="<?php echo Route::url($base . '&task=endpoint&active=' . $component . '#' . $key); ?>"><?php echo $endpoint['name']; ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
				<?php endforeach; $i++; ?>
					</ul>
				</div>
			</nav>