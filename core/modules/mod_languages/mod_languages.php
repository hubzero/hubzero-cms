<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Languages;

require_once __DIR__ . DS . 'helper.php';

with(new Helper($params, $module))->display();
