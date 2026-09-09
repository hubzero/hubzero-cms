{{--
  Resources thank-you page — shown after a contribution is submitted.

  Variables from controller:
    $title    — page title
    $option   — component option string
    $resource — resource object
    $config   — component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('create.css');

  $attachments = 0;
  $authors     = 0;
  $tags        = [];
  $state       = 'pending';
  $type        = '';

  if ($resource->get('id')) {
      switch ($resource->get('published')) {
          case 1:
              $state = 'published';
              break;
          case 2:
              $state = 'draft';
              break;
          case 3:
              $state = 'pending';
              break;
          case 0:
          default:
              $state = 'unpublished';
              break;
      }

      $type        = $resource->type()->get('type', Lang::txt('COM_CONTRIBUTE_NONE'));
      $attachments = $resource->children()->total();
      $authors     = $resource->authors()->total();
      $tags        = count($resource->tags());
  }

  $newUrl     = Route::url('index.php?option=' . $option . '&task=new');
  $browseUrl  = Route::url('index.php?option=' . $option . '&task=browse');
  $accountUrl = Route::url('index.php?option=com_members&task=myaccount');

  $autoApprove = ($config->get('autoapprove', 0) == 1);

  $stateBadgeClass = match ($state) {
      'published'   => 'badge-success',
      'draft'       => 'badge-warning',
      'pending'     => 'badge-info',
      'unpublished' => 'badge-ghost',
      default       => 'badge-ghost',
  };
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ Route::url('index.php?option=' . $option . '&task=draft') }}">
      {{ Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION') }}
    </a>
  @endslot

  @slot('sidebar')
    <a class="btn btn-outline btn-sm" href="{{ $newUrl }}">
      {{ Lang::txt('Return to start') }}
    </a>
  @endslot

  @if ($__view->getError())
    <div role="alert" class="alert alert-warning mb-4">
      <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
    </div>
  @endif

  <div role="alert" class="alert alert-success mb-6">
    <span>{{ Lang::txt('Thank you for your contribution!') }}</span>
  </div>

  {{-- Submission summary --}}
  <div class="card bg-base-100 shadow-sm mb-6">
    <div class="card-body">
      <h2 class="card-title text-base">{{ Lang::txt('Contribution submitted:') }}</h2>
      <div class="overflow-x-auto">
        <table class="table table-zebra" aria-label="{{ Lang::txt('Contribution submitted:') }}">
          <tbody>
            <tr>
              <th scope="row" class="font-medium">{{ Lang::txt('Type') }}</th>
              <td>
                {{ $type ? e(stripslashes($type)) : Lang::txt('(none)') }}
              </td>
            </tr>
            <tr>
              <th scope="row" class="font-medium">{{ Lang::txt('Title') }}</th>
              <td>
                @if ($resource->title)
                  {{ e(\Hubzero\Utility\Str::truncate(stripslashes($resource->title), 150)) }}
                @else
                  {{ Lang::txt('(none)') }}
                @endif
              </td>
            </tr>
            <tr>
              <th scope="row" class="font-medium">{{ Lang::txt('Attachments') }}</th>
              <td>{{ $attachments }}</td>
            </tr>
            <tr>
              <th scope="row" class="font-medium">{{ Lang::txt('Authors') }}</th>
              <td>{{ $authors }}</td>
            </tr>
            <tr>
              <th scope="row" class="font-medium">{{ Lang::txt('Tags') }}</th>
              <td>{{ $tags }}</td>
            </tr>
            <tr>
              <th scope="row" class="font-medium">{{ Lang::txt('Status') }}</th>
              <td>
                <span class="badge {{ $stateBadgeClass }}">{{ $state }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- FAQ section --}}
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <h2 class="card-title text-base">{{ Lang::txt('Frequently Asked Questions') }}</h2>

      <nav aria-label="{{ Lang::txt('Frequently Asked Questions') }}" class="mb-4">
        <ul class="list-disc list-inside space-y-1">
          <li><a class="link link-hover link-primary" href="#submission">{{ Lang::txt('What happens now?') }}</a></li>
          @if (!$autoApprove)
            <li><a class="link link-hover link-primary" href="#status">{{ Lang::txt('How will I know when my contribution is accepted?') }}</a></li>
          @endif
          <li><a class="link link-hover link-primary" href="#retract">{{ Lang::txt('Ooops! I missed something and/or submitted too early!') }}</a></li>
        </ul>
      </nav>

      @if (!$autoApprove)
        {{-- Manual approval flow --}}
        <section id="submission" class="mb-6" aria-labelledby="faq-submission-heading">
          <h3 id="faq-submission-heading" class="text-base font-semibold mb-2">{{ Lang::txt('What happens now?') }}</h3>
          <p>
            After submitting your contribution, it will be reviewed for completeness.
            If all appears satisfactory, the contribution will be approved and immediately
            appear in the <a class="link link-primary" href="{{ $browseUrl }}">resources listing</a>.
          </p>
        </section>

        <section id="status" class="mb-6" aria-labelledby="faq-status-heading">
          <h3 id="faq-status-heading" class="text-base font-semibold mb-2">{{ Lang::txt('How will I know when my contribution is accepted?') }}</h3>
          <p class="mb-2">
            When a contribution passes the review stage and is published (made publicly available),
            an email is sent to all authors listed on the contribution.
          </p>
          <p class="mb-2">You may also continually monitor the status by:</p>
          <ul class="list-disc list-inside space-y-1">
            <li>
              Checking your "contributions" tab under your
              <a class="link link-primary" href="{{ $accountUrl }}">account</a>.
            </li>
            <li>
              Checking the "My Drafts" module on your personalized dashboard (found
              <a class="link link-primary" href="{{ $accountUrl }}">here</a>).
              <strong>Note:</strong> The module must be present on your dashboard.
              If it isn't, you can easily add it from the "personalize dashboard" item.
            </li>
            <li>
              Visiting the <a class="link link-primary" href="{{ $newUrl }}">new contribution</a> page.
            </li>
          </ul>
        </section>

        <section id="retract" aria-labelledby="faq-retract-heading">
          <h3 id="faq-retract-heading" class="text-base font-semibold mb-2">{{ Lang::txt('Ooops! I missed something and/or submitted too early!') }}</h3>
          <p class="mb-2">No worries! You can retract a submission by following these steps:</p>
          <ol class="list-decimal list-inside space-y-1">
            <li>Visit the <a class="link link-primary" href="{{ $newUrl }}">new contribution</a> page.</li>
            <li>You should be presented with a list of your "drafts" and "pending" submissions. Find the (pending) contribution you wish to retract.</li>
            <li>Click "retract".</li>
          </ol>
        </section>

      @else
        {{-- Auto-approve flow --}}
        @php
          $viewUrl    = Route::url('index.php?option=' . $option . '&id=' . $resource->id);
          $supportUrl = Route::url('index.php?option=com_support');
        @endphp

        <section id="submission" class="mb-6" aria-labelledby="faq-submission-heading">
          <h3 id="faq-submission-heading" class="text-base font-semibold mb-2">{{ Lang::txt('What happens now?') }}</h3>
          <p>
            Your contribution is now published. You may view it
            <a class="link link-primary" href="{{ $viewUrl }}">here</a>.
          </p>
        </section>

        <section id="retract" aria-labelledby="faq-retract-heading">
          <h3 id="faq-retract-heading" class="text-base font-semibold mb-2">{{ Lang::txt('Ooops! I missed something and/or submitted too early!') }}</h3>
          <p class="mb-2">
            No worries! You can either
            <a class="link link-primary" href="{{ $supportUrl }}">contact the site administrators</a>
            and ask the submission be retracted (set back to "draft" status) or modify a submission
            by following these steps:
          </p>
          <ol class="list-decimal list-inside space-y-1">
            <li>
              Visit the <a class="link link-primary" href="{{ $viewUrl }}">resource's page</a>
              while <strong>logged in</strong>.
            </li>
            <li>You should see an "edit" button or link next to the title of the resource.</li>
            <li>Click "edit" and make the desired edits. Changes on approved resources take effect immediately and do not require approval.</li>
          </ol>
        </section>
      @endif
    </div>
  </div>

</x-page-container>
