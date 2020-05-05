<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Search;

require_once __DIR__ . DS . 'helper.php';

with(new Helper($params, $module))->display();
