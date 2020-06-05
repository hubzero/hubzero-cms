<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Collect;

require_once __DIR__ . '/helper.php';

with(new Helper($params, $module))->display();
