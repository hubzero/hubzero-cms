@php
$item = $post->item();
$base = 'index.php?option=' . $option . '&controller=' . $controller;
$no_html = Request::getInt('no_html', 0);

$itemCreatorName = e(stripslashes($item->creator()->get('name')));
$itemCreatorAccess = in_array($item->creator()->get('access'), User::getAuthorisedViewLevels());
$itemCreatorLink = Route::url($item->creator()->link(), false);
$itemCreatorPic = $item->creator()->picture();

$postCreatorName = e(stripslashes($post->creator()->get('name')));
$postCreatorAccess = in_array($post->creator()->get('access'), User::getAuthorisedViewLevels());
$postCreatorLink = Route::url($post->creator()->link(), false);
$postCollectionsLink = Route::url($post->creator()->link() . '&active=collections', false);
$postCreatorPic = $post->creator()->picture();

$collectionLink = Route::url($collection->link(), false);
$collectionTitle = e(stripslashes($collection->get('title')));

$collCreatorName = e(stripslashes($collection->creator('name')));
$collCreatorAccess = in_array($collection->creator()->get('access'), User::getAuthorisedViewLevels());
$collCreatorLink = Route::url($collection->creator()->link() . '&active=collections', false);
$collCreatorPic = $collection->creator()->picture();

$isCollOwner = ($collection->get('object_type') == 'member'
    && $collection->get('object_id') == User::get('id'));

$assets = $item->assets();
$images = [];
$files = [];
if ($assets->total() > 0) {
    foreach ($assets as $asset) {
        if ($asset->image()) {
            $images[] = $asset;
        } else {
            $files[] = $asset;
        }
    }
}

$content = $post->description('parsed') ?: $item->description('parsed');
$tags = $item->tags();
$allowComments = $config->get('allow_comments');
@endphp

