/*
 *  Document   : formsWizard.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Forms Wizard page
 */

var FormsWizard = function() {

    return {
        init: function() {
            /*
             *  Jquery Wizard, Check out more examples and documentation at http://www.thecodemine.org
             *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
             */

            /* Set default wizard options */
            var wizardOptions = {
                focusFirstInput: true,
                disableUIStyles: true,
                inDuration: 0,
                outDuration: 0
            };

            /* Initialize Clickable Wizard */
            var clickableWizard = $('#clickable-wizard');

            clickableWizard.formwizard(wizardOptions);

            $('.clickable-steps a').on('click', function(){
                var gotostep = $(this).data('gotostep');

                clickableWizard.formwizard('show', gotostep);
            });

            /* Initialize Progress Wizard */
            $('#progress-wizard').formwizard(wizardOptions);

            // Get the progress bar and change its width when a step is shown
            var progressBar = $('#progress-bar-wizard');
            progressBar
                .css('width', '33%')
                .attr('aria-valuenow', '33');

            $("#progress-wizard").bind('step_shown', function(event, data){
		if (data.currentStep === 'progress-first') {
                    progressBar
                        .css('width', '33%')
                        .attr('aria-valuenow', '33')
                        .removeClass('progress-bar-info progress-bar-success')
                        .addClass('progress-bar-danger');
                }
                else if (data.currentStep === 'progress-second') {
                    progressBar
                        .css('width', '66%')
                        .attr('aria-valuenow', '66')
                        .removeClass('progress-bar-danger progress-bar-success')
                        .addClass('progress-bar-info');
                }
                else if (data.currentStep === 'progress-third') {
                    progressBar
                        .css('width', '100%')
                        .attr('aria-valuenow', '100')
                        .removeClass('progress-bar-danger progress-bar-info')
                        .addClass('progress-bar-success');
                }
            });

            /* Initialize Validation Wizard */
            $('#validation-wizard').formwizard({
                disableUIStyles: true,
                validationEnabled: true,
                validationOptions: {
                    errorClass: 'help-block animation-slideDown', // You can change the animation class for a different entrance animation - check animations page
                    errorElement: 'span',
                    errorPlacement: function(error, e) {
                        e.parents('.form-group > div').append(error);
                    },
                    highlight: function(e) {
                        $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                        $(e).closest('.help-block').remove();
                    },
                    success: function(e) {
                        // You can use the following if you would like to highlight with green color the input after successful validation!
                        e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                        e.closest('.help-block').remove();
                    },
                    rules: {
                        'example-validation-username': {
                            required: true,
                            minlength: 2
                        },
                        'example-validation-password': {
                            required: true,
                            minlength: 5
                        },
                        'example-validation-confirm-password': {
                            required: true,
                            equalTo: '#example-validation-password'
                        },
                        'example-validation-email': {
                            required: true,
                            email: true
                        },
                        'example-validation-terms': {
                            required: true
                        }
                    },
                    messages: {
                        'example-validation-username': {
                            required: 'Please enter a username',
                            minlength: 'Your username must consist of at least 2 characters'
                        },
                        'example-validation-password': {
                            required: 'Please provide a password',
                            minlength: 'Your password must be at least 5 characters long'
                        },
                        'example-validation-confirm-password': {
                            required: 'Please provide a password',
                            minlength: 'Your password must be at least 5 characters long',
                            equalTo: 'Please enter the same password as above'
                        },
                        'example-validation-email': 'Please enter a valid email address',
                        'example-validation-terms': 'Please accept the terms to continue'
                    }
                },
                inDuration: 0,
                outDuration: 0
            });
        }
    };
}();