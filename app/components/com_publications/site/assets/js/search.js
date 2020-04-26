$(document).ready(function() {
  String.prototype.nohtml = function () {
  	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
  };

  // Remove onchange function for pagination
  $('#limit').removeAttr('onchange');

  var url = '/qubesresources/publications/browse';
  var limit = $('#limit').val();
  var queryParams = '?search=';
  var sortbyParams = '&sortby=date';
  var limitParams = `&limit=${limit}&limitstart=0`;
  var container = $('#results-container');
  var urlToFetch = null;



  $('#resourcesform').on('submit', function(e) {
    e.preventDefault();

    var inputTerms = $('#entry-search-field').val();
    var encodedTerms = encodeURIComponent(inputTerms).replace(/%20/g, '+');
    urlToFetch = `${url}${queryParams}${encodedTerms}${sortbyParams}${limitParams}`;

    console.log(inputTerms);
    console.log(limit);
    console.log(encodedTerms);
    console.log(urlToFetch);

    if (container.length && inputTerms.length) {
      container.load(urlToFetch + ' #results-container', function() {
        container.find('#limit').removeAttr('onchange');
      });
      return urlToFetch;
    }
  });

  $('body').on('change', '#limit', function() {
    console.log('limit change detected');
    var newLimit = $('#limit').val();
    limitParams = `&limit=${newLimit}&limitstart=0`;
    console.log(newLimit);
    console.log(urlToFetch);

    if (urlToFetch === null) {
      urlToFetch = `${url}${queryParams}${sortbyParams}${limitParams}`;
      console.log(urlToFetch);
      container.load(urlToFetch + ' #results-container', function() {
        container.find('#limit').removeAttr('onchange');
      });
    } else {
      urlToFetch = urlToFetch.replace(/limit=.+&limitstart=0/, 'limit=' + newLimit + '&limitstart=0');
      console.log(urlToFetch);
      container.load(urlToFetch + ' #results-container', function() {
        container.find('#limit').removeAttr('onchange');
      });
    }
  });

});
