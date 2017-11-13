<?php
global $RS_Connect;
global $shortcode_atts;
global $rs_the_programs;
$options = get_option('rs_remote_settings');

if (is_array($shortcode_atts)) {
    extract($shortcode_atts);
}
?>

<?php if (! empty($rs_the_programs)): ?>

    <div class="rs-list rs-program">
    <?php foreach($rs_the_programs as $program): ?>
        <?php $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'large'; ?>
        <?php $details_url = $program->alternate_url ? $program->alternate_url : $RS_Connect->get_page_url('programs').$program->ID.'/'.$program->slug; ?>

        <div class="rs-item <?php foreach($program->categories as $category) { echo 'rs-program-category-'.$category->slug . ' '; } ?>">
            <?php if ($program->title && empty($hide_title)) : ?>
                <h2 class="rs-title"><a href="<?php echo $details_url; ?>"><?php echo $program->title; ?></a></h2>
            <?php endif; ?>

            <?php if ($program->teacher_list && empty($hide_with_teachers)) : ?>
                <h3 class="rs-with-teachers"><?php echo $program->teacher_list; ?></h3>
            <?php endif; ?>

            <p>
                <?php if ($program->date && empty($hide_date)) : ?>
            <div class="rs-date"><?php echo $program->date; ?></div>
        <?php endif; ?>

            <?php if ($program->location && empty($hide_location)) : ?>
                <div class="rs-location"><?php echo $program->location; ?></div>
            <?php endif; ?>
            </p>

            <?php if ($program->early_bird_discount && empty($hide_discount)) : ?>
                <p class="rs-early-bird-discount rs-highlight"><?php echo $program->early_bird_discount; ?></p>
            <?php endif; ?>

            <?php if ($program->photo_details && empty($hide_photo)) : ?>
                <?php $program_image_url = $program->photo_details->{$image_size}->url; ?>
                <div class="rs-photo"><a href="<?php echo $details_url; ?>"><img src="<?php echo $program_image_url; ?>"></a></div>
            <?php endif; ?>

            <?php if ($program->teacher_details->teacher_objects && ! empty($show_first_teacher_photo)) : ?>
                <?php $teacher_image_url = $program->teacher_details->teacher_objects[0]->photo_details->{$image_size}->url; ?>
                <div class="rs-photo rs-teacher-thumbnail"><a href="<?php echo $details_url; ?>"><img src="<?php echo $teacher_image_url; ?>"></a></div>
            <?php endif; ?>

            <?php if ($program->text && empty($hide_text)) : ?>
                <p class="rs-excerpt"><?php echo $RS_Connect->excerpt($program->text); ?></p>
            <?php endif; ?>

            <?php if ($program->price_first && ! empty($show_first_price)) : ?>
                <p class="rs-first-price">From <?php echo $program->price_first; ?></p>
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
    <?php endforeach; ?>
    </div>
<?php endif; ?>
