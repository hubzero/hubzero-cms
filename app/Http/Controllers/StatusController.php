<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function __invoke()
    {
        $dbConnected = false;
        $dbVersion = '';
        $dbError = '';
        $tableCount = 0;

        try {
            $dbVersion = DB::selectOne('SELECT VERSION() AS v')->v;
            $tableCount = count(DB::select('SHOW TABLES'));
            $dbConnected = true;
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        return view('status', [
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
            'dbConnected' => $dbConnected,
            'dbVersion' => $dbVersion,
            'dbError' => $dbError,
            'tableCount' => $tableCount,
        ]);
    }
}
