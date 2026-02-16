<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices
 *
*/
class Migration20140408121756ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__courses_members', 'token')) {
            $schema->addColumn('#__courses_members', 'token')
                ->string(23)
                ->notNull()
                ->default('')
                ->execute();

            $path = PATH_APP . DS . 'site' . DS . 'courses' . DS . 'certificates';

            if (is_dir($path)) {
                if (
                    file_exists(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php')
                ) {
                    require_once PATH_CORE . DS . 'components' . DS . 'com_courses'
                        . DS . 'models' . DS . 'course.php';

                    // Loop through all files and separate them into arrays of images, folders, and other
                    $dirIterator = new DirectoryIterator($path);
                    foreach ($dirIterator as $file) {
                        if ($file->isDot()) {
                            continue;
                        }

                        if ($file->isDir()) {
                            continue;
                        }

                        if ($file->isFile()) {
                            $name = $file->getFilename();
                            if (
                                ('cvs' == strtolower($name))
                                || ('.svn' == strtolower($name))
                            ) {
                                continue;
                            }

                            $bits = explode('_', $name);
                            if (count($bits) < 4) {
                                continue;
                            }

                            $course = $bits[1];
                            $offering = $bits[2];
                            $user = strstr($bits[3], '.', true);

                            $member = \Components\Courses\Models\Member::getInstance($user, $course, $offering, null);
                            $member->token();
                        }
                    }
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_members', 'token')) {
            $schema->dropColumn('#__courses_members', 'token');
        }
    }
}
