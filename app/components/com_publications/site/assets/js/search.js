$(document).ready(function() {
  String.prototype.nohtml = function () {
  	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
  };

  var url = '/qubesresources/publications/browse';
  var limit = $('#limit').val();
  var queryParams = '?search=';
  var sortbyParams = '&sortby=date';
  var limitParams = `&limit=${limit}&limitstart=0`;
  var container = $('#results-container');
  var urlToFetch = null;

  // Remove onchange function for pagination
  // const removeOnchange = () => {
  //   $('#limit').removeAttr('onchange');
  // }

  $('#resourcesform').on('submit', function(e) {
    e.preventDefault();

    var inputTerms = $('#entry-search-field').val();
    var encodedTerms = encodeURIComponent(inputTerms).replace(/%20/g, '+');
    urlToFetch = `${url}${queryParams}${encodedTerms}${sortbyParams}${limitParams}`;

    if (container.length && inputTerms.length) {
      container.load(urlToFetch + ' #results-container');
      window.history.pushState({href: urlToFetch}, '', urlToFetch);

      return urlToFetch;
    }
  });

  // $('body').on('change', '#limit', function() {
  //   console.log('limit change detected');
  //   var newLimit = $('#limit').val();
  //   limitParams = `&limit=${newLimit}&limitstart=0`;
  //
  //   if (urlToFetch === null) {
  //     urlToFetch = `${url}${queryParams}${sortbyParams}${limitParams}`;
  //     container.load(urlToFetch + ' #results-container');
  //   } else {
  //     urlToFetch = urlToFetch.replace(/limit=.+&limitstart=0/, 'limit=' + newLimit + '&limitstart=0');
  //     container.load(urlToFetch + ' #results-container');
  //   }
  // });

  $('body').on('click', '.pagination li a', function(e) {
    e.preventDefault();

    var page = $(this).attr('href');

    if (window.location.href.indexOf('browse') > -1) {
      container.load(page + ' #results-container');
    }  else {
      $('#contrib-section').load(page + ' #contrib-section');
    }
  });

});
