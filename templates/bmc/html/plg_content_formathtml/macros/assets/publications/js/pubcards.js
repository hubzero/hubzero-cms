$(document).ready(function(){

  $('.demo-two-card').hover(function(){

      var description = $(this).find('.description'),
          fork = $(this).find('.fork'),
          abstract = $(this).find('.abstract'),
          favorites = $(this).find('.favorites-alt'),
          watch = $(this).find('.fa-eye'),
          share = $(this).find('.fa-share-alt'),
          watchhub = $(this).find('.watch'),
          sharehub = $(this).find('.share-alt'),
          viewRecord = $(this).find('.show-more');

      description.stop().animate({
          height: 'toggle',
          opacity: 'toggle'
      }, 300);

      if (description.prop('scrollHeight') > 150) {
        viewRecord.show();
      }

      if (fork.prop('scrollHeight') > 27) {
        fork.addClass('fade-fork');
      }

      if (abstract.prop('scrollHeight') > 90) {
        abstract.addClass('fade-abstract');
      }
  });

});
