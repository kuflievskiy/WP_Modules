jQuery(document).ready(function( $ ) {
    var deleteImageButton = $('.delete-settings-image-button');

    deleteImageButton.each(function(){
        $(this).on('click', deleteImage);
    });

    function deleteImage() {
        var td = $(this).closest('td');
        td.find('input.delete-settings-image').val('delete');
        td.find('img').remove();
        $(this).remove();
    }
});