! function ($) {
  
  $('.timepicker').each(function(idx, el) {
    $('#' + el.id).timepicker({
      showMeridian: true
    });
  })
  
  $('[type=time]').each(function(idx, el) {
    $('#' + el.id).timepicker()
  })
  
}(window.jQuery);