@if (!$no_html)
<x-page-container :title="Lang::txt('COM_COLLECTIONS')">
@endif

    <div class="lg:grid lg:grid-cols-[1fr_320px] lg:gap-8">
        {{-- Main post content --}}
        <div class="min-w-0">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">

                    {{-- Item creator attribution --}}
                    @if ($item->get('type') == 'file' || $item->get('type') == 'collection')
                        <div class="flex items-center gap-3 mb-4">
                            @if ($itemCreatorAccess)
                                <a href="{{ $itemCreatorLink }}">
                                    <div class="avatar">
                                        <div class="w-10 rounded-full">
                                            <img src="{{ $itemCreatorPic }}" alt="" />
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="avatar">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ $itemCreatorPic }}" alt="" />
                                    </div>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm">
                                    @if ($itemCreatorAccess)
                                        <a class="link link-hover font-medium"
                                           href="{{ $itemCreatorLink }}">{{ $itemCreatorName }}</a>
                                    @else
                                        <span class="font-medium">{{ $itemCreatorName }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-base-content/60">
                                    <time datetime="{{ $item->created() }}">
                                        {{ $item->created('date') }}
                                        {{ Lang::txt('COM_COLLECTIONS_AT') }}
                                        {{ $item->created('time') }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-base-content/60 mb-4">
                            {{ e($item->type('title')) }}
                        </p>
                    @endif

                    {{-- Post title --}}
                    @if ($item->get('title'))
                        <h3 class="text-xl font-bold mb-3">
                            {{ e(stripslashes($item->get('title'))) }}
                        </h3>
                    @endif

                    {{-- Images --}}
                    @if (count($images) > 0)
                        @php $first = array_shift($images); @endphp
                        <figure class="mb-4">
                            <a href="{{ $first->link('medium') }}" class="block">
                                <img src="{{ $first->link('thumb') }}"
                                     alt="{{ $first->get('description') ? e(stripslashes($first->get('description'))) : Lang::txt('COM_COLLECTIONS_IMAGE_ALT', ltrim($first->get('filename'), DS)) }}"
                                     class="rounded-lg max-w-full h-auto" />
                            </a>
                        </figure>
                        @if (count($images) > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach ($images as $img)
                                    <a href="{{ $img->link('medium') }}">
                                        <img src="{{ $img->link('thumb') }}"
                                             alt="{{ $img->get('description') ? e(stripslashes($img->get('description'))) : Lang::txt('COM_COLLECTIONS_IMAGE_ALT', ltrim($img->get('filename'), DS)) }}"
                                             class="w-16 h-16 rounded object-cover" />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    {{-- Files --}}
                    @if (count($files) > 0)
                        <ul class="menu bg-base-200 rounded-box mb-4 w-full">
                            @foreach ($files as $file)
                                @php
                                $fileHref = $file->isLink()
                                    ? $file->get('filename')
                                    : $file->link('original');
                                @endphp
                                <li>
                                    <a href="{{ $fileHref }}"
                                       @if ($file->isLink()) rel="external nofollow noreferrer" @endif>
                                        {{ $file->get('filename') }}
                                        @if (!$file->isLink() && $file->exists())
                                            <span class="badge badge-ghost badge-sm">
                                                {{ \Hubzero\Utility\Number::formatBytes($file->size()) }}
                                            </span>
                                        @elseif ($file->isLink())
                                            <span class="badge badge-ghost badge-sm">
                                                {{ Lang::txt('COM_COLLECTIONS_ASSET_TYPE_LINK') }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Link type --}}
                    @if ($item->get('type') == 'link' && $item->get('url'))
                        <p class="mb-4">
                            <a class="link link-primary"
                               href="{{ stripslashes($item->get('url')) }}"
                               rel="external nofollow noreferrer">
                                {{ e(stripslashes($item->get('title', $item->get('url')))) }}
                            </a>
                        </p>
                    @endif

                    {{-- Description --}}
                    @if ($content)
                        <div class="prose max-w-none mb-4">
                            {!! $content !!}
                        </div>
                    @endif

                    {{-- Deleted item --}}
                    @if ($item->get('type') == 'deleted')
                        <div class="alert mb-4">
                            <span>{{ Lang::txt('COM_COLLECTIONS_ITEM_DELETED') }}</span>
                        </div>
                    @endif

                    {{-- Tags --}}
                    @if (count($tags) > 0)
                        <div class="flex flex-wrap gap-1 mb-4">
                            @foreach ($tags as $tag)
                                <a class="badge badge-outline"
                                   href="{{ Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'), false) }}">
                                    {{ e(stripslashes($tag->get('raw_tag'))) }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Stats --}}
                    <div class="flex items-center gap-4 text-sm text-base-content/60 py-3 border-t border-base-300">
                        <span>{{ Lang::txt('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)) }}</span>
                        @if ($allowComments)
                            <span>{{ Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)) }}</span>
                        @endif
                        <span>{{ Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)) }}</span>
                    </div>

                    {{-- Post attribution (who posted it to which collection) --}}
                    <div class="flex items-center gap-3 pt-3 border-t border-base-300">
                        @if ($postCreatorAccess)
                            <a href="{{ $postCreatorLink }}">
                                <div class="avatar">
                                    <div class="w-8 rounded-full">
                                        <img src="{{ $postCreatorPic }}" alt="" />
                                    </div>
                                </div>
                            </a>
                        @else
                            <div class="avatar">
                                <div class="w-8 rounded-full">
                                    <img src="{{ $postCreatorPic }}" alt="" />
                                </div>
                            </div>
                        @endif
                        <div class="text-sm">
                            <p>
                                @if ($postCreatorAccess)
                                    <a class="link link-hover font-medium"
                                       href="{{ $postCollectionsLink }}">{{ $postCreatorName }}</a>
                                @else
                                    <span class="font-medium">{{ $postCreatorName }}</span>
                                @endif
                                {{ Lang::txt('COM_COLLECTIONS_ONTO', '', '') }}
                                <a class="link link-hover"
                                   href="{{ $collectionLink }}">{{ $collectionTitle }}</a>
                            </p>
                            <p class="text-xs text-base-content/60">
                                <time datetime="{{ $post->created() }}">
                                    {{ $post->created('date') }}
                                    {{ Lang::txt('COM_COLLECTIONS_AT') }}
                                    {{ $post->created('time') }}
                                </time>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comments section --}}
            @if ($allowComments)
                <div class="mt-6">
                    <h3 class="text-lg font-bold mb-4">
                        {{ Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)) }}
                    </h3>

                    @if ($item->get('comments'))
                        <div class="space-y-4">
                            @foreach ($item->comments() as $comment)
                                @php
                                $cuser = \Components\Members\Models\Member::oneOrNew($comment->created_by);
                                $cname = Lang::txt('JANONYMOUS');
                                $cuserAccess = false;
                                if (!$comment->anonymous) {
                                    $cname = e(stripslashes($cuser->get('name')));
                                    $cuserAccess = in_array(
                                        $cuser->get('access'),
                                        User::getAuthorisedViewLevels()
                                    );
                                }
                                $cuserPic = $cuser->picture($comment->anonymous);
                                $commentTime = Date::of($comment->created)->toLocal(
                                    Lang::txt('TIME_FORMAT_HZ1')
                                );
                                $commentDate = Date::of($comment->created)->toLocal(
                                    Lang::txt('DATE_FORMAT_HZ1')
                                );
                                @endphp
                                <div class="card bg-base-100 shadow-sm"
                                     id="c{{ $comment->id }}">
                                    <div class="card-body p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="avatar">
                                                <div class="w-8 rounded-full">
                                                    <img src="{{ $cuserPic }}" alt="" />
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    @if ($cuserAccess)
                                                        <a class="link link-hover font-medium text-sm"
                                                           href="{{ Route::url($cuser->link(), false) }}">
                                                            {{ $cname }}
                                                        </a>
                                                    @else
                                                        <span class="font-medium text-sm">{{ $cname }}</span>
                                                    @endif
                                                    <span class="text-xs text-base-content/60">
                                                        {{ $commentDate }} {{ $commentTime }}
                                                    </span>
                                                </div>
                                                <div class="prose prose-sm max-w-none">
                                                    <p>{{ stripslashes($comment->content) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Comment form --}}
                    @if (!User::isGuest())
                        @php
                        $commentFormUrl = Route::url(
                            $base . '&post=' . $post->get('id')
                            . '&task=savecomment'
                            . ($no_html ? '&no_html=' . $no_html : ''),
                            false
                        );
                        @endphp
                        <div class="card bg-base-100 shadow-sm mt-4">
                            <div class="card-body p-4">
                                <form action="{{ $commentFormUrl }}"
                                      method="post"
                                      id="commentform">
                                    <div class="flex items-start gap-3">
                                        <div class="avatar">
                                            <div class="w-8 rounded-full">
                                                <img src="{{ User::picture(0) }}" alt="" />
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <label class="label" for="comment-content">
                                                <span class="label-text">
                                                    {{ Lang::txt('COM_COLLECTIONS_FIELD_COMMENTS') }}
                                                </span>
                                            </label>
                                            <textarea class="textarea textarea-bordered w-full"
                                                      id="comment-content"
                                                      name="comment[content]"
                                                      rows="3"></textarea>

                                            <div class="flex items-center justify-between mt-3">
                                                <label class="label cursor-pointer gap-2">
                                                    <input type="checkbox"
                                                           class="checkbox checkbox-sm"
                                                           name="comment[anonymous]"
                                                           value="1" />
                                                    <span class="label-text">
                                                        {{ Lang::txt('COM_COLLECTIONS_FIELD_ANONYMOUS') }}
                                                    </span>
                                                </label>
                                                <button class="btn btn-primary btn-sm" type="submit">
                                                    {{ Lang::txt('COM_COLLECTIONS_SAVE') }}
                                                </button>
                                            </div>

                                            <input type="hidden" name="comment[id]" value="0" />
                                            <input type="hidden" name="comment[item_id]"
                                                   value="{{ $item->get('id') }}" />
                                            <input type="hidden" name="comment[item_type]"
                                                   value="collection" />
                                            <input type="hidden" name="comment[state]" value="1" />
                                            <input type="hidden" name="option" value="{{ $option }}" />
                                            <input type="hidden" name="controller"
                                                   value="{{ $controller }}" />
                                            <input type="hidden" name="post"
                                                   value="{{ $post->get('id') }}" />
                                            <input type="hidden" name="task" value="savecomment" />
                                            <input type="hidden" name="no_html"
                                                   value="{{ $no_html }}" />
                                            {!! Html::input('token') !!}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Sidebar: collection info --}}
        <aside class="mt-6 lg:mt-0">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">
                        @if ($collection->get('access', 0) == 4)
                            <span class="badge badge-ghost badge-sm">
                                {{ Lang::txt('COM_COLLECTIONS_FIELD_PRIVACY') }}
                            </span>
                        @endif
                        <a class="link link-hover" href="{{ $collectionLink }}">
                            {{ $collectionTitle }}
                        </a>
                    </h3>

                    @if ($collection->description('parsed'))
                        <div class="prose prose-sm max-w-none mt-2">
                            {!! $collection->description('parsed') !!}
                        </div>
                    @endif

                    <div class="flex items-center gap-4 text-sm text-base-content/60 mt-3">
                        <span>{{ Lang::txt('COM_COLLECTIONS_NUM_LIKES', $collection->get('positive', 0)) }}</span>
                        <span>{{ Lang::txt('COM_COLLECTIONS_NUM_POSTS', $collection->count('post')) }}</span>
                    </div>

                    {{-- Collection creator --}}
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-base-300">
                        @if ($collCreatorAccess)
                            <a href="{{ $collCreatorLink }}">
                                <div class="avatar">
                                    <div class="w-6 rounded-full">
                                        <img src="{{ $collCreatorPic }}" alt="" />
                                    </div>
                                </div>
                            </a>
                            <a class="link link-hover text-sm"
                               href="{{ $collCreatorLink }}">{{ $collCreatorName }}</a>
                        @else
                            <div class="avatar">
                                <div class="w-6 rounded-full">
                                    <img src="{{ $collCreatorPic }}" alt="" />
                                </div>
                            </div>
                            <span class="text-sm">{{ $collCreatorName }}</span>
                        @endif
                    </div>

                    {{-- Collection actions --}}
                    @if (!$no_html)
                        <div class="card-actions mt-3 pt-3 border-t border-base-300">
                            @if (!User::isGuest())
                                @if ($isCollOwner)
                                    <a class="btn btn-ghost btn-xs"
                                       href="{{ Route::url($collection->link() . '/edit', false) }}">
                                        {{ Lang::txt('JACTION_EDIT') }}
                                    </a>
                                    <a class="btn btn-ghost btn-xs text-error"
                                       href="{{ Route::url($collection->link() . '/delete', false) }}">
                                        {{ Lang::txt('JACTION_DELETE') }}
                                    </a>
                                @else
                                    <a class="btn btn-ghost btn-xs"
                                       href="{{ Route::url($base . '&controller=posts&board=' . $collection->get('id') . '&task=collect', false) }}">
                                        {{ Lang::txt('COM_COLLECTIONS_COLLECT') }}
                                    </a>
                                    @if ($collection->isFollowing())
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($collection->link() . '/unfollow', false) }}">
                                            {{ Lang::txt('COM_COLLECTIONS_UNFOLLOW') }}
                                        </a>
                                    @else
                                        <a class="btn btn-ghost btn-xs"
                                           href="{{ Route::url($collection->link() . '/follow', false) }}">
                                            {{ Lang::txt('COM_COLLECTIONS_FOLLOW') }}
                                        </a>
                                    @endif
                                @endif
                            @else
                                @php
                                $loginReturn = base64_encode(
                                    Route::url($collection->link(), false, true)
                                );
                                $loginUrl = Route::url(
                                    'index.php?option=com_users&view=login&return=' . $loginReturn,
                                    false
                                );
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
                    @endif
                </div>
            </div>

            {{-- Also in these collections --}}
            @php
            $otherCollections = $item->collections(
                'list',
                ['collection_id' => $collection->get('id')]
            );
            @endphp
            @if ($otherCollections->total())
                <h4 class="text-sm font-bold mt-6 mb-3">
                    {{ Lang::txt('COM_COLLECTIONS_ALSO_IN_THESE_COLLECTIONS') }}
                </h4>
                <div class="space-y-3">
                    @foreach ($item->collections() as $otherColl)
                        @php
                        $ocTitle = e(stripslashes(
                            $otherColl->get('title', Lang::txt('COM_COLLECTIONS_NONE'))
                        ));
                        $ocLink = Route::url($otherColl->link(), false);
                        $ocCreatorName = e(stripslashes($otherColl->creator()->get('name')));
                        $ocCreatorAccess = in_array(
                            $otherColl->creator()->get('access'),
                            User::getAuthorisedViewLevels()
                        );
                        $ocCreatorLink = Route::url(
                            $otherColl->creator()->link() . '&active=collections',
                            false
                        );
                        @endphp
                        <div class="card bg-base-100 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="font-medium text-sm">
                                    <a class="link link-hover" href="{{ $ocLink }}">
                                        {{ $ocTitle }}
                                    </a>
                                </h5>
                                <div class="flex items-center gap-3 text-xs text-base-content/60 mt-1">
                                    <span>{{ Lang::txt('COM_COLLECTIONS_NUM_POSTS', $otherColl->count('posts')) }}</span>
                                    <span>
                                        @if ($ocCreatorAccess)
                                            <a class="link link-hover"
                                               href="{{ $ocCreatorLink }}">{{ $ocCreatorName }}</a>
                                        @else
                                            {{ $ocCreatorName }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </aside>
    </div>

@if (!$no_html)
</x-page-container>
@endif
