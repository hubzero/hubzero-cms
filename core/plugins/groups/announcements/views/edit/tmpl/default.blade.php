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

$backUrl = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->cn
    . '&active=announcements'
);
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-ghost gap-2" href="{{ $backUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_BACK') }}
        </a>
    </li>
</ul>

<section class="main section">
    @if ($__view->getError())
        <div class="alert alert-error">
            <p>{!! implode('<br />', $__view->getErrors()) !!}</p>
        </div>
    @endif

    @php
    $formAction = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->get('cn')
        . '&active=announcements'
    );
    @endphp
    <form action="{{ $formAction }}"
        method="post"
        id="hubForm"
        class="full">

        <div class="explaination">
            {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_HINT') }}
        </div>

        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend text-lg font-semibold">
                @if ($announcement->get('id'))
                    {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EDIT') }}
                @else
                    {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NEW') }}
                @endif
            </legend>

            <div class="form-group mb-4">
                <label for="field_content" class="label">
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_ANNOUNCEMENT') }}
                        <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_REQUIRED') }}</span>
                    </span>
                </label>
                @php
                $editorContent = e(
                    stripslashes($announcement->get('content', ''))
                );
                echo $__view->editor(
                    'fields[content]',
                    $editorContent,
                    35,
                    5,
                    'field_content',
                    ['class' => 'form-control minimal no-footer']
                );
                @endphp
            </div>

            <fieldset class="fieldset border border-base-300 p-4 rounded-box mb-4">
                <legend class="fieldset-legend">
                    {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_WINDOW') }}
                    <span class="badge badge-ghost badge-sm">{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_OPTIONAL') }}</span>
                </legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="field-publish_up" class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_START') }}</span>
                        </label>
                        @php
                        $publish_up = $announcement->get('publish_up');
                        if ($publish_up && $publish_up != '0000-00-00 00:00:00') {
                            $publish_up = Date::of($publish_up)->toLocal('m/d/Y @ g:i a');
                        }
                        $publishHint = Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_HINT');
                        @endphp
                        <input class="datepicker input input-bordered w-full"
                            type="text"
                            name="fields[publish_up]"
                            id="field-publish_up"
                            value="{{ e($publish_up) }}" />
                        <div class="label">
                            <span class="label-text-alt">{{ $publishHint }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-publish_down" class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_PUBLISH_END') }}</span>
                        </label>
                        @php
                        $publish_down = $announcement->get('publish_down');
                        if ($publish_down && $publish_down != '0000-00-00 00:00:00') {
                            $publish_down = Date::of($publish_down)->toLocal('m/d/Y @ g:i a');
                        }
                        @endphp
                        <input class="datepicker input input-bordered w-full"
                            type="text"
                            name="fields[publish_down]"
                            id="field-publish_down"
                            value="{{ e($publish_down) }}" />
                        <div class="label">
                            <span class="label-text-alt">{{ $publishHint }}</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="form-control mb-2">
                <label class="label cursor-pointer justify-start gap-3" for="field-email">
                    <input class="checkbox checkbox-primary"
                        type="checkbox"
                        name="fields[email]"
                        id="field-email"
                        value="1"
                        @if ($announcement->get('email') == 1) checked @endif />
                    <span class="label-text">
                        @if ($announcement->get('sent') == 1)
                            <span class="text-warning font-semibold">
                                {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EMAIL_RESEND') }}
                            </span>
                        @else
                            {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EMAIL_MEMBERS') }}
                        @endif
                    </span>
                </label>
            </div>

            <div class="form-control mb-2">
                <label class="label cursor-pointer justify-start gap-3" for="field-priority">
                    <input class="checkbox checkbox-error"
                        type="checkbox"
                        name="fields[priority]"
                        id="field-priority"
                        value="1"
                        @if ($announcement->get('priority')) checked @endif />
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_MARK_HIGH_PRIORITY') }}
                        <span class="tooltip" data-tip="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_MARK_HIGH_PRIORITY_TITLE') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                    </span>
                </label>
            </div>

            <div class="form-control mb-4">
                <label class="label cursor-pointer justify-start gap-3" for="field-sticky">
                    <input class="checkbox checkbox-accent"
                        type="checkbox"
                        name="fields[sticky]"
                        id="field-sticky"
                        value="1"
                        @if ($announcement->get('sticky')) checked @endif />
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_MARK_STICKY') }}
                        <span class="tooltip" data-tip="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_MARK_STICKY_TITLE') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                    </span>
                </label>
            </div>
        </fieldset>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">
                {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_SAVE') }}
            </button>
        </div>

        <input type="hidden" name="fields[id]" value="{{ e($announcement->get('id')) }}" />
        <input type="hidden" name="fields[state]" value="1" />
        <input type="hidden" name="fields[scope]" value="{{ e($announcement->get('scope')) }}" />
        <input type="hidden" name="fields[scope_id]" value="{{ e($announcement->get('scope_id')) }}" />

        <input type="hidden" name="option" value="{{ e($option) }}" />
        <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
        <input type="hidden" name="active" value="announcements" />
        <input type="hidden" name="action" value="save" />

        {!! Html::input('token') !!}
    </form>
</section>
