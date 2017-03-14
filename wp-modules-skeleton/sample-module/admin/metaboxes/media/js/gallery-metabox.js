jQuery(function($) {

    var file_frame;

    $(document).on('click', '#gallery-metabox a.gallery-add', function(e) {

        e.preventDefault();

        if (file_frame) file_frame.close();

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader-title'),
            button: {
                text: $(this).data('uploader-button-text'),
            },
            multiple: true
        });

        file_frame.on('select', function() {
            var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list li:last')),
                selection = file_frame.state().get('selection');

            selection.map(function(attachment, i) {
                attachment = attachment.toJSON(),
                    index      = listIndex + (i + 1);

                $('#gallery-metabox-list').append('<li><input type="hidden" name="attached[' + index + ']" value="' + attachment.id + '"><img class="image-preview" src="' + attachment.sizes.full.url + '"><small><a class="remove-image" href="#">Remove image</a></small></li>');
            });
        });

        makeSortable();

        file_frame.open();

    });

    function resetIndex() {
        $('#gallery-metabox-list li').each(function(i) {
            $(this).find('input:hidden').attr('name', 'attached[' + i + ']');
        });
    }

    function makeSortable() {
        $('#gallery-metabox-list').sortable({
            opacity: 0.6,
            stop: function() {
                resetIndex();
            }
        });
    }

    $(document).on('click', '#gallery-metabox a.remove-image', function(e) {
        e.preventDefault();

        $(this).parents('li').animate({ opacity: 0 }, 200, function() {
            $(this).find('input:hidden').attr('name', function(i,val){
                return 'attached[delete]['+$(this).val()+']'
            });
            $(this).hide();
        });
    });

    makeSortable();

});