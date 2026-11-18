<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Resources Plugin class for adding Dublin Core metadata to the document
 */

namespace Plugins\Resources\Dublincore;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Document;
use Hubzero\Facades\Request;

class Dublincore extends Plugin
{
    /**
     * Return data on a resource view (this will be some form of HTML)
     *
     * @param   \Components\Resources\Models\Entry  $model   Current model
     * @param   string  $option  Name of the component
     * @param   array<mixed>   $areas   Active area(s)
     * @param   string  $rtrn    Data to be returned
     * @return  void
     */
    public function onResources($model, $option, $areas, $rtrn = 'all')
    {
        if (!App::isSite()) {
            return;
        }

        if (Request::getWord('tmpl') || Request::getWord('format') || Request::getInt('no_html')) {
            return;
        }

        $view = $this->view();

        // Add metadata
        Document::setMetaData('dc.title', $view->escape($model->title));

        $thedate = $model->date;

        if ($thedate) {
            Document::setMetaData('dc.date', Date::of($thedate)->toLocal('Y-m-d'));
        }

        if ($model->isTool()) {
            $tconfig = Component::params('com_tools');

            if ($model->doi && $tconfig->get('doi_shoulder')) {
                $doi = $tconfig->get('doi_shoulder') . '/' . strtoupper($model->doi);
            } else {
                $doi = '10254/' . $tconfig->get('doi_prefix') . $model->id . '.' . $model->doi_label;
            }

            Document::setMetaData('dc.identifier', $view->escape($doi));
        }

        Document::setMetaData('dcterms.description', $view->escape($model->introtext));

        if ($license = $model->params->get('license', '')) {
            Document::setMetaData('dcterms.license', $view->escape($license));
        }

        // Authors are resource Author models: their name and organization
        // come from the author row, or the member's profile when it has none
        foreach ($model->contributors('!submitter') as $contributor) {
            if (!$contributor instanceof \Components\Resources\Models\Author) {
                continue;
            }
            if (strtolower((string) $contributor->get('role')) == 'submitter') {
                continue;
            }

            $name = stripslashes((string) $contributor->name);
            $org  = stripslashes(trim((string) $contributor->organization));

            Document::setMetaData(
                'dcterms.creator',
                $view->escape($name . ($org ? ', ' . $org : '')),
            );
        }
    }
}
