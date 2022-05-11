<div class="chained-quiz-answer">
    <div class="chained-quiz-choice-columns">
        <?php
        // Save attachment ID
        if (isset($_POST['submit_image_selector']) && isset($_POST['image_attachment_id'])) :
            update_option('media_selector_attachment_id', absint($_POST['image_attachment_id']));
        endif;

        ?>
        <div class="image-selector-question">
            <div class='image-preview-wrapper'>
                <img alt="<?php echo empty($choice->id) ? 'image_preview[]' : 'image_preview-' . $choice->id ?>"
                     id="<?php echo empty($choice->id) ? 'image_preview[]' : 'image_preview-' . $choice->id ?>" class='image-preview'
                     src='<?php echo $choice->image ?>'
                     style='max-width: 100px; display: <?php echo empty($choice->image) ? 'none' : 'inherit' ?>;'>
            </div>
            <input name="<?php echo empty($choice->id) ? 'upload_image[]-' : 'upload_image-' . $choice->id ?>"
                   id="<?php echo 'upload_image_button-' . $choice->id ?>" type="button" class="button button-add-media"
                   value="<?php _e('Upload image'); ?>"/>
            <input type='hidden'
                   name='<?php echo empty($choice->id) ? 'image_attachment[]-' : 'image_attachment-' . $choice->id ?>'
                   id='<?php echo empty($choice->id) ? 'image_attachment[]-' : 'image_attachment_id-' . $choice->id ?>' value='<?php echo !empty($choice->image) ?  $choice->image : '' ?>'>
        </div>
        <div>
        <textarea rows="3" cols="40" class="chained-quiz-answer-textarea"
                  name="<?php echo empty($choice->id) ? 'answers[]' : 'answer' . $choice->id ?>"><?php echo stripslashes(@$choice->choice) ?></textarea>
        </div>
        <div class="chained-quiz-choice-options">
            <div>
                <?php _e('Points:', 'chained') ?> <input type="text" size="4"
                                                         name="<?php echo empty($choice->id) ? 'points[]' : 'points' . $choice->id ?>"
                                                         value="<?php echo @$choice->points ?>">
                <?php if (!empty($choice->id)): ?>
                    <input type="checkbox" name="dels[]"
                           value="<?php echo $choice->id ?>"> <?php _e('Delete this choice', 'chained') ?>
                <?php endif; ?>
            </div>
            <input type="checkbox" name="<?php echo empty($choice->id) ? 'is_correct[]' : 'is_correct' . $choice->id ?>"
                   value="1" <?php if (!empty($choice->is_correct)) echo 'checked' ?>> <?php _e('Correct answer', 'chained') ?>
            | <?php _e('When selected go to:', 'chained') ?> <select
                    name="<?php echo empty($choice->id) ? 'goto[]' : 'goto' . $choice->id ?>">
                <option value="next"><?php _e('Next question', 'chained') ?></option>
                <option value="finalize" <?php if (!empty($choice->goto) and $choice->goto == 'finalize') echo 'selected' ?>>
                    <?php _e('Finalize quiz', 'chained') ?></option>
                <?php if (count($other_questions)): ?>
                    <option disabled><?php _e('- Select question -', 'chained') ?></option>
                    <?php foreach ($other_questions as $other_question): ?>
                        <option value="<?php echo $other_question->id ?>" <?php if (!empty($choice->id) and $choice->goto == $other_question->id) echo 'selected' ?>><?php echo stripslashes($other_question->title);
                            if (!empty($choice->question_id) and $other_question->id == $choice->question_id) echo ' ' . __('(same question)', 'chained'); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
</div>