<?php

/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Helpers;

use Components\Newsletter\Models\Campaign;
use Components\Newsletter\Models\Page;
use Components\Newsletter\Secrets\PageCode;

class CodeHelper
{
    // Validate that the user-supplied URL and code are valid:
    public static function validateCode($username, $campaignId, $pageId, $code)
    {
        // acquire campaign info from database:
        $campModel = Campaign::all()->whereEquals('id', $campaignId)->row();
        $campNotExpired = !$campModel->isExpired();

        // Previously this functionality assumed that all users had access to all pages
        // and ran a cron job that populated a db table to ensure this was the case.
        // Here rather than maintaining the table we instead just assume that all users have access.

        // check for existence of page in database:
        $pageExists = (1 == Page::all()->whereEquals('id', $pageId)->total());

        // Calculate and compare hash of hub, user, and campaign secrets to passed code:
        $database = \Hubzero\Facades\App::get('db');
        $vars = array(
                    $campaignId,
                    $username
                );

        $sql = "SELECT hash_access_code(?, ?)";
        $database->prepare($sql)->bind($vars)->execute();

        $hashMatches = ($code == $database->loadResult());

        // Is this access valid?
        return ($hashMatches && $campNotExpired && $pageExists);
    }

    // Validate code obtained from user's URL, using email subscription page id
    public static function validateEmailSubscriptionsCode($username, $campaignId, $code)
    {
        $emailSubsPageId = PageCode::$emailSubscriptionsPageId;

        // Validate user-supplied URL and code for this page:
        return self::validateCode($username, $campaignId, $emailSubsPageId, $code);
    }
}
