{{--
  Resources contribution landing page — intro, in-progress submissions, and
  contributable resource types.

  Variables from controller:
    $title   — page title
    $option  — component option string

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('introduction.css', 'system');
  $__view->css('create.css');

  $database = App::get('db');

  $submissions = null;
  if (!User::isGuest()) {
      $query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype,";
      $query .= " AA.subtable, R.created, R.created_by, R.published, R.publish_up,";
      $query .= " R.standalone, R.rating, R.times_rated, R.alias, R.ranking,";
      $query .= " rt.type AS typetitle";
      $query .= " FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R";
      $query .= " LEFT JOIN #__resource_types AS t ON R.logical_type=t.id";
      $query .= " WHERE AA.authorid = " . User::get('id');
      $query .= " AND R.id = AA.subid";
      $query .= " AND AA.subtable = 'resources'";
      $query .= " AND R.standalone=1 AND R.type=rt.id";
      $query .= " AND (R.published=2 OR R.published=3) AND R.type!=7";
      $query .= " ORDER BY published ASC, title ASC";

      $database->setQuery($query);
      $submissions = $database->loadObjectList();
  }

  $getStartedUrl = Route::url('index.php?option=' . $option . '&task=draft');
  $categories = \Components\Resources\Models\Type::getMajorTypes();
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $getStartedUrl }}">
      {{ Lang::txt('Get Started') }} &rsaquo;
    </a>
  @endslot

  {{-- Introduction --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div>
      <h3 class="text-lg font-semibold mb-2">Present your work!</h3>
      <p>
        Become a contributor and share your work with the community! Contributing
        content is easy. Our step-by-step forms will guide you through the process.
      </p>
    </div>
    <div>
      <h3 class="text-lg font-semibold mb-2">What do I need?</h3>
      <p>
        The submission process will guide you through step-by-step, but for more
        detailed instructions on what can be submitted and how, please see the list
        of submission types below.
      </p>
    </div>
  </div>

  {{-- In-progress submissions --}}
  @if(!User::isGuest())
    <div class="mb-8">
      <h2 class="text-xl font-bold mb-4">{{ Lang::txt('In Progress') }}</h2>

      @if($submissions)
        <div class="overflow-x-auto">
          <table class="table table-zebra w-full" id="submissions">
            <thead>
              <tr>
                <th scope="col">{{ Lang::txt('Title') }}</th>
                <th scope="col">{{ Lang::txt('Attachments') }}</th>
                <th scope="col">{{ Lang::txt('Authors') }}</th>
                <th scope="col">{{ Lang::txt('Tags') }}</th>
                <th scope="col">{{ Lang::txt('Status') }}</th>
                <th scope="col">
                  <span class="sr-only">{{ Lang::txt('Actions') }}</span>
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($submissions as $submission)
                @php
                  $resource = \Components\Resources\Models\Entry::oneOrNew($submission->id);

                  $state = match ((int) $resource->get('published')) {
                      1 => 'published',
                      2 => 'draft',
                      3 => 'pending',
                      default => 'unpublished',
                  };

                  $attachments = $resource->children()->total();
                  $authors     = $resource->authors()->total();
                  $tags        = count($resource->tags());

                  $isDraft = ($submission->published == 2);
                  $baseUrl = 'index.php?option=' . $option;

                  $step1Url   = Route::url($baseUrl . '&task=draft&step=1&id=' . $submission->id);
                  $step2Url   = Route::url($baseUrl . '&task=draft&step=2&id=' . $submission->id);
                  $step3Url   = Route::url($baseUrl . '&task=draft&step=3&id=' . $submission->id);
                  $step4Url   = Route::url($baseUrl . '&task=draft&step=4&id=' . $submission->id);
                  $step5Url   = Route::url($baseUrl . '&task=draft&step=5&id=' . $submission->id);
                  $retractUrl = Route::url($baseUrl . '&task=retract&id=' . $submission->id);
                  $discardUrl = Route::url($baseUrl . '&task=discard&id=' . $submission->id);

                  $titleText = stripslashes($submission->title);
                  $typeText  = stripslashes($submission->typetitle);

                  $badgeClass = match ($state) {
                      'published'   => 'badge-success',
                      'draft'       => 'badge-warning',
                      'pending'     => 'badge-info',
                      default       => 'badge-ghost',
                  };
                @endphp
                <tr>
                  <td>
                    @if($isDraft)
                      <a class="link link-hover" href="{{ $step1Url }}">{{ e($titleText) }}</a>
                    @else
                      {{ e($titleText) }}
                    @endif
                    <br>
                    <span class="text-sm text-base-content/60">{{ e($typeText) }}</span>
                  </td>
                  <td>
                    @if($isDraft)
                      <a class="link link-hover" href="{{ $step2Url }}">
                        {{ $attachments }} attachment(s)
                      </a>
                    @else
                      {{ $attachments }} attachment(s)
                    @endif
                  </td>
                  <td>
                    @if($isDraft)
                      <a class="link link-hover" href="{{ $step3Url }}">
                        {{ $authors }} author(s)
                      </a>
                    @else
                      {{ $authors }} author(s)
                    @endif
                  </td>
                  <td>
                    @if($isDraft)
                      <a class="link link-hover" href="{{ $step4Url }}">
                        {{ $tags }} tag(s)
                      </a>
                    @else
                      {{ $tags }} tag(s)
                    @endif
                  </td>
                  <td>
                    <span class="badge {{ $badgeClass }} badge-sm">{{ $state }}</span>
                    @if($isDraft)
                      <br>
                      <a class="link link-hover text-sm" href="{{ $step5Url }}">
                        {{ Lang::txt('Review & Submit') }} &rsaquo;
                      </a>
                    @elseif($submission->published == 3)
                      <br>
                      <a class="link link-hover text-sm" href="{{ $retractUrl }}">
                        &lsaquo; {{ Lang::txt('Retract') }}
                      </a>
                    @endif
                  </td>
                  <td>
                    <a class="btn btn-ghost btn-xs text-error"
                       href="{{ $discardUrl }}"
                       aria-label="{{ Lang::txt('Delete') }}: {{ e($titleText) }}">
                      {{ Lang::txt('Delete') }}
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div role="alert" class="alert alert-info">
          <span>
            <strong>You currently have no contributions in progress.</strong>
            <br><br>
            Once you've started a new contribution, you can proceed at your leisure.
            Stop half-way through and watch a presentation, go to lunch, even close
            the browser and come back a different day! Your contribution will be
            waiting just as you left it, ready to continue at any time.
          </span>
        </div>
      @endif
    </div>
  @endif

  {{-- Before starting --}}
  <div class="mb-8">
    <h2 class="text-xl font-bold mb-4">Before starting</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <h3 class="text-lg font-semibold mb-2">Intellectual Property Considerations</h3>
        <p>
          All materials contributed must have <strong>clearly defined rights and
          privileges</strong>. Online presentations and instructional material are
          normally licensed under
          <a class="link" href="/legal/cc">Creative Commons 4</a>.
          Read <a class="link" href="/legal/licensing">more details</a> about our
          licensing policies.
        </p>
      </div>
      <div>
        <h3 class="text-lg font-semibold mb-2">Questions or concerns?</h3>
        <p>
          We hope that our self-service upload process is intuitive and easy to use.
          If you encounter any problems during the upload process or need assistance
          of any kind, please
          <a class="link" href="/support/ticket/new">file a trouble report</a>.
        </p>
      </div>
    </div>
  </div>

  {{-- Contributable resource types --}}
  @if($categories && $categories->count())
    @php
      $contributable = [];
      foreach ($categories as $cat) {
          if ($cat->get('state') && $cat->get('contributable') == 1) {
              $contributable[] = $cat;
          }
      }
    @endphp

    @if(count($contributable))
      <div>
        <h2 class="text-xl font-bold mb-4">What can I contribute?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($contributable as $category)
            @php
              $catUrl = Route::url(
                  'index.php?option=' . $option
                  . '&task=draft&step=1&type=' . $category->get('id')
              );
            @endphp
            <div class="card bg-base-100 shadow-sm">
              <div class="card-body">
                <h3 class="card-title">
                  <a class="link link-hover" href="{{ $catUrl }}">
                    {{ e(stripslashes($category->get('type'))) }}
                  </a>
                </h3>
                <p class="text-sm text-base-content/70">
                  {{ e(stripslashes($category->description)) }}
                </p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  @endif

</x-page-container>
