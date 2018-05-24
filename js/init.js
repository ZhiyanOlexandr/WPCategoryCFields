jQuery(document).ready(function($) {
    "use strict";


    //Image selector
    $('.wpccf-image-selector').on('click', function() {
        var frame;
        if ( frame ) {
            frame.open();
            return;
        }
        // Create a new media frame
        frame = wp.media({
            library: {
                type: [ 'image' ]
            },
            multiple: false
        });

        frame.on( 'select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#wpccf-category-image').val(attachment.id);
            $('.wpccf-image-selector-preview').html('<img class="wpccf-cat-image" src="" />');
            $('.wpccf-image-selector-preview .wpccf-cat-image').attr('src',attachment.url);
        });

        // Finally, open the modal on click
        frame.open();
    });

    //Remove image from category
    $('.wpccf-image-remove').on('click', function(){
        $('#wpccf-category-image').val('');
        $('.wpccf-image-selector-preview').html('');
    });


    //Video selector
    $('.wpccf-video-selector').on('click', function() {
        var url = $('#wpccf-category-video').val();

        if(url) {
            var val;
            var prefix;
            if(url.search('vimeo') > 0) {
                prefix = "https://player.vimeo.com/video/"
            } else {
                prefix = "https://www.youtube.com/embed/"
            }
            if (url.indexOf("=") > 0) {
                val = url.substring(url.indexOf("=") + 1, url.length);
            } else if(url.lastIndexOf("/") > 0) {
                val = url.substring(url.lastIndexOf("/") + 1, url.length);
            }

            $('.wpccf-video-selector-preview').html('<iframe width="525" height="295" src="' + prefix + val + '" frameborder="0" allowfullscreen></iframe>');
        }
    });

    //Remove video from category
    $('.wpccf-video-remove').on('click', function(){
        $('#wpccf-category-video').val('');
        $('.wpccf-video-selector-preview').html('');
    });

});
