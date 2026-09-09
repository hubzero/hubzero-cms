{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $title = $__view->get('title', '');
    $option = $__view->get('option', 'com_newsletter');
    $controller = $__view->get('controller', 'mailinglists');
    $mylists = $__view->get('mylists', []);
    $alllists = $__view->get('alllists', []);
    $email = $__view->get('email', '');
    $mylistIds = [];
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm"
           href="{{ Route::url('index.php?option=com_newsletter') }}">
            {{ Lang::txt('COM_NEWSLETTER_BROWSE') }}
        </a>
    @endslot

    <div class="max-w-2xl mx-auto">
        <form action="{{ Route::url('index.php?option=' . $option) }}"
              method="post">

            @if(count($mylists) > 0)
                <x-form-section :heading="Lang::txt('COM_NEWSLETTER_MAILINGLISTS_MYLISTS')">
                    @foreach($mylists as $mylist)
                        @php $mylistIds[] = $mylist->id; @endphp
                        @if($mylist->status != 'removed')
                            @php
                                $isActive = ($mylist->status == 'active' || $mylist->status == 'inactive');
                            @endphp
                            <label class="flex items-start gap-3 py-2 cursor-pointer"
                                   for="newsletterlist{{ $mylist->id }}">
                                <input type="checkbox"
                                       class="checkbox checkbox-sm mt-0.5"
                                       name="lists[]"
                                       id="newsletterlist{{ $mylist->id }}"
                                       value="{{ $mylist->id }}"
                                       @checked($isActive) />
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold">{{ e($mylist->name) }}</span>
                                    @if($isActive && !$mylist->confirmed)
                                        <span class="badge badge-warning badge-sm ml-1"
                                              title="{{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED_TOOLTIP') }}">
                                            {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED') }}
                                        </span>
                                        @php
                                            $resendUrl = Route::url(
                                                'index.php?option=com_newsletter&task=resendconfirmation'
                                                . '&mid=' . $mylist->id
                                                . '&e=' . urlencode($email)
                                            );
                                        @endphp
                                        <a href="{{ $resendUrl }}" class="link link-primary text-xs ml-1">
                                            {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_CONFIRMLINK_TEXT') }}
                                        </a>
                                    @elseif($mylist->status == 'unsubscribed')
                                        <span class="badge badge-ghost badge-sm ml-1">
                                            {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBED') }}
                                        </span>
                                    @endif
                                    <p class="text-sm text-base-content/60 mt-1">
                                        {!! $mylist->description
                                            ? nl2br(e($mylist->description))
                                            : Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION') !!}
                                    </p>
                                </div>
                            </label>
                        @endif
                    @endforeach
                </x-form-section>
            @endif

            @if(count($alllists) > 0)
                <x-form-section :heading="Lang::txt('COM_NEWSLETTER_MAILINGLISTS_PUBLICLISTS')">
                    @foreach($alllists as $list)
                        @if(!in_array($list->id, $mylistIds))
                            <label class="flex items-start gap-3 py-2 cursor-pointer"
                                   for="newsletterlist{{ $list->id }}">
                                <input type="checkbox"
                                       class="checkbox checkbox-sm mt-0.5"
                                       name="lists[]"
                                       id="newsletterlist{{ $list->id }}"
                                       value="{{ $list->id }}" />
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold">{{ e($list->name) }}</span>
                                    <p class="text-sm text-base-content/60 mt-1">
                                        {!! $list->description
                                            ? nl2br(e($list->description))
                                            : Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION') !!}
                                    </p>
                                </div>
                            </label>
                        @endif
                    @endforeach
                </x-form-section>
            @endif

            @if(count($mylists) > 0 || count($alllists) > 0)
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_SAVE') }}
                    </button>
                </div>
            @else
                <x-empty-state>
                    {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NONE') }}
                </x-empty-state>
            @endif

            <input type="hidden" name="e" value="{{ urlencode($email) }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="task" value="domultisubscribe" />
            {!! Html::input('token') !!}
        </form>
    </div>
</x-page-container>
