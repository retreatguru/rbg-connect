<?php
global $RS_Connect;
global $shortcode_atts;
global $rs_the_programs;
$options = get_option('rs_remote_settings');

if (is_array($shortcode_atts)) {
    extract($shortcode_atts);
}

if (! empty($rs_the_programs)) {

    foreach($rs_the_programs as $program):
        $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium';
        $details_url = $program->alternate_url ? $program->alternate_url : $RS_Connect->get_page_url('programs').$program->ID.'/'.$program->slug; ?>

        <?php // todo: the program categories should be appended with rs-program-category- ?>
        <div class="rs-program rs-group <?php foreach($program->categories as $category) {echo $category->slug . " ";} ?>">
            <?php if ($program->photo_details && empty($hide_photo)) : ?>
                <?php $program_image_url = $program->photo_details->{$image_size}->url; ?>
                <div class="rs-program-thumbnail"><a href="<?php echo $details_url; ?>"><img src="<?php echo $program_image_url; ?>"></a></div>
            <?php endif; ?>

            <?php if ($program->teacher_details->teacher_objects && ! empty($show_first_teacher_photo)) : ?>
                <?php $teacher_image_url = $program->teacher_details->teacher_objects[0]->photo_details->{$image_size}->url; ?>
                <div class="rs-teacher-thumbnail"><a href="<?php echo $details_url; ?>"><img src="<?php echo $teacher_image_url; ?>"></a></div>
            <?php endif; ?>

            <?php if ($program->title && empty($hide_title)) : ?>
                <h3 class="rs-program-title"><a href="<?php echo $details_url; ?>"><?php echo $program->title; ?></a></h3>
            <?php endif; ?>

            <?php if ($program->teacher_list && empty($hide_with_teachers)) : ?>
                <h3 class="rs-program-with-teachers"><?php echo $program->teacher_list; ?></h3>
            <?php endif; ?>

            <?php if ($program->date && empty($hide_date)) : ?>
                <div class="rs-program-date"><?php echo $program->date; ?></div>
            <?php endif; ?>

            <?php if ($program->location && empty($hide_location)) : ?>
                <div class="rs-program-location"><?php echo $program->location; ?></div>
            <?php endif; ?>

            <?php if ($program->early_bird_discount && empty($hide_discount)) : ?>
                <div class="rs-program-early-bird-discount rs-highlight"><?php echo $program->early_bird_discount; ?></div>
            <?php endif; ?>

            <?php if ($program->text && empty($hide_text)) : ?>
                <div class="rs-program-excerpt"><?php echo $RS_Connect->excerpt($program->text); ?></div>
            <?php endif; ?>

            <?php if ($program->price_first && ! empty($show_first_price)) : ?>
                <div class="rs-program-first-price">From <?php echo $program->price_first; ?></div>
            <?php endif; ?>

            <?php if (! empty($show_register_link)) : ?>
                <?php
                // todo: this is messy. why check for bookable and then also for action. and why insert 'closed' we should get most everything here from api
                // todo: identical logic is also duplicated in table view
                ?>
                <?php if ($program->registration_wait_list): ?>
                    <a href="<?php echo $program->registration_link; ?>" target="_blank">Join waiting list</a>
                <?php elseif ($program->registration_bookable): ?>
                    <a href="<?php echo $program->registration_link; ?>" target="_blank">Register Now</a>
                <?php else: ?>
                    <?php if (empty($program->registration_action)) { echo 'Closed'; } else { echo $program->registration_action; } ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    <?php endforeach;
} else {
    echo 'Sorry, no programs exist here.';
}
