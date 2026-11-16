<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Allow saving of hipaa compliance checkbox
 */

namespace Plugins\Resources\Hipaacompliant;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

class Hipaacompliant extends Plugin
{
    /**
     * Event call before a resource is submitted
     *
     * @param   \Components\Resources\Models\Entry  $resource
     * @return  string|null
     */
    public function onResourceBeforeSubmit(&$resource)
    {
        // Check authorization
        if (!$resource->id) {
            return null;
        }

        if (strtolower(Request::method()) != 'post') {
            return null;
        }

        $compliant = Request::getInt('hipaacompliant', 0, 'post');

        if (!$compliant) {
            return Lang::txt('Please select wether the uploaded materials contain potentially sensitive data or not.');
        }

        if ($compliant == 2) {
            // The raw column: group_owner itself goes through a transformer that
            // always gives a Group object, so this check never failed
            if (!$resource->get('group_owner') || !in_array($resource->get('access'), [4, 5])) {
                return Lang::txt(
                    'If materials are marked as containing potentially sensitive data, the Resource must be private to '
                        . 'a group.',
                );
            }
        }

        // The stored parameters, not the transformed ones merged with the component's
        $params = new \Hubzero\Config\Registry($resource->get('params'));
        $params->set('hipaacompliant', $compliant);

        $resource->set('params', $params->toString());

        return null;
    }
}
