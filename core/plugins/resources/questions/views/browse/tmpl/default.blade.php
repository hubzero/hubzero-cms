{{--
  Questions browse — paginated question list with voting.

  Variables (from plugin):
    $option   — string: component option
    $resource — object: resource model
    $rows     — collection: question objects with pagination
    $count    — int: total question count
    $banking  — bool: points banking enabled
    $infolink — string: banking info link

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $visibleCount = count($rows);
  $limit = Request::getInt('limit') ?: $visibleCount;
  $total = $count;
  $start = $limit > $total ? 1 : Request::getInt('limitstart') + 1;
  $end = ($start + $limit > $total) ? $total : ($start - 1) + $limit;
@endphp

<h3 class="section-header">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS') }}</h3>

<div class="container">
  <p class="section-options">
    <a class="icon-add add btn"
       href="{{ Route::url($resource->link() . '&active=questions&action=new') }}">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION') }}</a>
  </p>

  <table class="questions entries">
    <caption>
      {{ Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS') }}
      <span>({{ Lang::txt('COM_ANSWERS_RESULTS_TOTAL', $start, $end, $total) }})</span>
    </caption>
    <tbody>
      @if($rows)
        @foreach($rows as $row)
          @php
            $name = Lang::txt('JANONYMOUS');
            if (!$row->get('anonymous')) {
                $name = e(stripslashes($row->creator->get('name', $name)));
                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                    $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                }
            }

            $cls  = ($row->get('state') == 1) ? 'answered' : '';
            $cls  = $row->isReported() ? 'flagged' : $cls;
            $cls .= ($row->get('created_by') == User::get('username')) ? ' mine' : '';
          @endphp
          <tr{!! $cls ? ' class="' . $cls . '"' : '' !!}>
            <th>
              <span class="entry-id">{{ $row->get('id') }}</span>
            </th>
            <td>
              @if(!$row->isReported())
                <a class="entry-title"
                   href="{{ Route::url($row->link()) }}">{{ e(strip_tags($row->subject)) }}</a><br />
              @else
                <span class="entry-title">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_QUESTION_UNDER_REVIEW') }}</span><br />
              @endif
              <span class="entry-details">
                {!! Lang::txt('PLG_RESOURCES_QUESTIONS_ASKED_BY', $name) !!}
                <span class="entry-date-at">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_AT') }}</span>
                <span class="entry-time">
                  <time datetime="{{ $row->created() }}">{{ $row->created('time') }}</time>
                </span>
                <span class="entry-date-on">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_ON') }}</span>
                <span class="entry-date">
                  <time datetime="{{ $row->created() }}">{{ $row->created('date') }}</time>
                </span>
                <span class="entry-details-divider">&bull;</span>
                <span class="entry-state">
                  {{ ($row->get('state') == 1) ? Lang::txt('PLG_RESOURCES_QUESTIONS_STATE_CLOSED') : Lang::txt('PLG_RESOURCES_QUESTIONS_STATE_OPEN') }}
                </span>
                <span class="entry-details-divider">&bull;</span>
                <span class="entry-comments">
                  <a href="{{ Route::url($row->link() . '#answers') }}"
                     title="{{ Lang::txt('PLG_RESOURCES_QUESTIONS_NUM_RESPONSES', $row->get('rcount')) }}">{{ $row->responses->count() }}</a>
                </span>
              </span>
            </td>
            @if($banking)
              <td class="reward">
                @if($row->get('reward') == 1)
                  <span class="entry-reward">
                    {{ $row->get('points', 0) }}
                    <a href="{{ $infolink }}"
                       title="{{ Lang::txt('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->get('points', 0)) }}">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_POINTS') }}</a>
                  </span>
                @endif
              </td>
            @endif
            <td class="voting">
              @php
                $helpful = $row->get('helpful', 0);
                $voteClass = ($helpful > 0) ? 'like' : 'neutral';
                $likesTxt = Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_LIKES', $helpful);
                $voteUrl = Route::url(
                    'index.php?option=com_answers&task=vote&id=' . $row->get('id')
                    . '&category=question&vote=yes'
                );
              @endphp
              <span class="vote-like">
                @if(User::isGuest())
                  <span class="vote-button {{ $voteClass }} tooltips"
                        title="{{ Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP_LOGIN') }}">{!! $likesTxt !!}</span>
                @else
                  <a class="vote-button {{ $voteClass }} tooltips"
                     href="{{ $voteUrl }}"
                     title="{{ Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP', $helpful) }}">{!! $likesTxt !!}</a>
                @endif
              </span>
            </td>
          </tr>
        @endforeach
      @else
        <tr class="noresults">
          <td colspan="{{ $banking ? '4' : '3' }}">
            {{ Lang::txt('PLG_RESOURCES_QUESTIONS_NO_QUESTIONS_FOUND') }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>

  <form>
    {!! $rows->pagination !!}
  </form>
  <div class="clearfix"></div>
</div>

<div class="customfields">
  @php
    $data = [];
    preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $resource->fulltxt, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
    }

    $elements = new \Components\Resources\Models\Elements($data, $resource->type->customFields);
    $schema = $elements->getSchema();
    $tab = Request::getCmd('active', 'questions');

    if (is_object($schema)) {
        if (!isset($schema->fields) || !is_array($schema->fields)) {
            $schema->fields = [];
        }
        foreach ($schema->fields as $field) {
            if (isset($data[$field->name])
                && $elements->display($field->type, $data[$field->name])
                && isset($field->display) && $field->display == $tab
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
