{{--
  Wishlist browse — wish items with voting, ranking, and banking.

  Variables (from plugin):
    $option   — string: component option (com_wishlist)
    $resource — object: resource model
    $wishlist — object: wishlist model
    $rows     — collection: wish items
    $title    — string: section title
    $config   — object: wishlist config
    $admin    — int: admin level (0/1/2/3)
    $filters  — array: active filters (filterby, sortby, tag)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css();

  $addUrl = Route::url(
      'index.php?option=' . $option
      . '&task=add&category=' . $wishlist->category
      . '&rid=' . $wishlist->referenceid
  );
@endphp

<h3 class="section-header">{{ Lang::txt('PLG_RESOURCES_WISHLIST') }}</h3>

<div class="container">
  <p class="section-options">
    <a class="icon-add add btn" href="{{ $addUrl }}">
      {{ Lang::txt('PLG_RESOURCES_WISHLIST_ADD_NEW_WISH') }}
    </a>
  </p>

  <table class="ideas entries">
    <caption>{{ $title }}</caption>
    <tbody>
      @if($rows->count())
        @foreach($rows as $item)
          @php
            $item->subject = e(stripslashes($item->subject));
            $item->bonus = $config->get('banking') ? $item->bonus : 0;

            if ($item->status == 7) {
                $status = 'outstanding';
            } elseif (
                isset($item->ranked) && !$item->ranked
                && $item->status != 1 && $item->status != 3 && $item->status != 4
                && ($admin == 2 || $admin == 3)
            ) {
                $status = 'unranked';
            } else {
                $status = 'outstanding';
            }

            $state = (isset($item->ranked) && !$item->ranked
                && $item->status != 1 && ($admin == 2 || $admin == 3))
                ? 'new' : '';
            $state .= ($item->private) ? ' private' : '';
            switch ($item->status) {
                case 3: $state .= ' rejected'; break;
                case 2: break;
                case 1: $state .= ' granted'; break;
                case 0:
                default:
                    $state .= ($item->accepted == 1) ? ' accepted' : ' pending';
                    break;
            }

            $name = Lang::txt('JANONYMOUS');
            if (!$item->anonymous) {
                $name = '<a href="' . Route::url('index.php?option=com_members&id=' . $item->proposed_by)
                    . '">' . e($item->proposer->get('name')) . '</a>';
            }

            $wishBase = 'index.php?option=' . $option
                . '&task=wish&category=' . $wishlist->category
                . '&rid=' . $wishlist->referenceid
                . '&wishid=' . $item->id
                . '&filterby=' . $filters['filterby']
                . '&sortby=' . $filters['sortby']
                . '&tags=' . $filters['tag'];
          @endphp
          <tr class="{{ $state }}">
            <th class="{{ $status }}">
              <span class="entry-id">{{ $item->id }}</span>
            </th>
            <td>
              @if($item->status != 7)
                @php
                  $wishUrl = Route::url($wishBase);
                  $commentsUrl = Route::url($wishBase . '&com=1#comments');
                @endphp
                <a class="entry-title" href="{{ $wishUrl }}">{{ $item->subject }}</a><br />
                <span class="entry-details">
                  {{ Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY') }} {!! $name !!}
                  <span class="entry-time-at">@</span>
                  <span class="entry-time">
                    <time datetime="{{ $item->proposed }}">{{ Date::of($item->proposed)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) }}</time>
                  </span>
                  <span class="entry-date-on">{{ Lang::txt('PLG_RESOURCES_WISHLIST_ON') }}</span>
                  <span class="entry-date">
                    <time datetime="{{ $item->proposed }}">{{ Date::of($item->proposed)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</time>
                  </span>
                  <span class="entry-details-divider">&bull;</span>
                  <span class="entry-comments">
                    <a href="{{ $commentsUrl }}"
                       title="{{ $item->numreplies }} {{ Lang::txt('COM_WISHLIST_COMMENTS') }}">{{ $item->numreplies }}</a>
                  </span>
                </span>
              @else
                <span class="warning adjust">{{ Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED') }}</span>
              @endif
            </td>

            @if($config->get('banking'))
              <td class="reward">
                <span class="entry-reward">
                  @php
                    $bonusUrl = Route::url($wishBase . '&action=addbonus#action');
                    $pointsLabel = Lang::txt('COM_WISHLIST_POINTS');
                    $hasBonus = isset($item->bonus) && $item->bonus > 0
                        && ($item->status == 0 || $item->status == 6);
                  @endphp
                  @if($hasBonus)
                    @php
                      $bonusTitle = Lang::txt('COM_WISHLIST_WISH_ADD_BONUS')
                          . ' :: ' . $item->bonusgivenby
                          . ' ' . Lang::txt('COM_WISHLIST_MULTIPLE_USERS')
                          . ' ' . Lang::txt('COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL')
                          . ' ' . $item->bonus
                          . ' ' . $pointsLabel
                          . ' ' . Lang::txt('COM_WISHLIST_WISH_BONUS_AS_BONUS');
                    @endphp
                    <a class="bonus tooltips" href="{{ $bonusUrl }}"
                       title="{{ $bonusTitle }}">{{ $item->bonus }}
                      <span>{{ $pointsLabel }}</span></a>
                  @elseif($item->status == 0 || $item->status == 6)
                    @php
                      $noBonusTitle = Lang::txt('COM_WISHLIST_WISH_ADD_BONUS')
                          . ' :: ' . Lang::txt('COM_WISHLIST_WISH_BONUS_NO_USERS_CONTRIBUTED');
                    @endphp
                    <a class="nobonus tooltips" href="{{ $bonusUrl }}"
                       title="{{ $noBonusTitle }}">{{ $item->bonus }}
                      <span>{{ $pointsLabel }}</span></a>
                  @else
                    <span class="inactive"
                          title="{{ Lang::txt('COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED') }}">&nbsp;</span>
                  @endif
                </span>
              </td>
            @endif

            @if($item->status != 7)
              <td class="voting">
                @php
                  $view = new \Hubzero\Component\View([
                      'name'      => 'wishlists',
                      'base_path' => Component::path($option) . DS . 'site',
                      'layout'    => '_vote',
                  ]);
                  $view->set('option', 'com_wishlist')
                      ->set('item', $item)
                      ->set('listid', $wishlist->id)
                      ->set('plugin', 0)
                      ->set('admin', 0)
                      ->set('page', 'wishlist')
                      ->set('filters', $filters)
                      ->display();
                @endphp
              </td>
              <td class="ranking">
                @php
                  $html = '';
                  switch ($item->status) {
                      case 0:
                          if (isset($item->ranked) && !$item->ranked && ($admin == 2 || $admin == 3)) {
                              $rankUrl = 'index.php?option=' . $option
                                  . '&task=wish&category=' . $wishlist->category
                                  . '&rid=' . $wishlist->referenceid
                                  . '&wishid=' . $item->id
                                  . '&filterby=' . $filters['filterby']
                                  . '&sortby=' . $filters['sortby']
                                  . '&tags=' . $filters['tag'];
                              $html .= '<a class="rankit" href="' . $rankUrl . '">'
                                  . Lang::txt('COM_WISHLIST_WISH_RANK_THIS') . '</a>';
                          } elseif (isset($item->ranked) && $item->ranked) {
                              $pctWidth = ($item->ranking / 50) * 100;
                              $html .= '<span class="priority-level-base">'
                                  . '<span class="priority-level priority-level' . $item->id . '"'
                                  . ' style="width: ' . $pctWidth . '%"'
                                  . ' data-width="' . $pctWidth . '">'
                                  . '<span>' . Lang::txt('WISH_PRIORITY') . ': ' . $item->ranking . '</span>'
                                  . '</span></span>';
                          }
                          if ($item->accepted == 1) {
                              $html .= '<span class="accepted">'
                                  . Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED') . '</span>';
                          }
                          break;
                      case 1:
                          $html .= '<span class="granted">'
                              . Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED') . '</span>';
                          break;
                      case 3:
                          $html .= '<span class="rejected">'
                              . Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED') . '</span>';
                          break;
                      case 4:
                          $html .= '<span class="withdrawn">'
                              . Lang::txt('COM_WISHLIST_WISH_STATUS_WITHDRAWN') . '</span>';
                          break;
                  }
                  echo $html;
                @endphp
              </td>
            @endif
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>

  <div class="customfields">
    @php
      $data = [];
      preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $resource->fulltxt, $matches, PREG_SET_ORDER);
      foreach ($matches as $match) {
          $data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
      }

      $elements = new \Components\Resources\Models\Elements($data, $resource->type->customFields);
      $schema = $elements->getSchema();
      $tab = Request::getCmd('active', 'wishlist');

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
</div>
