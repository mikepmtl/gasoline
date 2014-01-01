!function ($) {
  
  $(function(){
    
    /////////////////////////////
    // TOOLTIP
    /////////////////////////////
    $("[data-toggle=tooltip]").tooltip()
    
    
    /////////////////////////////
    // POPOVER
    /////////////////////////////
    $("[data-toggle=popover]").popover()
    
    
    /////////////////////////////
    // DATEPICKER
    /////////////////////////////
    $('.datepicker').each(function(idx, el) {
      $('#' + this.id).datepicker();
    })
    
    
    /////////////////////////////
    // COLORPICKER
    /////////////////////////////
    $('.colorpicker').each(function(idx, el) {
      $('#' + this.id).colorpicker();
    })
    
    
    /////////////////////////////
    // CAROUSEL
    /////////////////////////////
    $('.carousel').carousel()
    
  })
}(window.jQuery)
