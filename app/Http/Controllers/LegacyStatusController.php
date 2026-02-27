<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\Http\Controllers;

use App\View\LegacyTemplateRenderer;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LegacyStatusController extends Controller
{
    public function __invoke(): Response
    {
        // Generate the same status content as StatusController
        $componentHtml = $this->buildStatusHtml();

        // Render through the legacy template engine
        $renderer = new LegacyTemplateRenderer(
            'hubzero',
            base_path('app/templates/hubzero')
        );

        $html = $renderer->render($componentHtml, 'System Status');

        return new Response($html);
    }

    private function buildStatusHtml(): string
    {
        $laravelVersion = app()->version();
        $phpVersion = PHP_VERSION;
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

        $dbStatus = $dbConnected
            ? "<p><strong>Connected</strong> — {$dbVersion}</p><p>{$tableCount} tables</p>"
            : "<p><strong>Not connected</strong> — " . htmlspecialchars($dbError) . "</p>";

        return <<<HTML
            <div class="grid">
                <h2>System Status</h2>
                <table class="adminlist">
                    <tbody>
                        <tr>
                            <th scope="row">Laravel</th>
                            <td>{$laravelVersion}</td>
                        </tr>
                        <tr>
                            <th scope="row">PHP</th>
                            <td>{$phpVersion}</td>
                        </tr>
                        <tr>
                            <th scope="row">Database</th>
                            <td>{$dbStatus}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        HTML;
    }
}
