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
            let file_frame;
            const wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            const set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this

            const buttons = document.querySelectorAll(".button-add-media"); // this element contains more than 1 DOMs.

            // Assign the onclick to every answer button
            buttons.forEach(function (button) {
                button.onclick = function (event) {
                    const imageId = event.target.name.split('-');

                    event.stopPropagation();
                    event.stopImmediatePropagation();
                    event.preventDefault();

                    wp.media.model.settings.post.id = set_to_post_id;

                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: 'Select a image to upload',
                        button: {
                            text: 'Use this image',
                        },
                        cid: imageId[1],
                        multiple: false	// Set to true to allow multiple files to be selected
                    });

                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        // We set multiple to false so only get one image from the uploader
                        const attachment = file_frame.state().get('selection').first().toJSON();

                        // Do something with attachment.id and/or attachment.url here
                        $(`#image_preview-${imageId[1]}`).attr('src', attachment.url).css('width', 'auto').css('display', 'inherit');
                        $(`#image_attachment_id-${imageId[1]}`).attr('value', attachment.url)

                        // Restore the main post ID
                        wp.media.model.settings.post.id = wp_media_post_id;
                    });

                    // Finally, open the modal
                    file_frame.open();
                };
            });

            // Restore the main ID when the add media button is pressed
            jQuery('a.add_media').on('click', function () {
                wp.media.model.settings.post.id = wp_media_post_id;
                file_frame.close()
            });
        });

    </script><?php

}
