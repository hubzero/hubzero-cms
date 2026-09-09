@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$base = $member->link() . '&active=' . $name;

$__view->css()
    ->js('jquery.masonry', 'com_collections')
    ->js('jquery.infinitescroll', 'com_collections')
    ->js();
@endphp

<form method="get" action="{{ Route::url($base . '&task=all') }}" id="collections">
    {!! $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('member', $member)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', 'collections')
        ->set('collections', $rows->total())
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->set('following', $following)
        ->loadTemplate() !!}

    @if (!User::isGuest() && !$params->get('access-create-collection'))
        <p class="guest-options">
            @if ($model->isFollowing())
                <a class="icon-unfollow unfollow btn"
                    data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL') }}"
                    data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL') }}"
                    href="{{ Route::url($base . '&task=unfollow') }}">
                    <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL') }}</span>
                </a>
            @else
                <a class="icon-follow follow btn"
                    data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL') }}"
                    data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL') }}"
                    href="{{ Route::url($base . '&task=follow') }}">
                    <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL') }}</span>
                </a>
            @endif
        </p>
    @endif

    @if ($rows->total() > 0)
        <div id="posts"
            data-base="{{ rtrim(Request::base(true), '/') }}"
            class="{{ User::isGuest() ? 'loggedout' : 'loggedin' }}">

            @if (!User::isGuest() && $params->get('access-create-collection') && !Request::getInt('no_html', 0))
                <div class="post new-collection">
                    <a class="icon-add add" href="{{ Route::url($base . '&task=new') }}">
                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_COLLECTION') }}</span>
                    </a>
                </div>
            @endif

            @foreach ($rows as $row)
                @php
                    $name = e(stripslashes($row->creator('name')));
                @endphp
                <div class="post collection {{ $row->get('access') == 4 ? 'private' : 'public' }}{{ $row->get('is_default') ? ' default' : '' }}"
                    id="b{{ $row->get('id') }}"
                    data-id="{{ $row->get('id') }}">
                    <div class="content">
                        {!! $__view->view('default_collection', 'post')
                            ->set('row', $row)
                            ->set('collection', $row)
                            ->loadTemplate() !!}

                        @if ($tags = $row->item()->tags('cloud'))
                            <div class="tags-wrap">{!! $tags !!}</div>
                        @endif

                        <div class="meta">
                            <p class="stats">
                                <span class="likes">{!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $row->get('positive', 0)) !!}</span>
                                <span class="reposts">{!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $row->get('posts', 0)) !!}</span>
                            </p>
                            <div class="actions">
                                @if (!User::isGuest())
                                    @php
                                        $isMemberOwned = $row->get('object_type') == 'member'
                                            && $row->get('object_id') == User::get('id');
                                    @endphp
                                    @if ($isMemberOwned)
                                        @if ($params->get('access-edit-collection'))
                                            <a class="btn edit"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($base . '&task=' . $row->get('alias') . '/edit') }}">
                                                <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT') }}</span>
                                            </a>
                                        @endif
                                        @if ($params->get('access-delete-collection'))
                                            <a class="btn delete"
                                                data-id="{{ $row->get('id') }}"
                                                href="{{ Route::url($base . '&task=' . $row->get('alias') . '/delete') }}">
                                                <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') }}</span>
                                            </a>
                                        @endif
                                    @else
                                        <a class="btn repost"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=' . $row->get('alias') . '/collect') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT') }}</span>
                                        </a>
                                        @if ($row->isFollowing())
                                            <a class="btn unfollow"
                                                data-id="{{ $row->get('id') }}"
                                                data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW') }}"
                                                data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW') }}"
                                                href="{{ Route::url($base . '&task=' . $row->get('alias') . '/unfollow') }}">
                                                <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW') }}</span>
                                            </a>
                                        @else
                                            <a class="btn follow"
                                                data-id="{{ $row->get('id') }}"
                                                data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW') }}"
                                                data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW') }}"
                                                href="{{ Route::url($base . '&task=' . $row->get('alias') . '/follow') }}">
                                                <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW') }}</span>
                                            </a>
                                        @endif
                                    @endif
                                @else
                                    @php
                                        $returnUrl = base64_encode(Route::url($base . '&task=' . $row->get('alias'), false, true));
                                        $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $returnUrl, false);
                                    @endphp
                                    <a class="btn repost"
                                        href="{{ $loginUrl }}"
                                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT') }}</span>
                                    </a>
                                    <a class="btn follow"
                                        href="{{ $loginUrl }}"
                                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($row->get('object_type') == 'member' && $row->get('object_id') != User::get('id'))
                            <div class="convo attribution clearfix">
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
                                    @if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels()))
                                        <a href="{{ Route::url($row->creator()->link()) }}">{{ $name }}</a>
                                    @else
                                        {{ $name }}
                                    @endif
                                    <br />
                                    <span class="entry-date">
                                        <span class="entry-date-at">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_AT') }}</span>
                                        <span class="date"><time datetime="{{ $row->created() }}">{{ $row->created('time') }}</time></span>
                                        <span class="entry-date-on">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_ON') }}</span>
                                        <span class="time"><time datetime="{{ $row->created() }}">{{ $row->created('date') }}</time></span>
                                    </span>
                                </p>
                            </div>
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
                $pageNav->setAdditionalUrlParam('task', 'all');
            @endphp
            {!! $pageNav->render() !!}
        @endif
    @else
        <div id="collection-introduction">
            @if ($params->get('access-create-collection'))
                <div class="instructions">
                    <ol>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP1') }}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP2') }}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP3') }}</li>
                    </ol>
                    <div class="new-collection">
                        <a class="icon-add add" href="{{ Route::url($base . '&task=new') }}">
                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_COLLECTION') }}</span>
                        </a>
                    </div>
                </div>
                <div class="questions">
                    <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_COLLECTION') }}</strong></p>
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_COLLECTION_EXPLANATION') }}</p>
                </div>
            @else
                <div class="instructions">
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NONE') }}</p>
                </div>
            @endif
        </div>
    @endif
</form>
