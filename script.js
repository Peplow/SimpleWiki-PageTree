jQuery(document).ready(function(){
    jQuery(document).on('click','.simplewiki-pagetree .open > div',function(e){
        jQuery(this).parent().addClass('closed');
        jQuery(this).parent().removeClass('open');
    });

    jQuery(document).on('click','.simplewiki-pagetree .closed > div',function(e){
        jQuery(this).parent().addClass('open');
        jQuery(this).parent().removeClass('closed');
    });
    
});