<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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