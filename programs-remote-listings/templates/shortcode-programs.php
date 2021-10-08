<?php
global $RS_Connect;
global $shortcode_atts;
global $rs_the_programs;
$options = get_option('rs_remote_settings');

if (is_array($shortcode_atts)) {
    extract($shortcode_atts);
}

if (! empty($rs_the_programs)) {

    foreach($rs_the_programs as $program): ?>
        <?php
        $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium';
        $details_url = ! empty($program->alternate_url) ? $program->alternate_url : $RS_Connect->get_page_url('programs').$program->ID.'/'.$program->slug;
        $featured_image = $program->photo_details->{$image_size} ?? null;
        $featured_teacher = $program->teacher_details->teacher_objects[0] ?? null;
        $teacher_image = $featured_teacher->photo_details->{$image_size} ?? null;
        ?>

        <?php // todo: the program categories should be appended with rs-program-category- ?>
        <div class="rs-program rs-group <?php foreach($program->categories as $category) {echo $category->slug . " ";} ?>"
             id="rs-program-id-<?php echo $program->ID; ?>">
            <?php if ($featured_image && empty($hide_photo)) : ?>
                <div class="rs-program-thumbnail">
                    <a href="<?php echo $details_url; ?>"><img
                                src="<?php echo $featured_image->url ?? ''; ?>"
                                alt="<?php echo $featured_image->alt ?? 'Program featured image'; ?>"></a>
                </div>
            <?php endif; ?>

            <div class="rs-program-content-wrap">
                <?php if ($teacher_image && ! empty($show_first_teacher_photo)) : ?>
                    <div class="rs-teacher-thumbnail">
                        <a href="<?php echo $details_url; ?>"><img
                                    src="<?php echo $teacher_image->url ?? ''; ?>"
                                    alt="<?php echo $teacher_image->alt ?? 'Teacher profile image'; ?>"></a>
                    </div>
                <?php endif; ?>
                <?php if (! empty($program->title) && empty($hide_title)) : ?>
                    <h3 class="rs-program-title"><a href="<?php echo $details_url; ?>"><?php echo $program->title; ?></a></h3>
                <?php endif; ?>

                <?php if (! empty($program->teacher_list) && empty($hide_with_teachers)) : ?>
                    <h3 class="rs-program-with-teachers"><?php echo $program->teacher_list; ?></h3>
                <?php endif; ?>

                <?php if (! empty($program->date) && empty($hide_date)) : ?>
                    <div class="rs-program-date"><?php echo $program->date; ?></div>
                <?php endif; ?>

                <?php if (! empty($program->location) && empty($hide_location)) : ?>
                    <div class="rs-program-location"><?php echo $program->location; ?></div>
                <?php endif; ?>

                <?php if (! empty($program->early_bird_discount) && empty($hide_discount)) : ?>
                    <div class="rs-program-early-bird-discount rs-highlight"><?php echo $program->early_bird_discount; ?></div>
                <?php endif; ?>

                <?php if (! empty($program->text) && empty($hide_text)) : ?>
                    <div class="rs-program-excerpt"><?php echo $RS_Connect->excerpt($program->text); ?></div>
                <?php endif; ?>

                <?php if (! empty($program->price_first) && ! empty($show_first_price)) : ?>
                    <div class="rs-program-first-price">From <?php echo $program->price_first; ?></div>
                <?php endif; ?>

                <?php if (! empty($show_price_details) && ! empty($program->price_details)) : ?>
                    <div class="rs-program-price-details"><?php echo $program->price_details; ?></div>
                <?php endif; ?>

                <?php if (! empty($show_availability) && ! empty($program->registration_spaces_available)) : ?>
                    <div class="rs-availability"><?php echo is_string($show_availability) ? $show_availability : 'Spaces'; ?>
                        <span class="rs-availability-number"> <?php echo $program->registration_spaces_available; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (! empty($show_availability_words) && ! empty($program->registration_spaces_available_words)) : ?>
                    <div class="rs-availability-words"><?php echo is_string($show_availability_words) ? $show_availability_words : 'Availability'; ?>
                        <span class="rs-availability-words-value"><?php echo $program->registration_spaces_available_words; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (! empty($show_more_link)) : ?>
                    <a class="rs-program-see-more-link" href="<?php echo $details_url; ?>" target="_blank"><?php echo is_string($show_more_link) ? $show_more_link : ' see more...'; ?></a><br>
                <?php endif; ?>

                <?php if (! empty($show_register_link)) : ?>
                    <?php
                    // todo: this is messy. why check for bookable and then also for action. and why insert 'closed' we should get most everything here from api
                    // todo: identical logic is also duplicated in table view
                    ?>
                    <?php if (! empty($program->registration_wait_list)): ?>
                        <a class="rs-program-register-link rs-program-wait-list-link" href="<?php echo $program->registration_link; ?>"
                           target="_blank"><?php echo ! empty($wait_list_text) && is_string($wait_list_text) ? $wait_list_text : ' Join waiting list'; ?></a>
                    <?php elseif (! empty($program->registration_bookable)): ?>
                        <a class="rs-program-register-link" href="<?php echo $program->registration_link; ?>" target="_blank"><?php echo is_string($show_register_link) ? $show_register_link : ' Register Now'; ?></a>
                    <?php else: ?>
                        <?php if (empty($program->registration_action)) { echo 'Closed'; } else { echo $program->registration_action; } ?>
                    <?php endif; ?>
                <?php endif; ?>

            </div> <!-- rs-program-content-wrap -->
        </div>

    <?php endforeach;
} else {
    echo 'Sorry, no programs exist here.';
}
