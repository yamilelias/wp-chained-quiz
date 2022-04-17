<?php

add_action('admin_menu', 'register_media_selector_settings_page');

function register_media_selector_settings_page()
{
    add_submenu_page('options-general.php', 'Media Selector', 'Media Selector', 'manage_options', 'media-selector', 'chained_quiz_media_selector_callback');
}

function chained_quiz_media_selector_callback()
{

    // Save attachment ID
    if (isset($_POST['submit_image_selector']) && isset($_POST['image_attachment_id'])) :
        update_option('media_selector_attachment_id', absint($_POST['image_attachment_id']));
    endif;

    ?>
    <div class='image-preview-wrapper'>
        <img id='image-preview' src='' width='100' height='100' style='max-height: 100px; width: 100px;'>
    </div>
    <input id="upload_image_button" type="button" class="button" value="<?php _e('Upload image'); ?>"/>
    <input type='hidden' name='image_attachment_id' id='image_attachment_id' value=''><?php

}

add_action('admin_enqueue_scripts', 'chained_quiz_media_selector_print_scripts');
function chained_quiz_media_selector_print_scripts()
{

    wp_enqueue_media();
    $my_saved_attachment_post_id = get_option('media_selector_attachment_id', 0);

    ?>
    <script type="application/javascript">
        // Fall back to a local copy of jQuery if the CDN fails
        window.jQuery || document.write('<script src="<?php echo CHAINED_URL ?>/js/jquery-3.6.0.min.js"><\/script>');
    </script>
    <script type='text/javascript'>

        jQuery(document).ready(function ($) {

            // Uploading files
            var file_frame;
            var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this

            var buttons = document.querySelectorAll(".button-add-media"); // this element contains more than 1 DOMs.

            // Assign the onclick to every answer button
            buttons.forEach(function (button) {
                console.log(button);
                button.onclick = function (event) {
                    const nameSplit = event.target.name.split('-');
                    console.log(nameSplit, `image_preview-${nameSplit[1]}`, `image_attachment_${nameSplit[1]}`)
                    event.stopPropagation();
                    event.stopImmediatePropagation();
                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if (file_frame) {
                        // Set the post ID to what we want
                        file_frame.uploader.uploader.param('post_id', set_to_post_id);
                        // Open frame
                        file_frame.open();
                        return;
                    } else {
                        // Set the wp.media post id so the uploader grabs the ID we want when initialised
                        wp.media.model.settings.post.id = set_to_post_id;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: 'Select a image to upload',
                        button: {
                            text: 'Use this image',
                        },
                        cid: nameSplit[1],
                        multiple: false	// Set to true to allow multiple files to be selected
                    });

                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        // We set multiple to false so only get one image from the uploader
                        const attachment = file_frame.state().get('selection').first().toJSON();
                        console.log(file_frame);

                        // Do something with attachment.id and/or attachment.url here
                        $(`#image_preview-${nameSplit[1]}`).attr('src', attachment.url).css('width', 'auto').css('display', 'inherit');
                        $(`#image_attachment_${nameSplit[1]}`).val(attachment.id);

                        // Restore the main post ID
                        wp.media.model.settings.post.id = wp_media_post_id;
                    });

                    // Finally, open the modal
                    file_frame.open();
                };
            });
            // jQuery('.button-add-media').on('click', );

            // Restore the main ID when the add media button is pressed
            jQuery('a.add_media').on('click', function () {
                wp.media.model.settings.post.id = wp_media_post_id;
            });
        });

    </script><?php

}
