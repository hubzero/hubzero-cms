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
  const removeOnchange = () => {
    $('#limit').removeAttr('onchange');
  }

  removeOnchange();

  $('#resourcesform').on('submit', function(e) {
    e.preventDefault();

    var inputTerms = $('#entry-search-field').val();
    var encodedTerms = encodeURIComponent(inputTerms).replace(/%20/g, '+');
    urlToFetch = `${url}${queryParams}${encodedTerms}${sortbyParams}${limitParams}`;

    if (container.length && inputTerms.length) {
      container.load(urlToFetch + ' #results-container', removeOnchange);
      window.history.pushState({href: urlToFetch}, '', urlToFetch);
      return urlToFetch;
    }
  });

  $('body').on('change', '#limit', function() {
    console.log('limit change detected');
    var newLimit = $('#limit').val();
    limitParams = `&limit=${newLimit}&limitstart=0`;

    if (urlToFetch === null) {
      urlToFetch = `${url}${queryParams}${sortbyParams}${limitParams}`;
      container.load(urlToFetch + ' #results-container', removeOnchange);
      window.history.pushState({href: urlToFetch}, '', urlToFetch);
    } else {
      urlToFetch = urlToFetch.replace(/limit=.+&limitstart=0/, 'limit=' + newLimit + '&limitstart=0');
      container.load(urlToFetch + ' #results-container', removeOnchange);
      window.history.pushState({href: urlToFetch}, '', urlToFetch);
    }
  });

  $('body').on('click', '.pagination li a', function(e) {
    e.preventDefault();

    var page = $(this).attr('href');

    container.load(page + ' #results-container', removeOnchange);
    window.history.pushState({href: page}, '', page);
  });

  window.addEventListener('popstate', function(e) {
    if (e.state) {
      openURL(e.state.href);
    }
  });
});
