{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $code = $__view->get('code', '');
    $campaignId = $__view->get('campaignId', 0);
    $userId = $__view->get('userId', 0);
    $subs = $__view->get('subs', []);
    $hubname = Config::get('sitename');

    $pageTitle = Lang::txt('COM_NEWSLETTER_EMAIL_SUBSCRIPTIONS');
    $formAction = Route::url(
        'index.php?option=com_newsletter&controller=email-subscriptions&task=update'
    );

    Pathway::append($pageTitle, '');
@endphp

<x-page-container :title="$pageTitle">
    <div class="max-w-lg mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <form method="POST"
                      action="{{ $formAction }}">

                    @foreach($subs as $s)
                        @php $sKey = $s['foreign_key']; @endphp
                        <div class="mb-6">
                            <label class="form-field-label">{{ $s['label'] }}</label>

                            @if($subView = $s['view'])
                                {!! $__view->view($subView)
                                    ->set('userId', $userId)
                                    ->loadTemplate() !!}
                            @endif

                            <select name="subscriptions[{{ $sKey }}][preference]"
                                    class="select select-bordered w-full">
                                @foreach($s['options'] as $o)
                                    <option @selected($o == $s['preference'])>
                                        {{ $o }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden"
                                   name="subscriptions[{{ $sKey }}][foreign_key]"
                                   value="{{ $sKey }}" />
                        </div>
                    @endforeach

                    {!! Html::input('token') !!}
                    <input type="hidden" name="code" value="{{ $code }}" />
                    <input type="hidden" name="userId" value="{{ $userId }}" />
                    <input type="hidden" name="campaign" value="{{ $campaignId }}" />

                    <button type="submit" class="btn btn-primary">
                        {{ Lang::txt('INPUT_SUBMIT') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 text-sm text-base-content/60">
            <p>
                {!! Lang::txt('COM_NEWSLETTER_EMAIL_SUBSCRIPTIONS_DISCLAIMER', $hubname, $hubname, $hubname) !!}
            </p>
        </div>
    </div>
</x-page-container>
