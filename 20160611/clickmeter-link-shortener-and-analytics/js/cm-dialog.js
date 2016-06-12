// custom.js
jQuery(function(jQuery) {
    jQuery("#modal-content").dialog({                   
        'dialogClass'   : 'wp-dialog',           
        'modal'         : true,
        'autoOpen'      : false, 
        'closeOnEscape' : true,
        'height': 160, 
        'width': 450,
        'buttons'       : {
            "Close": function() {
                jQuery(this).dialog('close');
            }
        }
    });
    jQuery("#open-modal").click(function(event) {
        event.preventDefault();
        info.dialog('open');
    });
});    