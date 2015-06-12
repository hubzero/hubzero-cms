/*
 *  jQuery Form Watcher - v1.0.0
 *
 *  Made by Christopher Smoak
 *  Under LGPLv3 License
 */
;(function( $ ) {
    $.fn.formWatcher = function( options )
    {
        // allow user to specify message
        var forms    = [],
            settings = $.extend({
                message: 'Your changes have not been saved. Are you sure you want to continue?',
                onChange: function(form, difference) {}
            }, options);

        // watch every form
        this.filter("form").each(function()
        {
            var form = $(this);

            // allow message to be overwritten by data attribute
            if (form.attr('data-formwatcher-message'))
            {
                settings.message = form.attr('data-formwatcher-message');
            }

            // attach original form data to form
            form.data('submitting', false);
            form.data('original_data', form.serialize());
            form.data('original_data_array', form.serializeArray());

            // update submitting var to allow form to submit 
            // without throwing onbeforeunload message
            form
                .on('change', function(event)
                {
                    var originalData = form.data('original_data_array'),
                        currentData  = form.serializeArray(),
                        diffs        = [];

                    // get data diff
                    $.each(originalData, function(index, originalField)
                    {
                        $.each(currentData, function(index2, currentField)
                        {
                            if (originalField.name == currentField.name
                                && originalField.value != currentField.value)
                            {
                                var diff = {
                                    field: originalField.name,
                                    oldValue: originalField.value,
                                    newValue: currentField.value
                                }
                                diffs.push(diff);
                            }
                        });
                    });

                    // call on change
                    settings.onChange.call(this, form, diffs);
                })
                .on('submit', function(event)
                {
                    form.data('submitting', true);
                });

            // keep track of all forms
            forms.push(form);
        });

        // if we have forms check all forms for changes
        if (forms.length > 0)
        {
            // stop page reload unless we are submitting
            window.onbeforeunload = function(event)
            {
                var anyChanged = false, anySubmitting = false,
                    anotherFormChangedAndNotBeingSubmitted = false;

                $.each(forms, function(index, form)
                {
                    if (form.serialize() != form.data('original_data'))
                    {
                        anyChanged = true;
                    }
                    if (form.data('submitting'))
                    {
                        anySubmitting = true;
                    }
                    // ther is another form on the page that has been changed 
                    if (form.serialize() != form.data('original_data') && !form.data('submitting'))
                    {
                        anotherFormChangedAndNotBeingSubmitted = true;
                    }
                });

                // thow message if any change & not submitting
                if ((anyChanged && !anySubmitting)
                    || anotherFormChangedAndNotBeingSubmitted)
                {
                    return settings.message;
                }
            }
        }

        return this;
    };
}(jQuery));