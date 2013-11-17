!function ($) {

  $(function(){
    $('select').each(function(idx, el) {
      $('#' + this.id).select2();
    });
  });
}(window.jQuery);
