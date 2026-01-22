<?php

// @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;

/**
 * Courses Plugin class for course PEC
 */
class PlgCoursesPec extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * Return the alias and name for this category of content
     *
     * @return     array
     */
    public function onOfferingEdit()
    {
        $area = array(
            'name' => $this->_name,
            'title' => Lang::txt('PLG_COURSES_' . strtoupper($this->_name))
        );
        return $area;
    }

    /**
     * Return data on a resource view (this will be some form of HTML)
     *
     * @param      object  $course
     * @param      object  $offering
     * @param      object  $section
     * @return     string|null
     */
    public function onCourseEnrolled($course, $offering, $section)
    {
        if (!$course->exists() || !$offering->exists()) {
            return null;
        }

        $offering->section($section->get('alias'));
        $url = $offering->link() . '&task=enroll';

        if ($offering->params('pec_register', 0) && $offering->params('pec_course', 0)) {
            $url = str_replace(
                '{{course}}',
                $offering->params('pec_course', 0),
                $offering->params('url', 'https://www.distance.purdue.edu/{{course}}')
            );
        }

        return $url;
    }

    /**
     * Actions to perform after saving an offering
     *
     * @param      object  $model \Components\Courses\Models\Section
     * @param      boolean $isNew Is this a newly created entry?
     * @return     void
     */
    public function onSectionSave($model, $isNew = false)
    {
        if (!$model->exists()) {
            return;
        }
    }

    /**
     * Actions to perform after deleting an offering
     *
     * @param      object  $model \Components\Courses\Models\Section
     * @return     void
     */
    public function onSectionDelete($model)
    {
        if (!$model->exists()) {
            return;
        }
    }
}
