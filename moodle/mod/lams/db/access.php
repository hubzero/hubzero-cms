<?php // $Id: access.php,v 1.3 2006/10/11 06:22:01 moodler Exp $
/**
 * Capability definitions for the lams module.
 *
 * For naming conventions, see lib/db/access.php.
 */
$mod_lams_capabilities = array(

    'mod/lams:participate' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'student' => CAP_ALLOW
        )
    ),

    'mod/lams:manage' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    )
);
