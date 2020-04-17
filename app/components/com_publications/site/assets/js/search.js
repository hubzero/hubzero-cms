$(document).ready(function() {
  String.prototype.nohtml = function () {
  	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
  };

  $('#resourcesform').on('submit', function(e) {
    e.preventDefault();

    var terms = $('#entry-search-field').val();
    var encoded = encodeURIComponent(terms).replace(/%20/g, '+');
    var container = $($(this).attr('data-target'));
    var url = $(this).attr('action') + '?' + 'search=' + encoded;

    console.log(terms);
    console.log(encoded);
    console.log(url);

    if (container.length && terms.length) {
      container.load(url + ' #results-container');
    }
  });

});
