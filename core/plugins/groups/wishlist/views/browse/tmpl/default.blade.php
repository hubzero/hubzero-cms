{{--
  Group Wishlist — wish listing with voting, bonus points, and status.

  Variables from plugin:
    $group    — group object
    $wishlist — wishlist object (id, category, referenceid)
    $rows     — collection of wish items
    $items    — total wish count
    $filters  — array with filterby, sortby, tag, limit
    $admin    — admin level (0 = none, 2/3 = admin)
    $config   — component config (banking, etc.)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $title = ($admin)
      ? Lang::txt('COM_WISHLIST_TITLE_PRIORITIZED')
      : Lang::txt('COM_WISHLIST_TITLE_RECENT_WISHES');

  $listUrl = Route::url(
      'index.php?option=com_wishlist&task=wishlist'
      . '&category=' . $wishlist->category
      . '&rid=' . $wishlist->referenceid
  );

  if (count($rows) > 0 && $items > $filters['limit']) {
      $title .= ' <span>(<a class="link link-hover" href="' . $listUrl . '">'
          . Lang::txt('PLG_GROUPS_WISHLIST_VIEW_ALL')
          . ' ' . $items . '</a>)</span>';
  } else {
      $title .= ' <span>(' . $items . ')</span>';
  }

  $returnUrl = Route::url(
      'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=wishlist',
      false,
      true
  );

  $banking = $config->get('banking');
@endphp

{{-- Action bar / warnings --}}
@if ($admin != 0)
  @if ($group->published == 1)
    @php
      $addUrl = Route::url(
          'index.php?option=com_wishlist&task=add'
          . '&category=' . $wishlist->category
          . '&rid=' . $wishlist->referenceid
      );
    @endphp
    <div class="flex justify-end mb-4">
      <a class="btn btn-primary btn-sm" href="{{ $addUrl }}">
        {{ Lang::txt('COM_WISHLIST_ADD_NEW_WISH') }}
      </a>
    </div>
  @endif
@else
  @if (User::isGuest())
    @php
      $loginUrl = Route::url(
          'index.php?option=com_users&view=login&return=' . $returnUrl,
          false
      );
    @endphp
    <div class="alert alert-warning mb-4">
      {!! Lang::txt('PLG_GROUPS_WISHLIST_MUST_LOGIN', $loginUrl) !!}
    </div>
  @else
    <div class="alert alert-warning mb-4">
      {{ Lang::txt('PLG_GROUPS_WISHLIST_MUST_BE_MEMBER') }}
    </div>
  @endif
@endif

<section>
  <div class="overflow-x-auto">
    <table class="table table-zebra w-full">
      <caption class="text-left text-sm mb-2">{!! $title !!}</caption>
      <tbody>
        @if ($rows->count())
          @foreach ($rows as $item)
            @php
              $item->subject = e(stripslashes($item->subject));
              $item->bonus   = $banking ? $item->bonus : 0;

              $isUnrankedAdmin = isset($item->ranked)
                  && !$item->ranked
                  && ($admin == 2 || $admin == 3);

              // Determine status badge
              $statusBadge = null;
              switch ((int) $item->status) {
                  case 1:
                      $statusBadge = ['class' => 'badge-success', 'text' => Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED')];
                      break;
                  case 3:
                      $statusBadge = ['class' => 'badge-error', 'text' => Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED')];
                      break;
                  case 4:
                      $statusBadge = ['class' => 'badge-warning', 'text' => Lang::txt('COM_WISHLIST_WISH_STATUS_WITHDRAWN')];
                      break;
                  case 0:
                  default:
                      if ($item->accepted == 1) {
                          $statusBadge = ['class' => 'badge-info', 'text' => Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED')];
                      } elseif ($isUnrankedAdmin) {
                          $statusBadge = ['class' => 'badge-ghost', 'text' => Lang::txt('COM_WISHLIST_WISH_RANK_THIS')];
                      } else {
                          $statusBadge = ['class' => 'badge-ghost', 'text' => Lang::txt('Pending')];
                      }
                      break;
              }

              // Proposer name
              $name = Lang::txt('JANONYMOUS');
              if (!$item->anonymous) {
                  $proposerUrl = Route::url('index.php?option=com_members&id=' . $item->proposed_by);
                  $name = '<a class="link link-hover" href="' . $proposerUrl . '">'
                      . e($item->proposer->get('name'))
                      . '</a>';
              }

              $wishBase = 'index.php?option=com_wishlist&task=wish'
                  . '&category=' . $wishlist->category
                  . '&rid=' . $wishlist->referenceid
                  . '&wishid=' . $item->id;
              $wishFilters = '&filterby=' . $filters['filterby']
                  . '&sortby=' . $filters['sortby']
                  . '&tags=' . $filters['tag'];
              $wishUrl     = Route::url($wishBase . $wishFilters);
              $commentsUrl = Route::url($wishBase . '&com=1' . $wishFilters . '#comments');
              $bonusUrl    = Route::url($wishBase . '&action=addbonus' . $wishFilters . '#action');

              $proposedTime = Date::of($item->proposed)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
              $proposedDate = Date::of($item->proposed)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
            @endphp
            <tr>
              <td class="w-12 text-base-content/40">{{ $item->id }}</td>
              <td>
                @if ($item->status != 7)
                  <a class="link link-hover font-medium" href="{{ $wishUrl }}">
                    {{ $item->subject }}
                  </a>
                  <div class="text-xs text-base-content/60 mt-0.5">
                    {{ Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY') }}
                    {!! $name !!}
                    <span>{{ Lang::txt('COM_WISHLIST_DATETIME_AT') }}</span>
                    <time datetime="{{ $item->proposed }}">{{ $proposedTime }}</time>
                    <span>{{ Lang::txt('COM_WISHLIST_DATETIME_ON') }}</span>
                    <time datetime="{{ $item->proposed }}">{{ $proposedDate }}</time>
                    &bull;
                    <a class="link link-hover" href="{{ $commentsUrl }}"
                       title="{{ $item->numreplies }} {{ Lang::txt('COM_WISHLIST_COMMENTS') }}">
                      {{ $item->numreplies }} {{ Lang::txt('COM_WISHLIST_COMMENTS') }}
                    </a>
                  </div>
                @else
                  <div class="alert alert-warning text-sm py-1 px-2">
                    {{ Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED') }}
                  </div>
                @endif
              </td>

              {{-- Bonus points column --}}
              @if ($banking)
                <td class="w-24 text-center">
                  @php
                    $hasBonus = isset($item->bonus)
                        && $item->bonus > 0
                        && ($item->status == 0 || $item->status == 6);
                  @endphp
                  @if ($hasBonus)
                    <a class="badge badge-accent badge-sm" href="{{ $bonusUrl }}"
                       title="{{ Lang::txt('COM_WISHLIST_WISH_ADD_BONUS') }}">
                      {{ $item->bonus }} {{ Lang::txt('COM_WISHLIST_POINTS') }}
                    </a>
                  @elseif ($item->status == 0 || $item->status == 6)
                    <a class="badge badge-ghost badge-sm" href="{{ $bonusUrl }}"
                       title="{{ Lang::txt('COM_WISHLIST_WISH_ADD_BONUS') }}">
                      {{ $item->bonus }} {{ Lang::txt('COM_WISHLIST_POINTS') }}
                    </a>
                  @endif
                </td>
              @endif

              {{-- Vote partial --}}
              @if ($item->status != 7)
                <td class="w-20">
                  @php
                    $voteView = new \Hubzero\Component\View([
                        'base_path' => Component::path('com_wishlist') . DS . 'site',
                        'name'      => 'wishlists',
                        'layout'    => '_vote',
                    ]);
                    $voteView->set('option', 'com_wishlist')
                             ->set('item', $item)
                             ->set('listid', $wishlist->id)
                             ->set('plugin', 0)
                             ->set('admin', 0)
                             ->set('page', 'wishlist')
                             ->set('filters', $filters);
                  @endphp
                  {!! $voteView->loadTemplate() !!}
                </td>

                {{-- Ranking / Status --}}
                <td class="w-28">
                  @if ($statusBadge)
                    <span class="badge badge-sm {{ $statusBadge['class'] }}">
                      {{ $statusBadge['text'] }}
                    </span>
                  @endif
                  @if ($item->status == 0 && isset($item->ranked) && $item->ranked && !$isUnrankedAdmin)
                    @php
                      $pct = (($item->ranking / 50) * 100);
                    @endphp
                    <div class="w-full bg-base-200 rounded-full h-1.5 mt-1"
                         title="{{ Lang::txt('COM_WISHLIST_WISH_PRIORITY') }}: {{ $item->ranking }}">
                      <div class="bg-primary h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="{{ $banking ? 5 : 4 }}" class="text-center text-base-content/60">
              {{ Lang::txt('COM_WISHLIST_NO_WISHES_BE_FIRST') }}
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</section>
