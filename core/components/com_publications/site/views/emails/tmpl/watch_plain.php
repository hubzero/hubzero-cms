<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Request;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     message: mixed,
 *     publication: mixed,
 *     subject: mixed,
 *     unsubscribeLink: mixed,
 *     url: mixed,
 * } $this
 */

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');

echo $this->subject; ?>

=======================
<?php echo $this->message; ?>

Publication: <?php echo $this->publication->get('title'); ?>

Version: <?php echo $this->publication->get('version_label'); ?>

URL: <?php echo $base . $this->url; ?>

-----------------------


This email was sent to you on behalf of <?php echo Request::root(); ?> because you are subscribed
to watch this publication. To unsubscribe, go to <?php echo $base . $this->unsubscribeLink; ?>.