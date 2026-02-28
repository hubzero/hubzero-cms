<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Http;

use Hubzero\Framework\Component\Loader;
use Hubzero\Framework\View\LegacyTemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Dispatches legacy HubZero components via the fallback route.
 *
 * Parses the first path segment as the component name, maps it to
 * com_{component}, which is found by the Loader and executed through
 * the HubZero component lifecycle. Output is wrapped by the legacy
 * template engine.
 */
class ComponentDispatchController extends Controller
{
    public function dispatch(Request $request, Loader $loader): Response|RedirectResponse
    {
        $component = $request->segment(1, '');

        // For POST requests to / with an option param, resolve from POST data
        if (!$component && $request->isMethod('POST') && $request->input('option')) {
            $option = $request->input('option');
        } elseif (!$component || !preg_match('/^[a-z][a-z0-9_-]*$/', $component)) {
            abort(404);
        } else {
            // Check menu items first — a menu path like "login" may map to
            // com_users rather than the literal com_login.
            $option = $this->resolveComponentFromMenu($request->getPathInfo())
                ?? 'com_' . $component;
        }

        // Verify the component exists before dispatching
        if (!$loader->path($option)) {
            abort(404);
        }

        // Set the component option on the HubZero request
        $hubzeroRequest = app('hubzero.request');
        $hubzeroRequest->setVar('option', $option);

        // Also set query vars from the menu item's link
        $this->applyMenuQueryVars($hubzeroRequest);

        // Populate the breadcrumb trail from the menu item tree
        $this->buildPathwayFromMenu();

        // Execute the component and capture output.
        // Legacy components may call App::redirect() which throws
        // LegacyRedirectException to break out of the component and
        // return a proper redirect through the middleware pipeline.
        try {
            $componentHtml = $loader->render($option);
        } catch (LegacyRedirectException $e) {
            return new RedirectResponse($e->url, $e->statusCode);
        }

        // Read page title from Document service
        $document = app('hubzero.document');
        $title = $document->getTitle() ?: ucfirst($component);

        // Render through the legacy template
        $renderer = new LegacyTemplateRenderer(
            'hubzero',
            base_path('app/templates/hubzero')
        );

        $html = $renderer->render($componentHtml, $title);

        return new Response($html);
    }

    /**
     * Resolve a URL path to a component via the menu service.
     *
     * Menu items map SEF paths (e.g., "login") to components
     * (e.g., com_users). Returns null if no menu match found.
     */
    private function resolveComponentFromMenu(string $pathInfo): ?string
    {
        if (!app()->bound('hubzero.menu')) {
            return null;
        }

        $menu = app('hubzero.menu');
        $active = $menu->getActive();

        if ($active && !empty($active->component)) {
            return $active->component;
        }

        return null;
    }

    /**
     * Populate the breadcrumb trail from the active menu item's tree,
     * matching the legacy PathwayServiceProvider behavior.
     */
    private function buildPathwayFromMenu(): void
    {
        if (!app()->bound('hubzero.menu') || !app()->bound('hubzero.pathway')) {
            return;
        }

        $menu = app('hubzero.menu');
        $active = $menu->getActive();
        if (!$active || empty($active->tree)) {
            return;
        }

        $home = $menu->getDefault();
        if (is_object($home) && $active->id == $home->id) {
            return; // Don't add breadcrumbs for the home page
        }

        $pathway = app('hubzero.pathway');

        foreach ($active->tree as $menuId) {
            $link = $menu->getItem($menuId);
            if (!$link) {
                continue;
            }

            $type = $link->type ?? 'component';
            $url = match ($type) {
                'separator' => '',
                'url' => $link->link,
                'alias' => 'index.php?Itemid='
                    . ($link->params->get('aliasoptions') ?? $link->id),
                default => 'index.php?Itemid=' . $link->id,
            };

            $pathway->append($link->title, $url);
        }
    }

    /**
     * Apply query variables from the active menu item's link to the
     * HubZero request, so the component sees view/task/etc. params.
     */
    private function applyMenuQueryVars(object $hubzeroRequest): void
    {
        if (!app()->bound('hubzero.menu')) {
            return;
        }

        $active = app('hubzero.menu')->getActive();
        if (!$active || empty($active->query)) {
            return;
        }

        foreach ($active->query as $key => $value) {
            if ($key === 'option') {
                continue; // already set
            }
            $hubzeroRequest->setVar($key, $value);
        }
    }
}
