(function() {
  $('#tabNodetype a').click(function(e) {
    e.preventDefault();
    return $(this).tab('show');
  });

  $('.reload-page').click(function() {
    return window.location.reload();
  });

}).call(this);
