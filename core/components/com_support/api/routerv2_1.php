<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// The v2.1 API routes exactly as v2.0 does; the loader includes this file
// for /api/v2.1/support/... and the one parse() implementation lives in
// routerv2_0.php (this file used to be a byte-for-byte copy of it).
require_once __DIR__ . DIRECTORY_SEPARATOR . 'routerv2_0.php';
