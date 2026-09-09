@php
$base = 'index.php?option=' . $option;
@endphp

<x-page-container :title="Lang::txt('COM_COLLECTIONS')">
    @slot('tabs')
        @php
        $tabOptions = [
            Route::url($base . '&task=posts', false) => '<span class="badge badge-sm">' . $total . '</span> posts',
            Route::url($base . '&task=all', false) => '<span class="badge badge-sm">' . $collections . '</span> collections',
            Route::url($base . '&task=about', false) => Lang::txt('COM_COLLECTIONS_GETTING_STARTED'),
        ];
        $activeTab = Route::url($base . '&task=posts', false);
        @endphp
        <x-filter-tabs :options="$tabOptions" :active="$activeTab" />
    @endslot

    <x-search-bar
        :action="Route::url($base . '&controller=' . $controller . '&task=' . $task, false)"
        :query="$filters['search'] ?? ''"
        :placeholder="Lang::txt('COM_COLLECTIONS_SEARCH_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_COLLECTIONS_SEARCH_LABEL')"
        :clearUrl="($filters['search'] ?? '') ? Route::url($base . '&task=posts', false) : ''"
    />

    @if ($rows->total() > 0)
        <div class="space-y-4">
            @if (!User::isGuest())
                @php
                $newPostUrl = Route::url(
                    'index.php?option=com_members&id=' . User::get('id')
                    . '&active=collections&task=post/new',
                    false
                );
                @endphp
                <div class="flex justify-end">
                    <a class="btn btn-primary btn-sm" href="{{ $newPostUrl }}">
                        {{ Lang::txt('COM_COLLECTIONS_NEW_POST') }}
                    </a>
                </div>
            @endif

            @foreach ($rows as $row)
                @php
                $item = $row->item();
                $postUrl = Route::url($base . '&controller=posts&post=' . $row->get('id'), false);
                $creatorName = e(stripslashes($row->creator()->get('name')));
                $creatorAccess = in_array($row->creator()->get('access'), User::getAuthorisedViewLevels());
                $creatorLink = Route::url($row->creator()->link() . '&active=collections', false);
                $creatorPic = $row->creator()->picture();
                @endphp
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        {{-- Post content --}}
                        @if ($item->get('title'))
                            <h3 class="card-title text-base">
                                <a class="link link-hover" href="{{ $postUrl }}">
                                    {{ e(stripslashes($item->get('title'))) }}
                                </a>
                            </h3>
                        @endif

                        @if ($item->description('parsed'))
                            <div class="prose prose-sm max-w-none">
                                {!! $item->description('parsed') !!}
                            </div>
                        @endif

                        @if ($tags = $item->tags('cloud'))
                            <div class="flex flex-wrap gap-1 mt-2">
                                {!! $tags !!}
                            </div>
                        @endif

                        {{-- Stats --}}
                        <div class="flex items-center gap-4 text-sm text-base-content/60 mt-3">
                            <span>{{ Lang::txt('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)) }}</span>
                            @if ($config->get('allow_comments'))
                                <span>{{ Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)) }}</span>
                            @endif
                            <span>{{ Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)) }}</span>
                        </div>

                        {{-- Actions + Attribution --}}
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-base-300">
                            <div class="flex items-center gap-2">
                                @if ($creatorAccess)
                                    <a href="{{ $creatorLink }}">
                                        <div class="avatar">
                                            <div class="w-8 rounded-full">
                                                <img src="{{ $creatorPic }}" alt="" />
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="avatar">
                                        <div class="w-8 rounded-full">
                                            <img src="{{ $creatorPic }}" alt="" />
                                        </div>
                                    </div>
                                @endif
                                <div class="text-sm">
                                    @if ($creatorAccess)
                                        <a class="link link-hover font-medium" href="{{ $creatorLink }}">{{ $creatorName }}</a>
                                    @else
                                        <span class="font-medium">{{ $creatorName }}</span>
                                    @endif
                                    <time class="text-base-content/50 block text-xs"
                                          datetime="{{ $row->created() }}">
                                        {{ $row->created('date') }} {{ Lang::txt('COM_COLLECTIONS_AT') }} {{ $row->created('time') }}
                                    </time>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                @if (!User::isGuest())
                                    @if ($row->get('created_by') == User::get('id'))
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=edit', false) }}">
                                            {{ Lang::txt('JACTION_EDIT') }}
                                        </a>
                                    @else
                                        @php
                                        $voteUrl = Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=vote', false);
                                        $isLiked = $item->get('voted');
                                        @endphp
                                        <a class="btn btn-ghost btn-xs {{ $isLiked ? 'text-error' : '' }}"
                                           href="{{ $voteUrl }}">
                                            {{ $isLiked ? Lang::txt('COM_COLLECTIONS_UNLIKE') : Lang::txt('COM_COLLECTIONS_LIKE') }}
                                        </a>
                                    @endif
                                    @if ($config->get('allow_comments'))
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=comment', false) }}">
                                            {{ Lang::txt('COM_COLLECTIONS_COMMENT') }}
                                        </a>
                                    @endif
                                    <a class="btn btn-ghost btn-xs"
                                       href="{{ Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=collect', false) }}">
                                        {{ Lang::txt('COM_COLLECTIONS_COLLECT') }}
                                    </a>
                                @else
                                    @php
                                    $loginReturn = base64_encode(Route::url($base . '&controller=' . $controller . '&task=' . $task, false, true));
                                    $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $loginReturn, false);
                                    @endphp
                                    <a class="btn btn-ghost btn-xs" href="{{ $loginUrl }}"
                                       title="{{ Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_LIKE') }}">
                                        {{ Lang::txt('COM_COLLECTIONS_LIKE') }}
                                    </a>
                                    @if ($config->get('allow_comments'))
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($base . '&controller=posts&post=' . $row->get('id') . '&task=comment', false) }}">
                                            {{ Lang::txt('COM_COLLECTIONS_COMMENT') }}
                                        </a>
                                    @endif
                                    <a class="btn btn-ghost btn-xs" href="{{ $loginUrl }}"
                                       title="{{ Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') }}">
                                        {{ Lang::txt('COM_COLLECTIONS_COLLECT') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($total > $filters['limit'])
            {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
        @endif
    @else
        <x-empty-state
            :title="Lang::txt('COM_COLLECTIONS_NO_POSTS_FOUND')"
            :message="$config->get('access-create-post')
                ? Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP1')
                : ''"
        >
            @if (!User::isGuest())
                @php
                $newPostUrl = Route::url(
                    'index.php?option=com_members&id=' . User::get('id')
                    . '&active=collections&task=post/new',
                    false
                );
                @endphp
                <a class="btn btn-primary" href="{{ $newPostUrl }}">
                    {{ Lang::txt('COM_COLLECTIONS_NEW_POST') }}
                </a>
            @endif
        </x-empty-state>
    @endif
</x-page-container>
