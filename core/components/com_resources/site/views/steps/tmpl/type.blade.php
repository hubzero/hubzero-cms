{{--
  Resources contribution — type selection page.

  Variables from controller:
    $title   — page title
    $option  — component option string
    $step    — current step number
    $types   — collection of resource Type models
    $group   — group CN (optional)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css('create.css');
  $__view->js('create.js');

  $error = $__view->getError();
  $newUrl = Route::url('index.php?option=' . $option . '&task=new');
  $siteName = Config::get('sitename');
  $baseUrl = Request::base(true);
  $supportUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
  $accountUrl = Route::url('index.php?option=com_members&task=myaccount');
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');

  // Filter contributable types
  $contributableTypes = [];
  if ($types) {
      foreach ($types as $type) {
          if (!$type->state || $type->contributable != 1) {
              continue;
          }
          if ($type->alias == 'tools' && !Component::isEnabled('com_tools', true)) {
              continue;
          }
          $contributableTypes[] = $type;
      }
  }
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $newUrl }}">
      {{ Lang::txt('COM_RESOURCES_TYPE_MAIN_PAGE') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_CONTRIBUTE_STEP_TYPE')">
      <p class="text-sm text-base-content/70">
        {{ Lang::txt('COM_RESOURCES_TYPE_SIDEBAR_HELP') }}
      </p>
    </x-sidebar-card>
  @endslot

  @if($error)
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ e($error) }}</span>
    </div>
  @endif

  {{-- Type selection cards --}}
  @if(count($contributableTypes) > 0)
    <x-card-grid :cols="3">
      @foreach($contributableTypes as $type)
        @php
          if ($type->alias == 'tools') {
              $typeUrl = Route::url('index.php?option=com_tools&task=create');
          } else {
              $typeUrl = Route::url(
                  'index.php?option=' . $option
                  . '&task=draft&step=' . $step
                  . '&type=' . $type->id
                  . ($group ? '&group=' . $group : '')
              );
          }

          $typeDescription = html_entity_decode(
              str_replace('&amp;', '&', strip_tags(stripslashes($type->description)))
          );
        @endphp
        <div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow">
          <div class="card-body items-center text-center">
            <h3 class="card-title">
              <a href="{{ $typeUrl }}" class="link link-hover after:absolute after:inset-0">
                {{ e(stripslashes($type->type)) }}
              </a>
            </h3>
            <p class="text-sm text-base-content/70">
              {{ e($typeDescription) }}
            </p>
          </div>
        </div>
      @endforeach
    </x-card-grid>
  @endif

  {{-- License info --}}
  @php
    $licenseLink = '<a class="link" href="' . $baseUrl . '/legal/license">'
        . Lang::txt('COM_RESOURCES_TYPE_LICENSE_AGREEMENT_LINK') . '</a>';
    $licensingLink = '<a class="link" href="' . $baseUrl . '/legal/licensing">'
        . Lang::txt('COM_RESOURCES_TYPE_LICENSE_CONTRIBUTIONS_LINK') . '</a>';
  @endphp
  <div role="alert" class="alert alert-info mt-6">
    <span>
      {!! Lang::txt('COM_RESOURCES_TYPE_LICENSE_NOTICE', e($siteName), $licenseLink, $licensingLink) !!}
    </span>
  </div>

  {{-- FAQ section --}}
  <section class="mt-8" aria-labelledby="faq-heading">
    <h3 id="faq-heading" class="text-lg font-semibold mb-4">{{ Lang::txt('COM_RESOURCES_TYPE_FAQ_HEADING') }}</h3>

    <div class="join join-vertical w-full">
      @php
        $contactLink = '<a class="link" href="' . $supportUrl . '">'
            . Lang::txt('COM_RESOURCES_TYPE_FAQ1_CONTACT_LINK') . '</a>';
      @endphp
      <div class="collapse collapse-arrow join-item border border-base-300">
        <input type="radio" name="faq-accordion" aria-label="{{ Lang::txt('COM_RESOURCES_TYPE_FAQ1_TITLE') }}" />
        <div class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_TYPE_FAQ1_TITLE') }}
        </div>
        <div class="collapse-content">
          <p>{!! Lang::txt('COM_RESOURCES_TYPE_FAQ1_INTRO', $contactLink) !!}</p>
          <ol class="list-decimal list-inside my-2 ml-4">
            <li>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ1_ITEM1') }}</li>
            <li>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ1_ITEM2') }}</li>
          </ol>
          <p>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ1_CLOSING') }}</p>
        </div>
      </div>

      @php
        $accountLink = '<a class="link" href="' . $accountUrl . '">'
            . Lang::txt('COM_RESOURCES_TYPE_FAQ2_ACCOUNT_LINK') . '</a>';
        $accountLink2 = '<a class="link" href="' . $accountUrl . '">'
            . Lang::txt('COM_RESOURCES_TYPE_FAQ2_HERE_LINK') . '</a>';
        $newContribLink = '<a class="link" href="' . $newUrl . '">'
            . Lang::txt('COM_RESOURCES_TYPE_FAQ2_NEW_CONTRIBUTION_LINK') . '</a>';
      @endphp
      <div class="collapse collapse-arrow join-item border border-base-300">
        <input type="radio" name="faq-accordion" aria-label="{{ Lang::txt('COM_RESOURCES_TYPE_FAQ2_TITLE') }}" />
        <div class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_TYPE_FAQ2_TITLE') }}
        </div>
        <div class="collapse-content">
          <p>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ2_BODY') }}</p>
          <p class="mt-2">{{ Lang::txt('COM_RESOURCES_TYPE_FAQ2_METHODS_INTRO') }}</p>
          <ul class="list-disc list-inside my-2 ml-4">
            <li>{!! Lang::txt('COM_RESOURCES_TYPE_FAQ2_METHOD1', $accountLink) !!}</li>
            <li>{!! Lang::txt('COM_RESOURCES_TYPE_FAQ2_METHOD2', $accountLink2) !!}</li>
            <li>{!! Lang::txt('COM_RESOURCES_TYPE_FAQ2_METHOD3', $newContribLink) !!}</li>
          </ul>
        </div>
      </div>

      @php
        $browseLink = '<a class="link" href="' . $browseUrl . '">'
            . Lang::txt('COM_RESOURCES_TYPE_FAQ3_LISTING_LINK') . '</a>';
      @endphp
      <div class="collapse collapse-arrow join-item border border-base-300">
        <input type="radio" name="faq-accordion" aria-label="{{ Lang::txt('COM_RESOURCES_TYPE_FAQ3_TITLE') }}" />
        <div class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_TYPE_FAQ3_TITLE') }}
        </div>
        <div class="collapse-content">
          <p>{!! Lang::txt('COM_RESOURCES_TYPE_FAQ3_BODY', $browseLink) !!}</p>
        </div>
      </div>

      @php
        $newContribLink2 = '<a class="link" href="' . $newUrl . '">'
            . Lang::txt('COM_RESOURCES_TYPE_FAQ4_NEW_CONTRIBUTION_LINK') . '</a>';
      @endphp
      <div class="collapse collapse-arrow join-item border border-base-300">
        <input type="radio" name="faq-accordion" aria-label="{{ Lang::txt('COM_RESOURCES_TYPE_FAQ4_TITLE') }}" />
        <div class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_TYPE_FAQ4_TITLE') }}
        </div>
        <div class="collapse-content">
          <p>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ4_INTRO') }}</p>
          <ul class="list-disc list-inside my-2 ml-4">
            <li>{!! Lang::txt('COM_RESOURCES_TYPE_FAQ4_STEP1', $newContribLink2) !!}</li>
            <li>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ4_STEP2') }}</li>
            <li>{{ Lang::txt('COM_RESOURCES_TYPE_FAQ4_STEP3') }}</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

</x-page-container>
