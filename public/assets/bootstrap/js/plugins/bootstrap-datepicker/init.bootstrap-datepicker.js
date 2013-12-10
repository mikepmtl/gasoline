! function ($) {
  
  $('.datepicker').each(function(idx, el) {
    $('#' + el.id).datepicker()
  })
  
  $('[type=date]').each(function(idx, el) {
    $('#' + el.id).datepicker()
  })
  
}(window.jQuery);

