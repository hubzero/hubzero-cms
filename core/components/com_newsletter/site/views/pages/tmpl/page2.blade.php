{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $code = $__view->get('code', '');
    $pageId = $__view->get('pageId', 0);
    $user = $__view->get('user', '');
    $campaignId = $__view->get('campaign', 0);

    $pageTitle = Lang::txt('COM_NEWSLETTER_REPLY');
    $formAction = Route::url(
        'index.php?option=com_newsletter&controller=replies&task=create'
    );

    Pathway::append($pageTitle, '');
@endphp

<x-page-container :title="$pageTitle">
    <div class="max-w-2xl mx-auto">
        {!! $__view->view('_page2_instructions')->loadTemplate() !!}

        <div class="card bg-base-100 shadow-sm mt-6">
            <div class="card-body">
                <form method="POST" action="{{ $formAction }}">
                    <x-form-field name="reply[text]" inputId="reply-text">
                        <textarea name="reply[text]" id="reply-text" rows="20"
                                  class="textarea textarea-bordered w-full"></textarea>
                    </x-form-field>

                    {!! Html::input('token') !!}
                    <input type="hidden" name="code" value="{{ $code }}" />
                    <input type="hidden" name="page_id" value="{{ $pageId }}" />
                    <input type="hidden" name="user" value="{{ $user }}" />
                    <input type="hidden" name="campaign_id" value="{{ $campaignId }}" />

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ Lang::txt('INPUT_SUBMIT') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-page-container>
