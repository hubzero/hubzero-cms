{{--
  Newsletter — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo = \Components\Newsletter\Helpers\Permissions::getActions('newsletter');
  $text  = ($task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

  // Stories
  $storyBase = 'index.php?option=' . $option . '&controller=stories&nid=' . $newsletter->id;

  $primaries = $newsletter->primary()
      ->whereEquals('deleted', 0)
      ->ordered()
      ->rows();

  $primaryHighestOrder = 1;
  if ($primaries->count() > 0) {
      $primaries->last();
      $primaryHighestOrder = $primaries->seek($primaries->key())->get('order');
      $primaries->rewind();
  }

  $secondaries = $newsletter->secondary()
      ->whereEquals('deleted', 0)
      ->ordered()
      ->rows();

  $secondaryHighestOrder = 1;
  if ($secondaries->count() > 0) {
      $secondaries->last();
      $secondaryHighestOrder = $secondaries->seek($secondaries->key())->get('order');
      $secondaries->rewind();
  }

  $params = new \Hubzero\Config\Registry($newsletter->params);
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER') }}: {{ $text }}"
    icon="newsletter"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Details fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_DETAILS') }}">

      <div class="admin-field">
        <label for="newsletter-name" class="label">
          {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="newsletter[name]"
               id="newsletter-name"
               class="input input-bordered w-full"
               required
               value="{{ $newsletter->name }}" />
      </div>

      <div class="admin-field">
        <label for="newsletter-alias" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_ALIAS') }}</label>
        <input type="text"
               name="newsletter[alias]"
               id="newsletter-alias"
               class="input input-bordered w-full"
               value="{{ $newsletter->alias }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_ALIAS_HINT') }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="newsletter-issue" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_ISSUE') }}</label>
          <input type="text"
                 name="newsletter[issue]"
                 id="newsletter-issue"
                 class="input input-bordered w-full"
                 value="{{ $newsletter->issue }}" />
        </div>
        <div class="admin-field">
          <label for="newsletter-type" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_FORMAT') }}</label>
          <select name="newsletter[type]" id="newsletter-type" class="select select-bordered w-full">
            <option value="html" @selected($newsletter->type == 'html')>{{ Lang::txt('COM_NEWSLETTER_FORMAT_HTML') }}</option>
            <option value="plain" @selected($newsletter->type == 'plain')>{{ Lang::txt('COM_NEWSLETTER_FORMAT_PLAIN') }}</option>
          </select>
        </div>
      </div>

      <div class="admin-field">
        <label for="newsletter-template_id" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE') }}</label>
        <select name="newsletter[template_id]" id="newsletter-template_id" class="select select-bordered w-full">
          <option value="">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE_DEFAULT') }}</option>
          <option value="-1" @selected($newsletter->template_id == '-1')>
            {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE_NONE') }}
          </option>
          @foreach($templates as $t)
            <option value="{{ $t->id }}" @selected($t->id == $newsletter->template_id)>
              {{ $t->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="admin-field">
        <label for="newsletter-published" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW') }}</label>
        <select name="newsletter[published]" id="newsletter-published" class="select select-bordered w-full">
          <option value="1" @selected($newsletter->published == '1')>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW_SHOW') }}</option>
          <option value="0" @selected($newsletter->published == '0')>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW_DONT_SHOW') }}</option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="newsletter-tracking" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_EMAIL_TRACKING') }}</label>
        <select name="newsletter[tracking]" id="newsletter-tracking" class="select select-bordered w-full">
          <option value="1" @selected($newsletter->tracking)>{{ Lang::txt('JYES') }}</option>
          <option value="0" @selected(!$newsletter->tracking)>{{ Lang::txt('JNO') }}</option>
        </select>
        @php
          $trackLink = $config->get('email_tracking_link', 'http://kb.mailchimp.com/article/how-open-tracking-works');
        @endphp
        <p class="text-xs text-muted-foreground mt-1">
          {!! Lang::txt('COM_NEWSLETTER_NEWSLETTER_WHAT_IS_TRACKING', $trackLink) !!}
        </p>
      </div>

      <div class="admin-field">
        <label for="newsletter-autogen" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_EMAIL_AUTOGEN') }}</label>
        <select name="newsletter[autogen]" id="newsletter-autogen" class="select select-bordered w-full">
          <option value="0" @selected($newsletter->autogen == 0)>{{ Lang::txt('Disabled') }}</option>
          <option value="1" @selected($newsletter->autogen == 1)>{{ Lang::txt('DAILY') }}</option>
          <option value="2" @selected($newsletter->autogen == 2)>{{ Lang::txt('WEEKLY') }}</option>
          <option value="3" @selected($newsletter->autogen == 3)>{{ Lang::txt('MONTHLY') }}</option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_EMAIL_AUTOGEN_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  {{-- Sidebar --}}
  @slot('sidebar')
    @if($newsletter->id)
      {{-- Meta --}}
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_ID') }}</td>
                <td>{{ $newsletter->id }}</td>
              </tr>
              @if($newsletter->created)
                <tr>
                  <td>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CREATED_DATE') }}</td>
                  <td>{{ Date::of($newsletter->created)->format('M d, Y @ g:ia') }}</td>
                </tr>
              @endif
              @if($newsletter->created_by)
                @php
                  $creator = User::getInstance($newsletter->created_by);
                  $creatorName = (is_object($creator) && $creator->get('name') != '')
                      ? $creator->get('name') : 'Admin';
                @endphp
                <tr>
                  <td>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CREATED_BY') }}</td>
                  <td>{{ $creatorName }}</td>
                </tr>
              @endif
              @if($newsletter->modified)
                <tr>
                  <td>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_LAST_MODIFIED') }}</td>
                  <td>{{ Date::of($newsletter->modified)->format('M d, Y @ g:ia') }}</td>
                </tr>
              @endif
              @if($newsletter->modified_by)
                @php
                  $modifier = User::getInstance($newsletter->modified_by);
                  $modifierName = (is_object($modifier) && $modifier->get('name') != '')
                      ? $modifier->get('name') : 'Admin';
                @endphp
                <tr>
                  <td>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_LAST_MODIFIED_BY') }}</td>
                  <td>{{ $modifierName }}</td>
                </tr>
              @endif
            </tbody>
          </table>
      </x-admin-fieldset>

      {{-- Mailing details --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_DETAILS') }}">
          <div class="admin-field">
            <label for="param-from_name" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FROM_NAME') }}</label>
            <input type="text"
                   name="params[from_name]"
                   id="param-from_name"
                   class="input input-bordered w-full"
                   value="{{ $params->get('from_name') }}" />
          </div>
          <div class="admin-field">
            <label for="param-from_address" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FROM_EMAIL') }}</label>
            <input type="text"
                   name="params[from_address]"
                   id="param-from_address"
                   class="input input-bordered w-full"
                   value="{{ $params->get('from_address') }}" />
          </div>
          <div class="admin-field">
            <label for="param-replyto_name" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_REPLYTO_NAME') }}</label>
            <input type="text"
                   name="params[replyto_name]"
                   id="param-replyto_name"
                   class="input input-bordered w-full"
                   value="{{ $params->get('replyto_name') }}" />
          </div>
          <div class="admin-field">
            <label for="param-replyto_address" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_REPLYTO_EMAIL') }}</label>
            <input type="text"
                   name="params[replyto_address]"
                   id="param-replyto_address"
                   class="input input-bordered w-full"
                   value="{{ $params->get('replyto_address') }}" />
          </div>
      </x-admin-fieldset>
    @else
      <div role="alert" class="alert alert-info">
        {{ Lang::txt('COM_NEWSLETTER_MUST_SAVE_TO_ADD_CONTENT') }}
      </div>
    @endif
  @endslot

  {{-- Content section (only for existing newsletters) --}}
  @if($newsletter->id)
    @if($newsletter->template_id == '-1' || (!$newsletter->template_id && $newsletter->content != ''))
      {{-- No template: raw HTML/plain content --}}
      <div class="admin-fieldset mt-6">
        <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT') }}</h3>
        <x-admin-fieldset>

          <div class="admin-field">
            <label for="newsletter-html_content" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML') }}</label>
            <textarea name="newsletter[html_content]"
                      id="newsletter-html_content"
                      class="textarea textarea-bordered w-full font-mono text-sm"
                      rows="20">{{ $newsletter->html_content }}</textarea>
            <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML_HINT') }}</p>
          </div>

          <div class="admin-field">
            <label for="newsletter-plain_content" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN') }}</label>
            <textarea name="newsletter[plain_content]"
                      id="newsletter-plain_content"
                      class="textarea textarea-bordered w-full font-mono text-sm"
                      rows="20">{{ $newsletter->plain_content }}</textarea>
            <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN_HINT') }}</p>
          </div>

        </x-admin-fieldset>
      </div>
    @else
      {{-- Template-based: story accordion --}}
      <div class="admin-fieldset mt-6" id="primary-stories">
        <h3 class="admin-fieldset-heading flex items-center justify-between">
          @if($newsletter->autogen == 0)
            <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_PRIMARY_STORIES') }}</span>
            <a href="{!! Route::url($storyBase . '&task=add&type=primary', false) !!}"
               class="btn btn-sm btn-primary">
              {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_PRIMARY_STORIES_ADD') }}
            </a>
          @else
            <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_AUTOGEN_STORIES') }}</span>
            <a href="{!! Route::url($storyBase . '&task=add&type=autogen', false) !!}"
               class="btn btn-sm btn-primary">
              {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_AUTOGEN_STORIES_ADD') }}
            </a>
          @endif
        </h3>
        <div class="admin-fieldset-body space-y-2">
          @forelse($primaries as $pi => $primary)
            <details class="collapse collapse-arrow bg-base-200 rounded-box">
              <summary class="collapse-title font-medium">
                {{ ($pi + 1) }}. {{ $primary->title }}
              </summary>
              <div class="collapse-content">
                <div class="flex gap-2 mb-3">
                  <a href="{!! Route::url($storyBase . '&task=edit&type=primary&sid=' . $primary->id, false) !!}"
                     class="btn btn-xs btn-primary">
                    {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_EDIT_STORY') }}
                  </a>
                  <a href="{!! Route::url($storyBase . '&task=delete&type=primary&sid=' . $primary->id, false) !!}"
                     class="btn btn-xs btn-error btn-outline">
                    {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_DELETE_STORY') }}
                  </a>
                </div>
                <table class="admin-meta">
                  <tbody>
                    <tr>
                      <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE') }}</td>
                      <td>{{ $primary->title }}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER') }}</td>
                      <td class="flex items-center gap-2">
                        <span>{{ $primary->order }}</span>
                        @if($primary->order > 1)
                          <a href="{!! Route::url($storyBase . '&task=reorder&direction=up&type=primary&sid=' . $primary->id, false) !!}"
                             class="btn btn-xs btn-ghost">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_UP') }}</a>
                        @endif
                        @if($primary->order < $primaryHighestOrder)
                          <a href="{!! Route::url($storyBase . '&task=reorder&direction=down&type=primary&sid=' . $primary->id, false) !!}"
                             class="btn btn-xs btn-ghost">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_DOWN') }}</a>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY') }}</td>
                      <td>{!! nl2br($primary->story) !!}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE') }}</td>
                      <td>
                        <strong>{{ $primary->readmore_title }}</strong>
                        @if($primary->readmore_link)
                          — {{ $primary->readmore_link }}
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </details>
          @empty
            <p class="text-sm text-muted-foreground py-2">{{ Lang::txt('COM_NEWSLETTER_NO_STORIES') }}</p>
          @endforelse
        </div>
      </div>

      {{-- Secondary stories (hidden if autogen) --}}
      @if($newsletter->autogen == 0)
        <div class="admin-fieldset mt-6" id="secondary-stories">
          <h3 class="admin-fieldset-heading flex items-center justify-between">
            <span>{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SECONDARY_STORIES') }}</span>
            <a href="{!! Route::url($storyBase . '&task=add&type=secondary', false) !!}"
               class="btn btn-sm btn-primary">
              {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_SECONDARY_STORIES_ADD') }}
            </a>
          </h3>
          <div class="admin-fieldset-body space-y-2">
            @forelse($secondaries as $si => $secondary)
              <details class="collapse collapse-arrow bg-base-200 rounded-box">
                <summary class="collapse-title font-medium">
                  {{ ($si + 1) }}. {{ $secondary->title }}
                </summary>
                <div class="collapse-content">
                  <div class="flex gap-2 mb-3">
                    <a href="{!! Route::url($storyBase . '&task=edit&type=secondary&sid=' . $secondary->id, false) !!}"
                       class="btn btn-xs btn-primary">
                      {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_EDIT_STORY') }}
                    </a>
                    <a href="{!! Route::url($storyBase . '&task=delete&type=secondary&sid=' . $secondary->id, false) !!}"
                       class="btn btn-xs btn-error btn-outline">
                      {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_DELETE_STORY') }}
                    </a>
                  </div>
                  <table class="admin-meta">
                    <tbody>
                      <tr>
                        <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE') }}</td>
                        <td>{{ $secondary->title }}</td>
                      </tr>
                      <tr>
                        <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER') }}</td>
                        <td class="flex items-center gap-2">
                          <span>{{ $secondary->order }}</span>
                          @if($secondary->order > 1)
                            <a href="{!! Route::url($storyBase . '&task=reorder&direction=up&type=secondary&sid=' . $secondary->id, false) !!}"
                               class="btn btn-xs btn-ghost">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_UP') }}</a>
                          @endif
                          @if($secondary->order < $secondaryHighestOrder)
                            <a href="{!! Route::url($storyBase . '&task=reorder&direction=down&type=secondary&sid=' . $secondary->id, false) !!}"
                               class="btn btn-xs btn-ghost">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_DOWN') }}</a>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY') }}</td>
                        <td>{!! nl2br($secondary->story) !!}</td>
                      </tr>
                      <tr>
                        <td class="font-semibold">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE') }}</td>
                        <td>
                          <strong>{{ $secondary->readmore_title }}</strong>
                          @if($secondary->readmore_link)
                            — {{ $secondary->readmore_link }}
                          @endif
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </details>
            @empty
              <p class="text-sm text-muted-foreground py-2">{{ Lang::txt('COM_NEWSLETTER_NO_STORIES') }}</p>
            @endforelse
          </div>
        </div>
      @endif
    @endif
  @endif

  <input type="hidden" name="newsletter[id]" value="{{ $newsletter->id }}" />
  <input type="hidden" name="id" value="{{ $newsletter->id }}" />
  <input type="hidden" name="newsletter[created]" value="{{ $newsletter->created }}" />
  <input type="hidden" name="newsletter[created_by]" value="{{ $newsletter->created_by }}" />
  <input type="hidden" name="newsletter[modified]" value="{{ $newsletter->modified }}" />
  <input type="hidden" name="newsletter[modified_by]" value="{{ $newsletter->modified_by }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
