{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js();

$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=forum';

$category->set('section_alias', $section->get('alias'));
$post->set('section', $section->get('alias'));
$post->set('category', $category->get('alias'));

if ($post->get('id')) {
    $action = $base
        . '&scope='
        . $section->get('alias')
        . '/'
        . $category->get('alias')
        . '/'
        . $post->get('thread');
} else {
    $action = $base . '&scope=' . $section->get('alias') . '/' . $category->get('alias');
    $post->set('access', 0);
}

$allDiscussionsUrl = Route::url(
    $base . '&scope='
    . $section->get('alias') . '/'
    . $category->get('alias')
);

$accessPlugin = $config->get('access-plugin');
$showAside = ($accessPlugin == 'anyone' || $accessPlugin == 'registered');
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-neutral gap-2"
            href="{{ $allDiscussionsUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
            {{ Lang::txt('PLG_GROUPS_FORUM_ALL_DISCUSSIONS') }}
        </a>
    </li>
</ul>

<section class="main section">
    @if ($showAside)
    <div class="subject">
    @endif

        <h3 class="post-comment-title text-lg font-bold">
            @if ($post->get('id'))
                {{ Lang::txt('PLG_GROUPS_FORUM_EDIT_DISCUSSION') }}
            @else
                {{ Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION') }}
            @endif
        </h3>

        <form action="{{ Route::url($action) }}" method="post" id="commentform" enctype="multipart/form-data">
            <p class="comment-member-photo">
                <img class="rounded-full" src="{{ $post->creator->picture() }}" alt="" />
            </p>

            <fieldset>
            @if ($config->get('access-edit-thread') && !$post->get('parent'))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="field-sticky" class="label cursor-pointer justify-start gap-2">
                            <input class="checkbox"
                                type="checkbox"
                                name="fields[sticky]"
                                id="field-sticky"
                                value="1"
                                @if ($post->get('sticky')) checked="checked" @endif />
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_STICKY') }}</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="field-closed" class="label cursor-pointer justify-start gap-2">
                            <input class="checkbox"
                                type="checkbox"
                                name="fields[closed]"
                                id="field-closed"
                                value="1"
                                @if ($post->get('closed')) checked="checked" @endif />
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_CLOSED_THREAD') }}</span>
                        </label>
                    </div>
                </div>
            @else
                <input type="hidden" name="fields[sticky]" id="field-sticky" value="{{ e($post->get('sticky')) }}" />
                <input type="hidden" name="fields[closed]" id="field-closed" value="{{ e($post->get('closed')) }}" />
            @endif

            @if (!$post->get('parent'))
                @if ($accessPlugin == 'anyone' || $accessPlugin == 'registered')
                    <div class="form-group">
                        <label for="field-access" class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS') }}</span>
                        </label>
                        @php $access = $post->get('access'); @endphp
                        <select name="fields[access]" id="field-access" class="select select-bordered w-full">
                            <option value="1" @if ($access == 1) selected="selected" @endif>
                                {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC') }}
                            </option>
                            <option value="2" @if ($access == 2) selected="selected" @endif>
                                {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED') }}
                            </option>
                            <option value="5" @if ($access == 5) selected="selected" @endif>
                                {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PRIVATE') }}
                            </option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="fields[access]" id="field-access" value="{{ $post->get('access', 0) }}" />
                @endif

                <div class="form-group">
                    <label for="field-category_id" class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_CATEGORY') }}
                            <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_GROUPS_FORUM_REQUIRED') }}</span>
                        </span>
                    </label>
                    <select name="fields[category_id]" id="field-category_id" class="select select-bordered w-full">
                        <option value="0">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_CATEGORY_SELECT') }}</option>
                        @php
                        $catFilters = [
                            'state'  => 1,
                            'access' => User::getAuthorisedViewLevels(),
                        ];
                        if (in_array(User::get('id'), $group->get('members'))) {
                            $catFilters['access'][] = 5;
                        }
                        @endphp
                        @foreach ($forum->sections($catFilters)->rows() as $forumSection)
                            @php
                            $sectionCats = $forumSection->categories()
                                ->whereEquals('state', $catFilters['state'])
                                ->whereIn('access', $catFilters['access'])
                                ->rows();
                            @endphp
                            @if ($sectionCats->count() > 0)
                                <optgroup label="{{ e(stripslashes($forumSection->get('title'))) }}">
                                    @foreach ($sectionCats as $catOption)
                                        <option value="{{ $catOption->get('id') }}"
                                            @if ($category->get('alias') == $catOption->get('alias')) selected="selected" @endif>
                                            {{ e(stripslashes($catOption->get('title'))) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="field-title" class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE') }}
                            <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_GROUPS_FORUM_REQUIRED') }}</span>
                        </span>
                    </label>
                    <input type="text"
                        class="input input-bordered w-full"
                        name="fields[title]"
                        id="field-title"
                        value="{{ e(stripslashes($post->get('title', ''))) }}" />
                </div>
            @else
                <input type="hidden" name="fields[category_id]" id="field-category_id" value="{{ e($post->get('category_id')) }}" />
                <input type="hidden" name="fields[access]" id="field-access" value="{{ $post->get('access', 0) }}" />
            @endif

                <div class="form-group">
                    <label for="field_comment" class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_COMMENTS') }}
                            <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_GROUPS_FORUM_REQUIRED') }}</span>
                            <span class="text-sm text-base-content/70 float-right">
                                Use an @ sign to mention group users in the post
                            </span>
                        </span>
                    </label>
                    @php
                    $gid = $group->get('gidNumber');
                    $feedUrl = '/api/members/mentions/group?gid=' . $gid . '&search={encodedQuery}';
                    $itemTpl = '<li data-id="{id}">'
                        . '<img class="photo" src="{picture}" />'
                        . '<strong class="username">{username}</strong>'
                        . '<span class="fullname">{name}</span></li>';
                    $outputTpl = '<a href="/members/{id}"'
                        . ' data-user-id="{id}" target="_blank">'
                        . '@{username}</a>&nbsp;&nbsp;';

                    echo $__view->editor(
                        'fields[comment]',
                        e(stripslashes($post->get('comment', ''))),
                        35,
                        15,
                        'field_comment',
                        [
                            'class' => 'minimal no-footer',
                            'mentions' => [
                                [
                                    'minChars' => 0,
                                    'feed' => $feedUrl,
                                    'itemTemplate' => $itemTpl,
                                    'outputTemplate' => $outputTpl,
                                ]
                            ]
                        ]
                    );
                    @endphp
                </div>

                <div class="form-group">
                    <label for="actags" class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_TAGS') }}:</span>
                    </label>
                    @php
                    echo $__view->autocompleter(
                        'tags',
                        'tags',
                        e($post->tags('string')),
                        'actags'
                    );
                    @endphp
                </div>

                <fieldset>
                    <legend>{{ Lang::txt('PLG_GROUPS_FORUM_LEGEND_ATTACHMENTS') }}</legend>

                    @php $attachment = $post->attachments()->row(); @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="upload" class="label">
                                <span class="label-text">
                                    {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE') }}:
                                    @if ($attachment->get('filename'))
                                        <strong>{{ e(stripslashes($attachment->get('filename'))) }}</strong>
                                    @endif
                                </span>
                            </label>
                            <input type="file"
                                class="file-input file-input-bordered w-full"
                                name="upload"
                                id="upload" />
                        </div>
                        <div class="form-group">
                            <label for="field-attach-descritpion" class="label">
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_DESCRIPTION') }}:</span>
                            </label>
                            <input type="text"
                                class="input input-bordered w-full"
                                name="description"
                                id="field-attach-descritpion"
                                value="{{ e(stripslashes($attachment->get('description', ''))) }}" />
                        </div>
                        <input type="hidden"
                            name="attachment"
                            value="{{ e(stripslashes($attachment->get('id', ''))) }}" />
                    </div>
                    @if ($attachment->get('id'))
                        <div class="alert alert-warning mt-2">
                            <p>{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE_WARNING') }}</p>
                        </div>
                    @endif
                </fieldset>

                @if ($post->get('id'))
                    <div class="form-group">
                        <label for="field-notify" class="label cursor-pointer justify-start gap-2">
                            <input class="checkbox"
                                type="checkbox"
                                name="notify"
                                id="field-notify"
                                value="1"
                                checked="checked" />
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_NOTIFY') }}</span>
                        </label>
                    </div>
                @else
                    <input class="option" type="hidden" name="notify" id="field-notify" value="1" />
                @endif

                @if ($config->get('allow_anonymous'))
                    <div class="form-group">
                        <label for="field-anonymous" class="label cursor-pointer justify-start gap-2">
                            <input class="checkbox"
                                type="checkbox"
                                name="fields[anonymous]"
                                id="field-anonymous"
                                value="1"
                                @if ($post->get('anonymous')) checked="checked" @endif />
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_ANONYMOUS') }}</span>
                        </label>
                    </div>
                @endif

                <p class="submit mt-4">
                    <input class="btn btn-success"
                        type="submit"
                        value="{{ Lang::txt('PLG_GROUPS_FORUM_SUBMIT') }}" />
                </p>

                <div class="alert alert-info mt-4">
                    <p><strong>{{ Lang::txt('PLG_GROUPS_FORUM_KEEP_POLITE') }}</strong></p>
                </div>
            </fieldset>
            <input type="hidden" name="fields[parent]" value="{{ e($post->get('parent')) }}" />
            <input type="hidden" name="fields[state]" value="1" />
            <input type="hidden" name="fields[id]" value="{{ e($post->get('id')) }}" />
            <input type="hidden" name="fields[scope]" value="{{ e($forum->get('scope')) }}" />
            <input type="hidden" name="fields[scope_id]" value="{{ e($forum->get('scope_id')) }}" />
            <input type="hidden" name="fields[thread]" value="{{ e($post->get('thread')) }}" />

            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
            <input type="hidden" name="active" value="forum" />
            <input type="hidden" name="action" value="savethread" />
            <input type="hidden" name="section" value="{{ e($section->get('alias')) }}" />

            {!! Html::input('token') !!}
        </form>
    @if ($showAside)
    </div>
    <aside class="aside">
        <p>{{ Lang::txt('PLG_GROUPS_FORUM_EDIT_HINT') }}</p>
    </aside>
    @endif
</section>
