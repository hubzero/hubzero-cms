<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Percona;

/**
 * Co-located Percona grammar alias.
 *
 * Percona currently shares MySQL grammar behavior in the legacy layout.
 */
class PerconaGrammar extends \Hubzero\Database\Drivers\Mysql\MysqlGrammar {}
