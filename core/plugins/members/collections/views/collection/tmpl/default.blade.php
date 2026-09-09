@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

$base = $member->link() . '&active=' . $name;

if (!$collection->get('layout')) {
    $collection->set('layout', 'grid');
}
$viewas = Request::getWord('viewas', $collection->get('layout'));
if (!in_array($viewas, ['grid', 'list'])) {
    $viewas = 'grid';
}

$__view->css()
    ->js('jquery.masonry', 'com_collections')
    ->js('jquery.infinitescroll', 'com_collections')
    ->js();

$allow_comments = Component::params('com_collections')->get('allow_comments');
@endphp

<form method="get"
    action="{{ Route::url($base . '&task=' . $collection->get('alias')) }}"
    id="collections">

    {!! $__view->view('_submenu', 'collection')
        ->set('params', $params)
        ->set('option', $option)
        ->set('member', $member)
        ->set('name', $name)
        ->set('active', ($collection->exists() ? '' : 'posts'))
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->set('following', $following)
        ->loadTemplate() !!}

    @if ($collection->exists())
        @php
            $createdSortUrl = Route::url($collection->link() . '&sort=created&viewas=' . $viewas);
            $orderingSortUrl = Route::url($collection->link() . '&sort=ordering&viewas=' . $viewas);
            $gridViewUrl = Route::url($collection->link() . '&sort=' . $filters['sort'] . '&viewas=grid');
            $listViewUrl = Route::url($collection->link() . '&sort=' . $filters['sort'] . '&viewas=list');
        @endphp
        <p class="overview">
            <span class="title count">
                "{{ e(stripslashes($collection->get('title'))) }}"
            </span>
            <span class="posts count">
                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $total) !!}
            </span>

            @if (!User::isGuest() && !$params->get('access-create-item'))
                @if ($collection->isFollowing())
                    <a class="icon-unfollow unfollow btn"
                        data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS') }}"
                        data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS') }}"
                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_TITLE') }}"
                        href="{{ Route::url($collection->link() . '/unfollow') }}">
                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS') }}</span>
                    </a>
                @else
                    <a class="icon-follow follow btn"
                        data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS') }}"
                        data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS') }}"
                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_TITLE') }}"
                        href="{{ Route::url($collection->link() . '/follow') }}">
                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS') }}</span>
                    </a>
                @endif
                <a class="icon-repost repost btn"
                    title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT_TITLE') }}"
                    href="{{ Route::url($collection->link() . '/collect') }}">
                    <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT') }}</span>
                </a>
            @endif

            <span class="options sort-options">
                <a href="{{ $createdSortUrl }}"
                    class="icon-created{{ $filters['sort'] == 'created' ? ' selected' : '' }}"
                    data-view="sort-created"
                    title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT') }}">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT') }}</a>
                <a href="{{ $orderingSortUrl }}"
                    class="icon-ordering{{ $filters['sort'] == 'ordering' ? ' selected' : '' }}"
                    data-view="sort-ordering"
                    title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT') }}">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT') }}</a>
            </span>
            <span class="options view-options">
                <a href="{{ $gridViewUrl }}"
                    class="icon-grid{{ $viewas == 'grid' ? ' selected' : '' }}"
                    data-view="view-grid"
                    title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW') }}">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW') }}</a>
                <a href="{{ $listViewUrl }}"
                    class="icon-list{{ $viewas == 'list' ? ' selected' : '' }}"
                    data-view="view-list"
                    title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW') }}">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW') }}</a>
            </span>
        </p>
    @endif

    @if ($rows->total() > 0)
        @php
            $baseUrl = rtrim(Request::base(true), '/');
            $updateUrl = Route::url('index.php?option=com_collections&controller=posts&task=reorder&' . Session::getFormToken() . '=1');
            $viewCls = $viewas . ' ' . (User::isGuest() ? 'loggedout' : 'loggedin');
        @endphp
        <div id="posts"
            data-base="{{ $baseUrl }}"
            data-update="{{ $updateUrl }}"
            class="view-{{ $viewCls }}">

            @if ($params->get('access-create-collection') && !Request::getInt('no_html', 0))
                <div class="post new-post" id="post_0">
                    <a class="icon-add add"
                        href="{{ Route::url($base . '&task=post/new&board=' . $collection->get('alias')) }}">
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST') }}
                    </a>
                </div>
            @endif

            @foreach ($rows as $row)
                @php
                    $item = $row->item();
                    $metadataUrl = Route::url('index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id'));
                    $name = e(stripslashes($row->creator()->get('name')));
                @endphp
                <div class="post {{ $item->type() }}"
                    id="post_{{ $row->get('id') }}"
                    data-id="{{ $row->get('id') }}"
                    data-closeup-url="{{ Route::url($base . '&task=post/' . $row->get('id')) }}">
                    <div class="content">
                        {!! $__view->view('default_' . $item->type(), 'post')
                            ->set('name', $name)
                            ->set('option', $option)
                            ->set('member', $member)
                            ->set('params', $params)
                            ->set('row', $row)
                            ->loadTemplate() !!}

                        @if ($tags = $item->tags('cloud'))
                            <div class="tags-wrap">{!! $tags !!}</div>
                        @endif

                        <div class="meta" data-metadata-url="{{ $metadataUrl }}">
                            <p class="stats">
                                <span class="likes">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)) }}</span>
                                @if ($allow_comments)
                                    <span class="comments">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)) }}</span>
                                @endif
                                <span class="reposts">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)) }}</span>
                            </p>
                            <div class="actions">
                                @if (!User::isGuest())
                                    @if ($row->get('created_by') == User::get('id'))
                                        <a class="btn edit"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/edit') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT') }}</span>
                                        </a>
                                    @else
                                        @php
                                            $voteCls = $item->get('voted') ? 'unlike' : 'like';
                                            $voteLabel = $item->get('voted')
                                                ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE')
                                                : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE');
                                        @endphp
                                        <a class="btn vote {{ $voteCls }}"
                                            data-id="{{ $row->get('id') }}"
                                            data-text-like="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE') }}"
                                            data-text-unlike="{{ Lang::txt('Unlike') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/vote') }}">
                                            <span>{{ $voteLabel }}</span>
                                        </a>
                                    @endif

                                    @if ($allow_comments)
                                        <a class="btn comment"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT') }}</span>
                                        </a>
                                    @endif

                                    <a class="btn repost"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/collect') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT') }}</span>
                                    </a>

                                    @php
                                        $isOriginalOwner = $row->get('original')
                                            && ($item->get('created_by') == User::get('id')
                                            || $params->get('access-delete-item'));
                                        $isEditor = $row->get('created_by') == User::get('id')
                                            || $params->get('access-edit-item');
                                    @endphp

                                    @if ($isOriginalOwner)
                                        <a class="btn delete"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/delete') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') }}</span>
                                        </a>
                                    @elseif ($isEditor)
                                        <a class="btn unpost"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/remove') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_REMOVE') }}</span>
                                        </a>
                                    @endif
                                @else
                                    @php
                                        $returnUrl = base64_encode(Route::url($base . '&task=' . $collection->get('alias'), false, true));
                                        $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $returnUrl, false);
                                    @endphp
                                    <a class="btn vote like"
                                        href="{{ $loginUrl }}"
                                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE') }}</span>
                                    </a>
                                    @if ($allow_comments)
                                        <a class="btn comment"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT') }}</span>
                                        </a>
                                    @endif
                                    <a class="btn repost"
                                        href="{{ $loginUrl }}"
                                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="convo attribution reposted">
                            @if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels()))
                                <a href="{{ Route::url($row->creator()->link()) }}"
                                    title="{{ $name }}"
                                    class="img-link">
                                    <img src="{{ $row->creator()->picture() }}"
                                        alt="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name) }}" />
                                </a>
                            @else
                                <span class="img-link">
                                    <img src="{{ $row->creator()->picture() }}"
                                        alt="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name) }}" />
                                </span>
                            @endif
                            <p>
                                @php
                                    $who = $name;
                                    if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) {
                                        $who = '<a href="' . Route::url($row->creator()->link()) . '">' . $name . '</a>';
                                    }
                                    $where = '<a href="' . Route::url($row->link()) . '">' . e(stripslashes($row->get('title'))) . '</a>';
                                @endphp
                                {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where) !!}
                                <br />
                                <span class="entry-date">
                                    <span class="entry-date-at">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_AT') }}</span>
                                    <span class="time"><time datetime="{{ $row->created() }}">{{ $row->created('time') }}</time></span>
                                    <span class="entry-date-on">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_ON') }}</span>
                                    <span class="date"><time datetime="{{ $row->created() }}">{{ $row->created('date') }}</time></span>
                                </span>
                            </p>
                        </div>

                        @if (!User::isGuest() && $params->get('access-create-item') && $filters['sort'] == 'ordering')
                            <div class="sort-handle" title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_GRAB_TO_REORDER') }}"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($total > $filters['limit'])
            @php
                $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
                $pageNav->setAdditionalUrlParam('id', $member->get('id'));
                $pageNav->setAdditionalUrlParam('active', 'collections');
                $pageNav->setAdditionalUrlParam('task', $task);
                $pageNav->setAdditionalUrlParam('viewas', $viewas);
                $pageNav->setAdditionalUrlParam('sort', $filters['sort']);
            @endphp
            {!! $pageNav->render() !!}
        @endif
    @else
        <div id="collection-introduction">
            @if ($params->get('access-create-item'))
                <div class="instructions">
                    <ol>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP1') }}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP2') }}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP3') }}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP4') }}</li>
                    </ol>
                    <div class="new-post">
                        <a class="icon-add add"
                            href="{{ Route::url($base . '&task=post/new&board=' . $collection->get('alias')) }}">
                            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST') }}
                        </a>
                    </div>
                </div>
                <div class="questions">
                    <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST') }}</strong></p>
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST_EXPLANATION') }}</p>
                </div>
            @else
                <div class="instructions">
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_EMPTY_COLLECTION') }}</p>
                </div>
            @endif
        </div>
    @endif
</form>
