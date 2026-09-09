@php
$base = 'index.php?option=' . $option;
$collectionsUrl = Route::url('index.php?option=com_members&task=myaccount/collections', false);
$myaccountUrl = Route::url('index.php?option=com_members&task=myaccount', false);
@endphp

<x-page-container :title="Lang::txt('COM_COLLECTIONS')">
    @slot('tabs')
        @php
        $tabOptions = [
            Route::url($base . '&task=posts', false) => '<span class="badge badge-sm">' . $total . '</span> posts',
            Route::url($base . '&task=all', false) => '<span class="badge badge-sm">' . $collections . '</span> collections',
            Route::url($base . '&task=about', false) => Lang::txt('COM_COLLECTIONS_GETTING_STARTED'),
        ];
        $activeTab = Route::url($base . '&task=about', false);
        @endphp
        <x-filter-tabs :options="$tabOptions" :active="$activeTab" />
    @endslot

    <div class="space-y-6">
        <p class="text-lg text-base-content/70">
            {{ Lang::txt('COM_COLLECTIONS_TAGLINE') }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_COLLECTIONS_POST') }}</h3>
                    <p>{{ Lang::txt('COM_COLLECTIONS_POST_EXPLANATION') }}</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_COLLECTIONS_COLLECTION') }}</h3>
                    <p>{{ Lang::txt('COM_COLLECTIONS_COLLECTION_EXPLANATION') }}</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_COLLECTIONS_FOLLOW') }}</h3>
                    <p>{!! Lang::txt('COM_COLLECTIONS_FOLLOW_EXPLANATION', $collectionsUrl, $myaccountUrl, $collectionsUrl) !!}</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_COLLECTIONS_UNFOLLOW') }}</h3>
                    <p>{!! Lang::txt('COM_COLLECTIONS_UNFOLLOW_EXPLANATION', $collectionsUrl) !!}</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm md:col-span-2">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_COLLECTIONS_LIVE_FEED') }}</h3>
                    <p>{!! Lang::txt('COM_COLLECTIONS_LIVE_FEED_EXPLANATION', $collectionsUrl) !!}</p>
                </div>
            </div>
        </div>
    </div>
</x-page-container>
