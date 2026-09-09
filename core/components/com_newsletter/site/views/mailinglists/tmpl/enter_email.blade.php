{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $title = $__view->get('title', '');
    $option = $__view->get('option', 'com_newsletter');
    $controller = $__view->get('controller', 'mailinglists');
    $redirect = $__view->get('redirect', '');
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm"
           href="{{ Route::url('index.php?option=com_newsletter') }}">
            {{ Lang::txt('COM_NEWSLETTER_BROWSE') }}
        </a>
    @endslot

    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <form action="{{ Route::url('index.php?option=' . $option . '&task=subscribe') }}"
                      method="post">
                    <x-form-section :heading="Lang::txt('COM_NEWSLETTER_MAILINGLISTS_GUEST_PROMPT')">
                        <x-form-field name="e" inputId="email"
                                      :label="Lang::txt('COM_NEWSLETTER_MAILINGLISTS_EMAIL')" required>
                            <input type="email" name="e" id="email"
                                   class="input input-bordered w-full"
                                   placeholder="you@example.com" required />
                        </x-form-field>
                    </x-form-section>

                    <div class="flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_CONTINUE_GUEST') }}
                        </button>
                        <a class="btn btn-ghost" href="{{ $redirect }}">
                            {{ Lang::txt('COM_NEWSLETTER_LOGIN') }}
                        </a>
                    </div>

                    <input type="hidden" name="option" value="{{ $option }}" />
                    <input type="hidden" name="controller" value="{{ $controller }}" />
                    <input type="hidden" name="task" value="subscribe" />
                    {!! Html::input('token') !!}
                </form>
            </div>
        </div>
    </div>
</x-page-container>
