@php
$base = 'index.php?option=' . $option;
@endphp

<x-page-container :title="Lang::txt('COM_COLLECTIONS')">
    @slot('tabs')
        @php
        $tabOptions = [
            Route::url($base . '&task=posts', false) => '<span class="badge badge-sm">' . $posts . '</span> posts',
            Route::url($base . '&task=all', false) => '<span class="badge badge-sm">' . $total . '</span> collections',
            Route::url($base . '&task=about', false) => Lang::txt('COM_COLLECTIONS_GETTING_STARTED'),
        ];
        $activeTab = Route::url($base . '&task=all', false);
        @endphp
        <x-filter-tabs :options="$tabOptions" :active="$activeTab" />
    @endslot

    <x-search-bar
        :action="Route::url($base . '&controller=' . $controller . '&task=' . $task, false)"
        :query="$filters['search'] ?? ''"
        :placeholder="Lang::txt('COM_COLLECTIONS_SEARCH_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_COLLECTIONS_SEARCH_LABEL')"
        :clearUrl="($filters['search'] ?? '') ? Route::url($base . '&task=all', false) : ''"
    />

    @if ($rows->total() > 0)
        @if (!User::isGuest())
            @php
            $newCollUrl = Route::url(
                'index.php?option=com_members&id=' . User::get('id')
                . '&active=collections&task=new',
                false
            );
            @endphp
            <div class="flex justify-end mb-4">
                <a class="btn btn-primary btn-sm" href="{{ $newCollUrl }}">
                    {{ Lang::txt('COM_COLLECTIONS_NEW_COLLECTION') }}
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($rows as $row)
                @php
                $collUrl = Route::url($row->link(), false);
                $creatorName = e(stripslashes($row->creator()->get('name')));
                $creatorAccess = in_array($row->creator()->get('access'), User::getAuthorisedViewLevels());
                $creatorLink = Route::url($row->creator()->link() . '&active=collections', false);
                $creatorPic = $row->creator()->picture();
                $isOwner = ($row->get('object_type') == 'member' && $row->get('object_id') == User::get('id'));
                @endphp
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-base">
                            @if ($row->get('access', 0) == 4)
                                <span class="badge badge-ghost badge-sm">{{ Lang::txt('COM_COLLECTIONS_FIELD_PRIVACY') }}</span>
                            @endif
                            <a class="link link-hover" href="{{ $collUrl }}">
                                {{ e(stripslashes($row->get('title', Lang::txt('COM_COLLECTIONS_NONE')))) }}
                            </a>
                        </h3>

                        @if ($row->description('parsed'))
                            <div class="prose prose-sm max-w-none line-clamp-3">
                                {!! $row->description('parsed') !!}
                            </div>
                        @endif

                        <div class="flex items-center gap-4 text-sm text-base-content/60 mt-2">
                            <span>{{ Lang::txt('COM_COLLECTIONS_NUM_LIKES', $row->count('likes')) }}</span>
                            <span>{{ Lang::txt('COM_COLLECTIONS_NUM_POSTS', $row->count('posts')) }}</span>
                        </div>

                        {{-- Attribution --}}
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-base-300">
                            @if ($creatorAccess)
                                <a href="{{ $creatorLink }}">
                                    <div class="avatar">
                                        <div class="w-6 rounded-full">
                                            <img src="{{ $creatorPic }}" alt="" />
                                        </div>
                                    </div>
                                </a>
                                <a class="link link-hover text-sm" href="{{ $creatorLink }}">{{ $creatorName }}</a>
                            @else
                                <div class="avatar">
                                    <div class="w-6 rounded-full">
                                        <img src="{{ $creatorPic }}" alt="" />
                                    </div>
                                </div>
                                <span class="text-sm">{{ $creatorName }}</span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="card-actions justify-end mt-2">
                            @if (!User::isGuest())
                                @if ($isOwner)
                                    <a class="btn btn-ghost btn-xs"
                                       href="{{ Route::url($row->link() . '/edit', false) }}">
                                        {{ Lang::txt('JACTION_EDIT') }}
                                    </a>
                                    <a class="btn btn-ghost btn-xs text-error"
                                       href="{{ Route::url($row->link() . '/delete', false) }}">
                                        {{ Lang::txt('JACTION_DELETE') }}
                                    </a>
                                @else
                                    <a class="btn btn-ghost btn-xs"
                                       href="{{ Route::url($base . '&controller=posts&board=' . $row->get('id') . '&task=collect', false) }}">
                                        {{ Lang::txt('COM_COLLECTIONS_COLLECT') }}
                                    </a>
                                    @if ($row->isFollowing())
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($row->link() . '/unfollow', false) }}">
                                            {{ Lang::txt('COM_COLLECTIONS_UNFOLLOW') }}
                                        </a>
                                    @else
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($row->link() . '/follow', false) }}">
                                            {{ Lang::txt('COM_COLLECTIONS_FOLLOW') }}
                                        </a>
                                    @endif
                                @endif
                            @else
                                @php
                                $loginReturn = base64_encode(Route::url($row->link(), false, true));
                                $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $loginReturn, false);
                                @endphp
                                <a class="btn btn-ghost btn-xs" href="{{ $loginUrl }}"
                                   title="{{ Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') }}">
                                    {{ Lang::txt('COM_COLLECTIONS_COLLECT') }}
                                </a>
                                <a class="btn btn-ghost btn-xs" href="{{ $loginUrl }}"
                                   title="{{ Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW') }}">
                                    {{ Lang::txt('COM_COLLECTIONS_FOLLOW') }}
                                </a>
                            @endif
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
            :title="Lang::txt('COM_COLLECTIONS_NO_COLLECTIONS_FOUND')"
        >
            @if (!User::isGuest())
                @php
                $newCollUrl = Route::url(
                    'index.php?option=com_members&id=' . User::get('id')
                    . '&active=collections&task=new',
                    false
                );
                @endphp
                <a class="btn btn-primary" href="{{ $newCollUrl }}">
                    {{ Lang::txt('COM_COLLECTIONS_NEW_COLLECTION') }}
                </a>
            @endif
        </x-empty-state>
    @endif
</x-page-container>
