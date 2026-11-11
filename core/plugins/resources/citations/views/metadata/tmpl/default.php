<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{citations: mixed, url: mixed} $this
 */
?>

<p class="citation">
    <a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_RESOURCES_CITATIONS_COUNT', count($this->citations));
    ?></a>
</p>