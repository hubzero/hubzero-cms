<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$name = $this->name;

$fuzzyEnd = $this->fuzzyEnd;
$operator = $this->operator;
$value = $this->value;
?>

<input type="text" name="query[<?php echo $name; ?>][value]"
	value="<?php echo $value; ?>">

<input type="hidden" name="query[<?php echo $name; ?>][operator]"
	value="<?php echo $operator; ?>">

<input type="hidden" name="query[<?php echo $name; ?>][fuzzy_end]"
	value="<?php echo $fuzzyEnd; ?>">
