<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Percona;

/**
 * Co-located Percona grammar alias.
 *
 * Percona currently shares MySQL grammar behavior in the legacy layout.
 */
class PerconaGrammar extends \Hubzero\Database\Drivers\Mysql\MysqlGrammar
{
}
