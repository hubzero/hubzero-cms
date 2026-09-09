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

<form method="get"
    action="{{ Route::url($base . '&task=' . $collection->get('alias')) }}"
    id="collections">

    {!! $__view->view('_submenu', 'collection')
        ->set('params', $params)
        ->set('option', $option)
        ->set('member', $member)
        ->set('name', $name)
        ->set('active', 'livefeed')
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->set('following', $following)
        ->loadTemplate() !!}

    @if ($rows->total() > 0)
        <div id="posts" data-base="{{ rtrim(Request::base(true), '/') }}">
            @foreach ($rows as $row)
                @php
                    $item = $row->item();
                    $metadataUrl = Route::url('index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id'));
                    $name = e(stripslashes($row->creator()->get('name')));
                @endphp
                <div class="post {{ $item->type() }}"
                    id="b{{ $row->get('id') }}"
                    data-id="{{ $row->get('id') }}"
                    data-closeup-url="{{ Route::url($base . '&task=post/' . $row->get('id')) }}">
                    <div class="content">
                        {!! $__view->view('default_' . $item->type(), 'post')
                            ->set('name', $name)
                            ->set('option', $option)
                            ->set('member', $member)
                            ->set('params', $params)
                            ->set('row', $row)
                            ->set('board', $collection)
                            ->loadTemplate() !!}

                        @if (count($item->tags()) > 0)
                            <div class="tags-wrap">{!! $item->tags('render') !!}</div>
                        @endif

                        <div class="meta" data-metadata-url="{{ $metadataUrl }}">
                            <p class="stats">
                                <span class="likes">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)) }}</span>
                                <span class="comments">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)) }}</span>
                                <span class="reposts">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)) }}</span>
                            </p>
                            <div class="actions">
                                @if (!User::isGuest())
                                    @if ($item->get('created_by') == User::get('id'))
                                        <a class="btn edit"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/edit') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT') }}</span>
                                        </a>
                                    @else
                                        @php
                                            $voteClass = $item->get('voted') ? 'unlike' : 'like';
                                            $voteTxt = $item->get('voted')
                                                ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE')
                                                : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE');
                                        @endphp
                                        <a class="btn vote {{ $voteClass }}"
                                            data-id="{{ $row->get('id') }}"
                                            data-text-like="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE') }}"
                                            data-text-unlike="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/vote') }}">
                                            <span>{{ $voteTxt }}</span>
                                        </a>
                                    @endif

                                    <a class="btn comment"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT') }}</span>
                                    </a>
                                    <a class="btn repost"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/collect') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT') }}</span>
                                    </a>

                                    @if ($row->get('original') && $item->get('created_by') == User::get('id'))
                                        <a class="btn delete"
                                            data-id="{{ $row->get('id') }}"
                                            href="{{ Route::url($base . '&task=post/' . $row->get('id') . '/delete') }}">
                                            <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') }}</span>
                                        </a>
                                    @endif
                                @else
                                    @php
                                        $likeReturn = base64_encode(Route::url($base . '&task=post/' . $row->get('id') . '/vote', false, true));
                                        $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $likeReturn, false);
                                    @endphp
                                    <a class="btn vote like"
                                        href="{{ $loginUrl }}"
                                        title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE') }}</span>
                                    </a>
                                    <a class="btn comment"
                                        data-id="{{ $row->get('id') }}"
                                        href="{{ Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment') }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT') }}</span>
                                    </a>
                                    @php
                                        $collectReturn = base64_encode(Route::url($base . '&task=post/' . $row->get('id') . '/collect', false, true));
                                        $collectLoginUrl = Route::url('index.php?option=com_users&view=login&return=' . $collectReturn, false);
                                    @endphp
                                    <a class="btn repost"
                                        href="{{ $collectLoginUrl }}"
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
                    </div>
                </div>
            @endforeach
        </div>

        @if ($total > $filters['limit'])
            @php
                $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
                $pageNav->setAdditionalUrlParam('id', $member->get('id'));
                $pageNav->setAdditionalUrlParam('active', 'collections');
            @endphp
            {!! $pageNav->render() !!}
        @endif
    @else
        <div id="collection-introduction">
            @if ($params->get('access-create-item'))
                @if ($following <= 0)
                    <div class="instructions">
                        <ol>
                            <li>{!! Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1', Route::url('index.php?option=com_collections')) !!}</li>
                            <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2') }}</li>
                            <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3') }}</li>
                        </ol>
                    </div>
                    <div class="questions">
                        <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FEED') }}</strong></p>
                        <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FEED_EXPLANATION') }}</p>
                        <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING') }}</strong></p>
                        <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION') }}</p>
                        <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHERE_ARE_MY_POSTS') }}</strong></p>
                        <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHERE_ARE_MY_POSTS_EXPLANATION') }}</p>
                    </div>
                @else
                    <div class="instructions">
                        <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_NO_POSTS_AVAILABLE_FOR_YOU') }}</p>
                    </div>
                @endif
            @else
                <div class="instructions">
                    @if ($filters['collection_id'][0] == -1)
                        <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_MEMBER_NOT_FOLLOWING') }}</p>
                    @else
                        <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_NO_POSTS_AVAILABLE_FOR_THIS_MEMBER') }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif
</form>
