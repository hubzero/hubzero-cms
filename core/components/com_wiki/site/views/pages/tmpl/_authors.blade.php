{{--
 * Wiki page author attribution (Knol mode only)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
@endphp

@if($page->param('mode', 'wiki') == 'knol' && !$page->param('hide_authors', 0))
    @php
        $author = e(stripslashes($page->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN'))));
        $auths = [];
        $creatorLevels = User::getAuthorisedViewLevels();
        $creatorAccess = $page->creator->get('access');
        $creatorLink = '<a href="' . Route::url($page->creator->link()) . '">' . $author . '</a>';
        $auths[] = in_array($creatorAccess, $creatorLevels) ? $creatorLink : $author;

        foreach ($page->authors()->rows() as $auth) {
            if ($auth->get('user_id') == $page->get('created_by')) {
                continue;
            }
            $name = e(stripslashes($auth->user->get('name', '')));
            $authLink = '<a href="' . Route::url($auth->user->link()) . '">' . $name . '</a>';
            $auths[] = in_array($auth->user->get('access'), $creatorLevels) ? $authLink : $name;
        }
    @endphp
    <p class="text-sm text-base-content/60">{!! Lang::txt('COM_WIKI_BY_AUTHORS', implode(', ', $auths)) !!}</p>
@endif
