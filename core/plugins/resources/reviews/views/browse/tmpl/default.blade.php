{{--
  Reviews browse — main reviews listing with comment thread and review form.

  Variables (from plugin):
    $option   — string: component option
    $resource — object: resource model
    $reviews  — collection: review objects
    $isAuthor — bool: whether current user is the resource author
    $h        — object: helper with ->myreview
    $banking  — bool: whether points banking is enabled
    $infolink — string: banking info link
    $config   — object: plugin config

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css()->js();
@endphp

<h3 class="section-header">{{ Lang::txt('PLG_RESOURCES_REVIEWS') }}</h3>

<p class="section-options">
  @if(User::isGuest())
    @php
      $reviewUrl = Route::url(
          'index.php?option=' . $option . '&id=' . $resource->id
          . '&active=reviews&action=addreview#commentform'
      );
      $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($reviewUrl));
    @endphp
    <a class="icon-add add btn" href="{{ $loginUrl }}">
      {{ Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW') }}
    </a>
  @elseif(!$isAuthor)
    @php
      $reviewUrl = Route::url(
          'index.php?option=' . $option . '&id=' . $resource->id
          . '&active=reviews&action=addreview#commentform'
      );
    @endphp
    <a class="icon-add add btn" href="{{ $reviewUrl }}">
      {{ Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW') }}
    </a>
  @endif
</p>

@if($__view->getError())
  <p class="warning">{!! implode('<br />', $__view->getErrors()) !!}</p>
@endif

@if($reviews->count() > 0)
  @php
    $__view->view('_list')
        ->set('parent', 0)
        ->set('cls', 'odd')
        ->set('depth', 0)
        ->set('option', $option)
        ->set('resource', $resource)
        ->set('comments', $reviews)
        ->set('config', $config)
        ->set('base', 'index.php?option=' . $option . '&id=' . $resource->id . '&active=reviews')
        ->display();
  @endphp
@else
  <div class="results-none">
    <p>{{ Lang::txt('PLG_RESOURCES_REVIEWS_NO_REVIEWS_FOUND') }}</p>
  </div>
@endif

@if(!User::isGuest())
  @if(isset($h->myreview) && is_object($h->myreview))
    @php
      $__view->view('default', 'review')
          ->set('option', $option)
          ->set('review', $h->myreview)
          ->set('banking', $banking)
          ->set('infolink', $infolink)
          ->set('resource', $resource)
          ->display();
    @endphp
  @endif
@endif

<div class="customfields">
  @php
    $data = [];
    preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $resource->fulltxt, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
    }

    $elements = new \Components\Resources\Models\Elements($data, $resource->type->customFields);
    $schema = $elements->getSchema();
    $tab = Request::getCmd('active', 'reviews');

    if (is_object($schema)) {
        if (!isset($schema->fields) || !is_array($schema->fields)) {
            $schema->fields = [];
        }
        foreach ($schema->fields as $field) {
            if (isset($data[$field->name])
                && $elements->display($field->type, $data[$field->name])
                && isset($field->display)
                && $field->display == $tab
            ) {
                echo '<h4>' . $field->label . '</h4>';
                echo '<div class="resource-content">';
                echo $elements->display($field->type, $data[$field->name]);
                echo '</div>';
            }
        }
    }
  @endphp
</div>
