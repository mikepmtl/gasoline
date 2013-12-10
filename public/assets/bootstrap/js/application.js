! function ($) {
  
  $('[rel=tooltip]').tooltip()
  
  $("[data-toggle=popover]")
    .popover()
  
  $(".grading-popover").popover({
    title: function() {
      console.log($(this).data('title'));
      
      return ( $(this).parent().find('.popover-heading').html() !== undefined ? $(this).parent().find('.popover-heading').html() : $(this).data('title') );
    },
    content: function() {
      return ( $(this).parent().find('.popover-body').html() !== undefined ? $(this).parent().find('.popover-body').html() : $(this).data('content') );
    }
  })
    
  $('[data-toggle=class]').hover(function(e) {    
    if ( $(this).data('classChange') ) {
      $(this).toggleClass($(this).data('classChange'))
    }
  })
  
}(window.jQuery);
