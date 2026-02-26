<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$base = 'index.php?option=' . $this->option
    . '&controller=' . $this->controller;
$done = array();

$docsUrl = Route::url($base . '&task=docs');
$schemaUrl = Route::url($base . '&task=docs#overview-schema');
$errorUrl = Route::url($base . '&task=docs#overview-errormessages');
$httpUrl = Route::url($base . '&task=docs#overview-httpverbs');
$versionUrl = Route::url($base . '&task=docs#overview-versioning');
$rateUrl = Route::url($base . '&task=docs#overview-ratelimiting');
$jsonpUrl = Route::url($base . '&task=docs#overview-jsonp');
$expandUrl = Route::url($base . '&task=docs#overview-expanding');

$oauthUrl = Route::url($base . '&task=docs#oauth');
$authCodeUrl = Route::url($base . '&task=docs#oauth-authorizationcode');
$userCredUrl = Route::url($base . '&task=docs#oauth-usercredentials');
$refreshUrl = Route::url($base . '&task=docs#oauth-refreshtoken');
$sessionUrl = Route::url($base . '&task=docs#oauth-sessiontoken');
$toolUrl = Route::url($base . '&task=docs#oauth-toolsessiontoken');
$authUrl = Route::url($base . '&task=docs#oauth-authenticating');

$activeCls = $this->active ? 'inactive' : 'active';
?>
            <nav class="toc">
                <h3
                    class="toc-header"
                    data-section="overview"
                    data-index="0"
                >
                    <?php echo Lang::txt('Using the API'); ?>
                </h3>
                <div class="toc-content">
                    <ul>
                        <li class="<?php echo $activeCls; ?>">
                            <a href="<?php echo $docsUrl; ?>">
                                <?php echo Lang::txt('Overview'); ?>
                            </a>
                            <ul>
                                <li>
                                    <a href="<?php echo $schemaUrl; ?>">
                                        <?php echo Lang::txt('Schema'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $errorUrl; ?>">
                                        <?php echo Lang::txt('Error Messages'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $httpUrl; ?>">
                                        <?php echo Lang::txt('HTTP Verbs'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $versionUrl; ?>">
                                        <?php echo Lang::txt('Versioning'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $rateUrl; ?>">
                                        <?php echo Lang::txt('Rate Limiting'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $jsonpUrl; ?>">
                                        <?php echo Lang::txt('JSON-P'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $expandUrl; ?>">
                                        <?php echo Lang::txt('Expanding Objects'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
            <!--    </div>

                <h3 class="toc-header" data-section="oauth" data-index="1">
                    <?php echo Lang::txt('Authentication (OAuth2)'); ?>
                </h3>
                <div class="toc-content"> -->
                        <li class="<?php echo $activeCls; ?>">
                            <a href="<?php echo $oauthUrl; ?>">
                                <?php echo Lang::txt('Authentication (OAuth2)'); ?>
                            </a>
                            <ul>
                                <li>
                                    <a href="<?php echo $authCodeUrl; ?>">
                                        <?php echo Lang::txt('Web Application Flow'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $userCredUrl; ?>">
                                        <?php echo Lang::txt('User Credentials Flow'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $refreshUrl; ?>">
                                        <?php echo Lang::txt('Refresh Token Flow'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $sessionUrl; ?>">
                                        <?php echo Lang::txt('Session Token Flow'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $toolUrl; ?>">
                                        <?php echo Lang::txt('Tool Session Token Flow'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $authUrl; ?>">
                                        <?php echo Lang::txt('Using the Token'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <h3
                    class="toc-header"
                    data-section="endpoints"
                    data-index="2"
                >
                    <?php echo Lang::txt('API Endpoints'); ?>
                </h3>
                <div class="toc-content">
                    <ul>
                <?php foreach ($this->documentation['sections'] as $component => $endpoints) :?>
                        <?php
                        $compCls = ($component == $this->active) ? 'active' : 'inactive';
                        $compUrl = Route::url($base . '&task=endpoint&active=' . $component);
                        ?>
                        <li class="<?php echo $compCls; ?>">
                            <a href="<?php echo $compUrl; ?>">
                                <?php echo ucfirst($component); ?>
                            </a>
                            <?php if (count($endpoints)) : ?>
                                <ul>
                                    <?php foreach ($endpoints as $endpoint) : ?>
                                        <?php
                                            $key = $endpoint['_metadata']['component']
                                                . '-' . $endpoint['_metadata']['method'];
                                        if (in_array($key, $done)) {
                                            continue;
                                        }
                                            $done[] = $key;
                                            $epUrl = Route::url(
                                                $base . '&task=endpoint&active='
                                                . $component . '#' . $key
                                            );
                                        ?>
                                        <li>
                                            <a href="<?php echo $epUrl; ?>">
                                                <?php echo $endpoint['name']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                <?php endforeach; ?>
                    </ul>
                </div>
            </nav>
