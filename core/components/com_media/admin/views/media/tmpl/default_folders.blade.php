{{--
  com_media — Recursive folder tree (sidebar panel)

  Variables: $folderTree (array), $folders_id (string, e.g. 'id="media-tree"'),
             $folder (string, current folder path), $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

$__tmpl  = Request::getCmd('tmpl', '');
$__token = Session::getFormToken();
$__opt   = $option;
$__cur   = $folder;

/**
 * Recursively renders a folder tree as nested <ul> elements.
 *
 * Depth 1 items are always marked open; deeper items are open only
 * when their path matches the current folder path.
 */
$renderFolderTree = null;
$renderFolderTree = function (
    array $tree,
    int $depth,
    string $currentFolder,
    string $tmpl,
    string $token,
    string $opt,
    ?string $treeId = null
) use (&$renderFolderTree): string {
    $idAttr = $treeId ? ' id="' . htmlspecialchars($treeId, ENT_QUOTES) . '"' : '';
    $out    = '<ul' . $idAttr . ' class="depth' . $depth . '">' . "\n";

    foreach ($tree as $folder) {
        $cls  = '';
        $icon = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);

        if ($depth === 1) {
            $cls  = ' class="open"';
            $icon = Html::asset(
                'image', 'assets/filetypes/folder-open.svg', '', null, true, true
            );
        } else {
            $trail = explode('/', trim($currentFolder, '/'));
            $p     = explode('/', trim($folder['path'], '/'));
            $open  = 0;

            foreach ($p as $i => $seg) {
                if (!isset($trail[$i])) {
                    break;
                }
                if ($p[$i] === $trail[$i]) {
                    $open++;
                }
            }

            if ($open && $open === count($p)) {
                $cls = ' class="open"';
            }
        }

        $escapedName = htmlspecialchars($folder['name'], ENT_QUOTES);
        $dataFolder  = htmlspecialchars('/' . ltrim($folder['path'], '/'), ENT_QUOTES);
        $folderHref  = Route::url(
            'index.php?option=' . $opt
            . '&controller=medialist&tmpl=' . $tmpl
            . '&' . $token . '=1'
            . '&folder=/' . urlencode($folder['path']), false
        );
        $out .= '<li id="' . $escapedName . '"' . $cls . '>' . "\n";
        $out .= '<a class="folder"'
            . ' data-folder="' . $dataFolder . '"'
            . ' href="' . $folderHref . '">'
            . '<span class="folder-icon">'
            . '<img src="' . htmlspecialchars($icon, ENT_QUOTES) . '"'
            . ' alt="' . $escapedName . '" />'
            . '</span>'
            . $escapedName
            . '</a>' . "\n";

        if (isset($folder['children']) && count($folder['children'])) {
            $out .= $renderFolderTree(
                $folder['children'],
                $depth + 1,
                $currentFolder,
                $tmpl,
                $token,
                $opt,
                'folder-' . $folder['name']
            );
        }

        $out .= '</li>' . "\n";
    }

    $out .= '</ul>' . "\n";
    return $out;
};

// Extract id value from the folders_id attribute string (e.g. ' id="media-tree"')
$__treeId = null;
if (isset($folders_id) && preg_match('/id="([^"]*)"/', $folders_id, $m)) {
    $__treeId = $m[1];
}
@endphp
{!! $renderFolderTree($folderTree, 1, $__cur, $__tmpl, $__token, $__opt, $__treeId) !!}
