<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\GoogleGtag;

require_once __DIR__ . DS . 'helper.php';

with(new Helper($params, $module))->display();
