<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

use Hubzero\User\User;
use Hubzero\User\Password;
use Components\Courses\Models\Course;
use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Lang;

/**
 * Helper for PREMIS bulk registration and course enrollment
 */
class Premis
{
    /**
     * Register (or find) a user and manage course enrollment
     *
     * @param   array  $user     User data: fName, lName, email, casId, premisId, password, premisEnrollmentId
     * @param   array  $courses  Course data: add, drop (course aliases)
     * @return  array  ['status' => 'ok'|'error', 'message' => '...']
     */
    public static function doRegistration($user, $courses)
    {
        $messages = array();

        // Validate required fields
        if (empty($user['email'])) {
            return array('status' => 'error', 'message' => 'No email address provided.');
        }

        if (empty($user['fName']) || empty($user['lName'])) {
            return array('status' => 'error', 'message' => 'First and last name are required.');
        }

        // Check if user already exists by email
        $member = User::oneByEmail($user['email']);

        if ($member->get('id')) {
            $messages[] = 'Existing user found (ID: ' . $member->get('id') . ').';
        } else {
            // Create new user
            $member = self::createUser($user);

            if (!$member || !$member->get('id')) {
                return array(
                    'status' => 'error',
                    'message' => 'Failed to create user account for ' . $user['email'] . '.'
                );
            }

            $messages[] = 'User created (ID: ' . $member->get('id') . ').';
        }

        $userId = $member->get('id');

        // Process course additions
        if (!empty($courses['add'])) {
            $addResult = self::enrollInCourses($userId, $courses['add']);
            $messages = array_merge($messages, $addResult);
        }

        // Process course drops
        if (!empty($courses['drop'])) {
            $dropResult = self::dropFromCourses($userId, $courses['drop']);
            $messages = array_merge($messages, $dropResult);
        }

        return array(
            'status' => 'ok',
            'message' => implode(' ', $messages)
        );
    }

    /**
     * Create a new user account from PREMIS data
     *
     * @param   array  $user  User data array
     * @return  User|false
     */
    protected static function createUser($user)
    {
        $member = User::blank();

        // Build username from email prefix, ensure uniqueness
        $username = self::generateUsername($user['email']);
        if (!$username) {
            return false;
        }

        $name = trim($user['fName'] . ' ' . $user['lName']);

        $member->set('username', $username);
        $member->set('name', $name);
        $member->set('givenName', $user['fName']);
        $member->set('surname', $user['lName']);
        $member->set('email', $user['email']);
        $member->set('registerDate', Date::toSql());
        $member->set('activation', 1); // Pre-activated (admin import)

        // Set default access group
        $config = Component::params('com_members');
        $newUsertype = $config->get('new_usertype', 2);
        $member->set('accessgroups', array($newUsertype));
        $member->set('access', 5);

        // Set home directory
        $homedir = rtrim($config->get('homedir', '/home'), '/');
        $member->set('homeDirectory', $homedir . '/' . $username);
        $member->set('loginShell', '/bin/bash');
        $member->set('ftpShell', '/usr/lib/sftp-server');

        if (!$member->save()) {
            return false;
        }

        // Set password if provided
        if (!empty($user['password'])) {
            Password::changePassword($member->get('id'), $user['password']);
        }

        return $member;
    }

    /**
     * Generate a unique username from an email address
     *
     * @param   string  $email  Email address
     * @return  string|false
     */
    protected static function generateUsername($email)
    {
        $parts = explode('@', $email);
        $base = preg_replace('/[^a-z0-9]/', '', strtolower($parts[0]));

        if (empty($base)) {
            $base = 'user';
        }

        // Check if base username is available
        $username = $base;
        $existing = User::oneByUsername($username);

        if ($existing->get('id')) {
            // Append numbers until unique
            $i = 1;
            do {
                $username = $base . $i;
                $existing = User::oneByUsername($username);
                $i++;
            } while ($existing->get('id') && $i < 1000);

            if ($existing->get('id')) {
                return false;
            }
        }

        return $username;
    }

    /**
     * Enroll a user in one or more courses
     *
     * @param   int     $userId   User ID
     * @param   string  $courses  Comma-separated course aliases
     * @return  array   Status messages
     */
    protected static function enrollInCourses($userId, $courses)
    {
        $messages = array();

        $aliases = array_map('trim', explode(',', $courses));

        foreach ($aliases as $alias) {
            if (empty($alias)) {
                continue;
            }

            $course = Course::getInstance($alias);
            if (!$course->exists()) {
                $messages[] = 'Course "' . $alias . '" not found.';
                continue;
            }

            $offering = $course->offering();
            if (!$offering->exists()) {
                $messages[] = 'No offering found for course "' . $alias . '".';
                continue;
            }

            $section = $offering->section();
            if (!$section->exists()) {
                $messages[] = 'No section found for course "' . $alias . '".';
                continue;
            }

            $section->add($userId);
            if ($section->getError()) {
                $messages[] = 'Enroll in "' . $alias . '" failed: ' . $section->getError();
            } else {
                $messages[] = 'Enrolled in "' . $alias . '".';
            }
        }

        return $messages;
    }

    /**
     * Drop a user from one or more courses
     *
     * @param   int     $userId   User ID
     * @param   string  $courses  Comma-separated course aliases
     * @return  array   Status messages
     */
    protected static function dropFromCourses($userId, $courses)
    {
        $messages = array();

        $aliases = array_map('trim', explode(',', $courses));

        foreach ($aliases as $alias) {
            if (empty($alias)) {
                continue;
            }

            $course = Course::getInstance($alias);
            if (!$course->exists()) {
                $messages[] = 'Course "' . $alias . '" not found for drop.';
                continue;
            }

            $offering = $course->offering();
            if (!$offering->exists()) {
                $messages[] = 'No offering for course "' . $alias . '" to drop.';
                continue;
            }

            $section = $offering->section();
            if (!$section->exists()) {
                $messages[] = 'No section for course "' . $alias . '" to drop.';
                continue;
            }

            $section->remove($userId);
            if ($section->getError()) {
                $messages[] = 'Drop from "' . $alias . '" failed: ' . $section->getError();
            } else {
                $messages[] = 'Dropped from "' . $alias . '".';
            }
        }

        return $messages;
    }
}
