<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\BlockElement;

use Components\Publications\Models\BlockElement as Base;
use Hubzero\Facades\Request;

/**
 * Renders URL selector element
 */
class Dataselector extends Base
{
    /**
     * Element name
     *
     * @var  string
     */
    protected $name = 'dataselector';

    /**
     * Git helper
     *
     * @var  string
     */
    protected $git = null;

    /**
     * Project repo path
     *
     * @var  string
     */
    protected $path = null;

    /**
     * Render
     *
     * @param   integer  $elementid
     * @param   object   $manifest
     * @param   object   $pub
     * @param   string   $viewname
     * @param   string   $status
     * @param   object   $master
     * @param   integer  $order
     * @return  string
     */
    public function render(
        $elementid,
        $manifest,
        $pub = null,
        $viewname = 'edit',
        $status = null,
        $master = null,
        $order = 0,
    ) {
        $html = '';

        // Get project path
        $this->path = $pub->_project->repo()->get('path');

        $showElement = $master->props['showElement'];
        $total       = $master->props['total'];

        // Incoming
        $activeElement = Request::getInt('el', $showElement);

        // Git helper
        if (!$this->git) {
            $this->git = new \Components\Projects\Helpers\Git($this->path);
        }

        // Do we need to collapse inactive elements?
        $collapse = isset($master->params->collapse_elements) && $master->params->collapse_elements ? 1 : 0;

        switch ($viewname) {
            case 'edit':
            default:
                $html = $this->drawSelector(
                    $elementid,
                    $manifest,
                    $pub,
                    $status->elements->$elementid,
                    $activeElement,
                    $collapse,
                    $total,
                    $master,
                    $order,
                );
                break;

            case 'freeze':
            case 'curator':
                $html = $this->drawItem(
                    $elementid,
                    $manifest,
                    $pub,
                    $status->elements->$elementid,
                    $master,
                    $viewname,
                );
                break;
        }

        return $html;
    }

    /**
     * Draw element without editing capabilities
     *
     * @param   integer  $elementId
     * @param   object   $manifest
     * @param   object   $pub
     * @param   string   $status
     * @param   object   $master
     * @param   string   $viewname
     * @return  string
     */
    public function drawItem($elementId, $manifest, $pub = null, $status = null, $master = null, $viewname = 'freeze')
    {
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'publications',
            'name'    => 'freeze',
            'layout'  => 'dataselector',
        ]);

        // Get attachment type model
        $attModel = new \Components\Publications\Models\Attachments($this->parent->db);

        // Make sure we have attachments
        if (!isset($pub->_attachments)) {
            // Get attachments
            $pContent = new \Components\Publications\Tables\Attachment($this->parent->db);
            $pub->_attachments = $pContent->sortAttachments($pub->version_id);
        }

        // Get attached items
        $attachments = $pub->_attachments;
        $attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;
        $attachments = $attModel->getElementAttachments(
            $elementId,
            $attachments,
            $manifest->params->type,
            $manifest->params->role,
        );

        $view->set('type', $manifest->params->type);
        $view->set('path', $this->path);
        $view->set('pub', $pub);
        $view->set('manifest', $manifest);
        $view->set('status', $status);
        $view->set('elementId', $elementId);
        $view->set('attachments', $attachments);
        $view->set('database', $this->parent->db);
        $view->set('master', $master);
        $view->set('name', $viewname);
        $view->set('viewer', 'freeze');
        $view->set('git', $this->git);

        return $view->loadTemplate();
    }

    /**
     * Draw file selector
     *
     * @param   integer  $elementId
     * @param   object   $manifest
     * @param   object   $pub
     * @param   string   $status
     * @param   integer  $active
     * @param   integer  $collapse
     * @param   integer  $total
     * @param   object   $master
     * @param   integer  $order
     * @return  string
     */
    public function drawSelector(
        $elementId,
        $manifest,
        $pub = null,
        $status = null,
        $active = 0,
        $collapse = 0,
        $total = 0,
        $master = null,
        $order = 0,
    ) {
        // Get attachment type model
        $attModel = new \Components\Publications\Models\Attachments($this->parent->db);

        // Make sure we have attachments
        if (!isset($pub->_attachments)) {
            // Get attachments
            $pContent = new \Components\Publications\Tables\Attachment($this->parent->db);
            $pub->_attachments = $pContent->sortAttachments($pub->version_id);
        }

        // Get attached items
        $attachments = $pub->_attachments;
        $attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;
        $attachments = $attModel->getElementAttachments(
            $elementId,
            $attachments,
            $manifest->params->type,
            $manifest->params->role,
        );

        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'publications',
            'name'    => 'blockelement',
            'layout'  => 'dataselector',
        ]);

        $view->set('type', $manifest->params->type);
        $view->set('path', $this->path);
        $view->set('pub', $pub);
        $view->set('manifest', $manifest);
        $view->set('status', $status);
        $view->set('elementId', $elementId);
        $view->set('attachments', $attachments);
        $view->set('active', $active);
        $view->set('collapse', $collapse);
        $view->set('total', $total);
        $view->set('master', $master);
        $view->set('database', $this->parent->db);
        $view->set('order', $order);
        $view->set('viewer', 'edit');
        $view->set('git', $this->git);

        return $view->loadTemplate();
    }
}
