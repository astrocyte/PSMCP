/**
 * SST Class Registration - Camera Upload Enhancement
 * Adds camera capture option to file upload fields
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Find file upload fields in our registration form
        var $form = $('.sst-inperson-registration-form, .wpforms-form');

        if (!$form.length) return;

        // Find file upload containers
        $form.find('.wpforms-field-file-upload').each(function() {
            var $field = $(this);
            var $input = $field.find('input[type="file"]');
            var fieldLabel = $field.find('.wpforms-field-label').text().trim();

            // Only enhance OSHA Card and SST Card fields
            if (fieldLabel.indexOf('OSHA') === -1 && fieldLabel.indexOf('SST Card') === -1) {
                return;
            }

            // Add accept attribute for images
            $input.attr('accept', 'image/*,.pdf');

            // Create camera capture UI
            var $wrapper = $('<div class="sst-upload-wrapper"></div>');
            var $options = $('<div class="sst-upload-options"></div>');

            $options.html(
                '<p class="sst-upload-label">Choose how to upload:</p>' +
                '<div class="sst-upload-buttons">' +
                    '<button type="button" class="sst-btn sst-btn-camera">' +
                        '<span class="dashicons dashicons-camera"></span> Take Photo' +
                    '</button>' +
                    '<button type="button" class="sst-btn sst-btn-file">' +
                        '<span class="dashicons dashicons-upload"></span> Choose File' +
                    '</button>' +
                '</div>' +
                '<div class="sst-preview"></div>'
            );

            // Hide original input, wrap with our UI
            $input.hide();
            $field.find('.wpforms-field-description').after($wrapper);
            $wrapper.append($options);

            // Create hidden camera input
            var $cameraInput = $('<input type="file" accept="image/*" capture="environment" style="display:none;" class="sst-camera-input">');
            $wrapper.append($cameraInput);

            // Camera button - opens camera directly on mobile
            $options.find('.sst-btn-camera').on('click', function(e) {
                e.preventDefault();
                $cameraInput.trigger('click');
            });

            // File button - opens regular file picker
            $options.find('.sst-btn-file').on('click', function(e) {
                e.preventDefault();
                $input.trigger('click');
            });

            // Handle camera capture
            $cameraInput.on('change', function() {
                handleFileSelect(this, $field, $input);
            });

            // Handle regular file select
            $input.on('change', function() {
                handleFileSelect(this, $field, null);
            });
        });

        function handleFileSelect(input, $field, $originalInput) {
            var files = input.files;
            if (!files || !files.length) return;

            var file = files[0];
            var $preview = $field.find('.sst-preview');

            // Show preview
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $preview.html(
                        '<div class="sst-preview-item">' +
                            '<img src="' + e.target.result + '" alt="Preview">' +
                            '<span class="sst-preview-name">' + file.name + '</span>' +
                            '<button type="button" class="sst-preview-remove">&times;</button>' +
                        '</div>'
                    );
                };
                reader.readAsDataURL(file);
            } else {
                $preview.html(
                    '<div class="sst-preview-item sst-preview-file">' +
                        '<span class="dashicons dashicons-media-document"></span>' +
                        '<span class="sst-preview-name">' + file.name + '</span>' +
                        '<button type="button" class="sst-preview-remove">&times;</button>' +
                    '</div>'
                );
            }

            // If captured from camera, copy to original input
            if ($originalInput && input.files.length) {
                // Create a DataTransfer to copy files
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                $originalInput[0].files = dataTransfer.files;
            }

            // Remove button handler
            $preview.find('.sst-preview-remove').on('click', function() {
                $preview.empty();
                $field.find('input[type="file"]').val('');
            });
        }
    });

})(jQuery);
