{{--
  Review form — write or edit a review with star rating.

  Variables (from plugin):
    $option   — string: component option
    $review   — object: review model
    $banking  — bool: whether points banking is enabled
    $infolink — string: banking info link
    $resource — object: resource model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $title = $review->get('id')
      ? Lang::txt('PLG_RESOURCES_REVIEWS_EDIT_YOUR_REVIEW')
      : Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW');

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&id=' . $review->get('resource_id') . '&active=reviews'
  );

  $anon = User::isGuest() ? 1 : 0;

  $ratings = [
      1 => ['stars' => '&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;', 'label' => 'PLG_RESOURCES_REVIEWS_RATING_POOR'],
      2 => ['stars' => '&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;', 'label' => 'PLG_RESOURCES_REVIEWS_RATING_FAIR'],
      3 => ['stars' => '&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;', 'label' => 'PLG_RESOURCES_REVIEWS_RATING_GOOD'],
      4 => ['stars' => '&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;', 'label' => 'PLG_RESOURCES_REVIEWS_RATING_VERY_GOOD'],
      5 => ['stars' => '&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;', 'label' => 'PLG_RESOURCES_REVIEWS_RATING_EXCELLENT'],
  ];
@endphp

<form action="{{ $formAction }}" method="post" id="commentform">
  <section class="below section">
    <h3 id="reviewform-title">{{ $title }}</h3>
    <p class="comment-member-photo">
      <span class="comment-anchor"></span>
      <img src="{{ $review->creator->picture($anon) }}" alt="" />
    </p>
    <fieldset>
      <input type="hidden" name="review[created]" value="{{ $review->get('created') }}" />
      <input type="hidden" name="review[id]" value="{{ $review->get('id') }}" />
      <input type="hidden" name="review[user_id]" value="{{ $review->get('user_id') }}" />
      <input type="hidden" name="review[resource_id]" value="{{ $review->get('resource_id') }}" />
      <input type="hidden" name="review[state]" value="{{ $review->get('state') }}" />
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="task" value="view" />
      <input type="hidden" name="id" value="{{ $review->get('resource_id') }}" />
      <input type="hidden" name="action" value="savereview" />
      <input type="hidden" name="active" value="reviews" />

      {!! Html::input('token') !!}

      @if($banking)
        <p class="help">
          {{ Lang::txt('PLG_RESOURCES_REVIEWS_DID_YOU_KNOW_YOU_CAN') }}
          <a href="{{ $infolink }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_EARN_POINTS') }}</a>
          {{ Lang::txt('PLG_RESOURCES_REVIEWS_FOR_REVIEWS') }}?
          {{ Lang::txt('PLG_RESOURCES_REVIEWS_EARN_POINTS_EXP') }}
        </p>
      @endif

      <fieldset>
        <legend>{{ Lang::txt('PLG_RESOURCES_REVIEWS_FORM_RATING') }}:</legend>
        @foreach($ratings as $val => $rating)
          <label>
            <input class="option"
                   id="review_rating_{{ $val }}"
                   name="review[rating]"
                   type="radio"
                   value="{{ $val }}"
                   {{ $review->get('rating') == $val ? 'checked' : '' }} />
            {!! $rating['stars'] !!}
            {{ Lang::txt($rating['label']) }}
          </label>
        @endforeach
      </fieldset>

      <label for="review_comments">
        {!! Lang::txt('PLG_RESOURCES_REVIEWS_FORM_COMMENTS') !!}
        @if($banking)
          ( <span class="required">{{ Lang::txt('PLG_RESOURCES_REVIEWS_REQUIRED') }}</span>
          {{ Lang::txt('PLG_RESOURCES_REVIEWS_FOR_ELIGIBILITY') }}
          <a href="{{ $infolink }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_EARN_POINTS') }}</a> )
        @endif
        @php
          echo $__view->editor(
              'review[comment]',
              e($review->get('comment')),
              35, 10, 'review_comments',
              ['class' => 'minimal no-footer']
          );
        @endphp
      </label>

      <label id="comment-anonymous-label">
        <input class="option" type="checkbox" name="review[anonymous]"
               id="review-anonymous" value="1"
               {{ $review->get('anonymous') != 0 ? 'checked' : '' }} />
        {{ Lang::txt('PLG_RESOURCES_REVIEWS_FORM_ANONYMOUS') }}
      </label>

      <p class="submit">
        <input type="submit" class="btn btn-success"
               value="{{ Lang::txt('PLG_RESOURCES_REVIEWS_SUBMIT') }}" />
      </p>

      <div class="sidenote">
        <p><strong>{{ Lang::txt('PLG_RESOURCES_REVIEWS_KEEP_POLITE') }}</strong></p>
        <p>{{ Lang::txt('PLG_RESOURCES_REVIEWS_CONTENT_FORMATTING_NOTES') }}</p>
      </div>
    </fieldset>
  </section>
</form>
