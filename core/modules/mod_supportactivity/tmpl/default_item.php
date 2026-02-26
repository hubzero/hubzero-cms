<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();
?>
                <?php
                $ticketUrl = Route::url(
                    'index.php?option=com_support&controller=tickets&task=edit&id='
                    . $result->ticket . ($result->id ? '#c' . $result->id : '')
                );
                $langKey = 'MOD_SUPPORTACTIVITY_'
                    . strtoupper($result->category);
                $timeFormatted = Date::of($result->created)
                    ->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                $dateFormatted = Date::of($result->created)
                    ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                ?>
                <li data-parent="activity-list<?php echo $this->module->id; ?>"
                    data-time="<?php echo $result->created; ?>"
                    class="<?php echo $this->escape($result->category); ?>"
                >
                    <a href="<?php echo $ticketUrl; ?>">
                        <span class="activity-event">
                            <?php echo Lang::txt($langKey, $result->ticket); ?>
                        </span>
                        <span class="activity-details">
                            <span class="activity-time"><time
                                datetime="<?php echo $result->created; ?>"
                            ><?php echo $timeFormatted; ?></time></span>
                            <span class="activity-date"><time
                                datetime="<?php echo $result->created; ?>"
                            ><?php echo $dateFormatted; ?></time></span>
                        </span>
                    </a>
                </li>