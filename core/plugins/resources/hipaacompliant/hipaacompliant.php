<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Allow saving of hipaa compliance checkbox
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgResourcesHipaacompliant extends \Hubzero\Plugin\Plugin
{
    /**
     * Event call before a resource is submitted
     *
     * @param   object  $resource
     * @return  void
     */
    public function onResourceBeforeSubmit(&$resource)
    {
        // Check authorization
        if (!$resource->id) {
            return;
        }

        if (strtolower(Request::method()) != 'post') {
            return;
        }

        $compliant = Request::getInt('hipaacompliant', 0, 'post');

        if (!$compliant) {
            return Lang::txt('Please select wether the uploaded materials contain potentially sensitive data or not.');
        }

        if ($compliant == 2) {
            if (!$resource->group_owner || !in_array($resource->access, array(4, 5))) {
                return Lang::txt('If materials are marked as containing potentially sensitive data,
                     the Resource must be private to a group.');
            }
        }

        $params = new \Hubzero\Config\Registry($resource->params);
        $params->set('hipaacompliant', $compliant);

        $resource->params = $params->toString();
    }
}
