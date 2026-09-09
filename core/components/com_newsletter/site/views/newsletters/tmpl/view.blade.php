{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $option = $__view->get('option', 'com_newsletter');
    $id = $__view->get('id', 0);
    $newsletter = $__view->get('newsletter', '');
    $newsletters = $__view->get('newsletters', []);
    $title = $__view->get('title', '');
@endphp

<x-page-container :title="Lang::txt(strtoupper($option))">
    @slot('actions')
        @if($id)
            <a class="btn btn-sm"
               href="{{ Route::url('index.php?option=com_newsletter&id=' . $id . '&task=output') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                {{ Lang::txt('COM_NEWSLETTER_VIEW_SAVEASPDF') }}
            </a>
        @endif
        <a class="btn btn-primary btn-sm"
           href="{{ Route::url('index.php?option=com_newsletter&task=subscribe') }}">
            {{ Lang::txt('COM_NEWSLETTER_VIEW_SUBSCRIBE_TO_MAILINGLISTS') }}
        </a>
    @endslot

    @slot('sidebar')
        <x-sidebar-card :title="Lang::txt('COM_NEWSLETTER_VIEW_PAST_NEWSLETTERS')" class="mt-10">
            <ul class="menu menu-sm">
                @foreach($newsletters as $nl)
                    @if($nl->published)
                        <li>
                            <a href="{{ Route::url('index.php?option=com_newsletter&id=' . $nl->id) }}"
                               @class(['active' => $id == $nl->id])>
                                {{ $nl->name }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </x-sidebar-card>

        <x-sidebar-card :title="Lang::txt('COM_NEWSLETTER_VIEW_NEWSLETTER_HELP')">
            <ul class="menu menu-sm">
                <li>
                    <a href="{{ Route::url('index.php?option=com_help&component=newsletter&page=index') }}"
                       target="_blank">
                        {{ Lang::txt('COM_NEWSLETTER_VIEW_NEWSLETTER_HELP') }}
                    </a>
                </li>
            </ul>
        </x-sidebar-card>
    @endslot

    <h3 class="text-lg font-semibold mb-4">{{ e($title) }}</h3>

    @if($newsletter != '')
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-0">
                <iframe
                    id="newsletter-iframe"
                    width="100%"
                    height="0"
                    title="{{ e($title) }}"
                    src="{{ Route::url('index.php?option=com_newsletter&id=' . $id . '&no_html=1') }}"
                    class="rounded-box"
                    data-auto-resize
                ></iframe>
            </div>
        </div>
    @else
        <x-empty-state>
            {{ Lang::txt('COM_NEWSLETTER_VIEW_NO_NEWSLETTERS') }}
        </x-empty-state>
    @endif
</x-page-container>
