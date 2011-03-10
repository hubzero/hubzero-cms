<?php // $Id: access.php,v 1.7 2007/02/16 08:47:00 vyshane Exp $
/**
 * Capability definitions for the workshop module.
 *
 * For naming conventions, see lib/db/access.php.
 */
$mod_workshop_capabilities = array(

    'mod/workshop:participate' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'student' => CAP_ALLOW
        )
    ),

    'mod/workshop:manage' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    )
);
