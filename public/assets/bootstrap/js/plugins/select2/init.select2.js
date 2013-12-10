! function ($) {
  
  $('.select2').each(function(idx, el) {
    $('#' + this.id).select2();
  });
  
}(window.jQuery);
