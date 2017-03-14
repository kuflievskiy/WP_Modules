jQuery(document).ready(function ($) {

    var EmailQueueController = function () {
        this.settings = {};

        this.init = function(settings) {
            if (typeof settings != 'undefined' && settings) {
                // Merge object2 into object1
                $.extend(this.settings, settings);
            }

            this.initFormHandler();
        };

        this.initFormHandler = function() {
            $(this.settings.statusFieldSelector).on('change',function(event){
                event.preventDefault();
                $.post(ajaxurl,{
                    'action':'update_email_queue_item',
                    'data': {'status':$(this).val(),'id':$(this).data('id')}
                },function (response) {
                    alert(response.data.message);
                });
            });
        };
    };

    var emailQueueController = new EmailQueueController();

    emailQueueController.init({
        'statusFieldSelector':'select.status'
    });
});