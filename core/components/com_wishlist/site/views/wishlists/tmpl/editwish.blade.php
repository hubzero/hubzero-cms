{{--
 * Add/edit wish form
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Event;

    $tags = $wish->tags('string') ?: Request::getString('tag', '');
    $wish->set('about', preg_replace('/<br\\s*?\/??>/i', '', $wish->get('about', '')));
@endphp

@if(!$wishlist->get('id'))
    <x-page-container :title="Lang::txt('COM_WISHLIST')">
        <div role="alert" class="alert alert-error">
            <span>{{ Lang::txt('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND') }}</span>
        </div>
    </x-page-container>
@else
    <x-page-container :title="e($__view->title)">
        @slot('actions')
            <a class="btn btn-sm"
               href="{{ Route::url($wishlist->link(), false) }}">
                {{ Lang::txt('COM_WISHLIST_WISHES_ALL') }}
            </a>
        @endslot

        @slot('sidebar')
            <x-sidebar-card :title="Lang::txt('COM_WISHLIST_HELP')">
                <p>{{ Lang::txt('COM_WISHLIST_TEXT_ADD_WISH') }}</p>
                @if($banking && $__view->task != 'editwish')
                    <div class="mt-3">
                        <p class="font-medium">{{ Lang::txt('COM_WISHLIST_WHAT_IS_REWARD') }}</p>
                        <p class="text-sm text-base-content/60">
                            {{ Lang::txt('COM_WISHLIST_WHY_ADDBONUS') }}
                            <a class="link" href="{{ $infolink }}">
                                {{ Lang::txt('COM_WISHLIST_LEARN_MORE') }}
                            </a>
                            {{ Lang::txt('COM_WISHLIST_ABOUT_POINTS') }}.
                        </p>
                    </div>
                @endif
            </x-sidebar-card>
        @endslot

        @if($__view->getError())
            <div role="alert" class="alert alert-error mb-4">
                <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
            </div>
        @endif

        <form id="hubForm" method="post"
              action="{{ Route::url('index.php?option=' . $option, false) }}"
              class="max-w-2xl">
            <x-form-section :heading="Lang::txt('COM_WISHLIST_DETAILS')">
                @if($__view->task == 'editwish')
                    <x-form-field name="by" inputId="field-by"
                                  :label="Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY')" required>
                        <input name="by" maxlength="50" id="field-by" type="text"
                               class="input input-bordered w-full"
                               value="{{ e($wish->proposer->get('username')) }}" />
                    </x-form-field>
                @endif

                <x-form-field name="fields[anonymous]" inputId="field-anonymous"
                              :label="Lang::txt('COM_WISHLIST_WISH_POST_ANONYMOUSLY')"
                              type="checkbox">
                    <input type="checkbox" name="fields[anonymous]" id="field-anonymous"
                           class="checkbox" value="1"
                           {{ $wish->get('anonymous') ? 'checked' : '' }} />
                </x-form-field>

                @if($wishlist->access('manage') && $wishlist->isPublic())
                    <x-form-field name="fields[private]" inputId="field-private"
                                  :label="Lang::txt('COM_WISHLIST_WISH_MAKE_PRIVATE')"
                                  type="checkbox">
                        <input type="checkbox" name="fields[private]" id="field-private"
                               class="checkbox" value="1"
                               {{ $wish->get('private') ? 'checked' : '' }} />
                    </x-form-field>
                @endif

                <input type="hidden" name="fields[proposed_by]"
                       value="{{ e($wish->get('proposed_by')) }}" />
                <input type="hidden" name="task" value="savewish" />
                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="wishlist" value="{{ e($wishlist->get('id')) }}" />
                <input type="hidden" name="fields[wishlist]" value="{{ e($wishlist->get('id')) }}" />
                <input type="hidden" name="fields[status]" value="{{ e($wish->get('status')) }}" />
                <input type="hidden" name="fields[id]" value="{{ e($wish->get('id')) }}" />
                {!! Html::input('token') !!}

                <x-form-field name="fields[subject]" inputId="subject"
                              :label="Lang::txt('COM_WISHLIST_SUMMARY_OF_WISH')" required>
                    <input name="fields[subject]" maxlength="200" id="subject" type="text"
                           class="input input-bordered w-full"
                           value="{{ e(stripslashes($wish->get('subject', ''))) }}" />
                </x-form-field>

                <x-form-field name="fields[about]" inputId="field_about"
                              :label="Lang::txt('COM_WISHLIST_WISH_EXPLAIN_IN_DETAIL')">
                    {!! $__view->editor(
                        'fields[about]',
                        e($wish->get('about')),
                        35, 10, 'field_about',
                        ['class' => 'textarea textarea-bordered w-full']
                    ) !!}
                </x-form-field>

                <x-form-field name="tags" inputId="actags"
                              :label="Lang::txt('COM_WISHLIST_WISH_ADD_TAGS')">
                    @php
                        $tf = Event::trigger(
                            'hubzero.onGetMultiEntry',
                            [['tags', 'tags', 'actags', '', $tags]]
                        );
                    @endphp
                    @if(count($tf) > 0)
                        {!! $tf[0] !!}
                    @else
                        <textarea name="tags" id="actags"
                                  class="textarea textarea-bordered w-full"
                                  rows="3">{{ e($wish->tags('string')) }}</textarea>
                    @endif
                </x-form-field>

                @if($banking && $__view->task != 'editwish')
                    <x-form-field name="reward" inputId="field-reward"
                                  :label="Lang::txt('COM_WISHLIST_ASSIGN_REWARD')">
                        <input type="text" name="reward" id="field-reward"
                               class="input input-bordered w-24"
                               value="" size="5"
                               {{ $funds <= 0 ? 'disabled' : '' }} />
                        <span class="text-sm text-base-content/60 ml-2">
                            {{ Lang::txt('COM_WISHLIST_YOU_HAVE') }}
                            <strong>{{ e($funds) }}</strong>
                            {{ Lang::txt('COM_WISHLIST_POINTS_TO_SPEND') }}.
                        </span>
                    </x-form-field>
                    <input type="hidden" name="funds" value="{{ e($funds) }}" />
                @endif
            </x-form-section>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="btn btn-primary">
                    {{ Lang::txt('COM_WISHLIST_FORM_SUBMIT') }}
                </button>
                <a class="btn" href="{{ $wish->link() }}">
                    {{ Lang::txt('JCANCEL') }}
                </a>
            </div>
        </form>
    </x-page-container>
@endif
