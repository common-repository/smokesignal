
jQuery.noConflict();
jQuery(document).ready(function ($) {
    jQuery('#smokesignal_add_attachment').click(function() {
        tb_show('', 'media-upload.php?type=image&TB_iframe=false');
        return false;
    });

    window.send_to_editor = function(html) {
        var link = jQuery(html).attr('href');
        tb_remove();

        jQuery('#message').val(jQuery('#message').val() + link);
    }
});