<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SAML_TITLE'), 'SAML');

if (User::authorise('core.admin', $this->option))
{
        Toolbar::preferences($this->option, '550');
}
?>

<section>
    <div>
        <p>There are no administrative functions for this component yet.</p>
    </div>
</section>
