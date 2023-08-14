<?php
global $RS_Connect;
global $shortcode_atts;
global $rs_the_programs;
if (is_array($shortcode_atts)) extract($shortcode_atts);
$programs_page = $RS_Connect->get_programs_page();
$programs_page_title = $programs_page ? $programs_page->post_title : '';
$teacher_details = ! empty($rs_the_programs[0]) ? $rs_the_programs[0]->teacher_details->teacher_settings : [];
$teacher_word = ! empty($teacher_details->title_plural) ? $teacher_details->title_plural : 'Hosts';
?>
<table class="pure-table rs-program rs-group shortcode table">
    <thead>
    <tr>
        <?php if (! empty($show_date)) : ?>
            <th class="rs-dates"><?php echo is_string($show_date) ? $show_date : 'Dates'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_title)) : ?>
            <th class="rs-title">
            <?php if (is_string($show_title)) : ?>
                <?php echo $show_title; ?>
            <?php else: ?>
                <?php _e($programs_page_title); ?>
            <?php endif; ?>
            </th>
        <?php endif; ?>
            <?php if (! empty($extra_display_field)): ?><th class="rs-custom-field"><?php echo $extra_display_field; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_teachers)) : ?>
            <th class="rs-teachers"><?php echo is_string($show_teachers) ? $show_teachers : $teacher_word; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_location)) : ?>
            <th class="rs-location"><?php echo is_string($show_location) ? $show_location : 'Location'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_price_details)) : ?>
            <th class="rs-price"><?php echo is_string($show_price_details) ? $show_price_details : 'Price'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_price_first)) : ?>
            <th class="rs-price-first"><?php echo is_string($show_price_first) ? $show_price_first : 'Price From'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_more_link)) : ?>
            <th class="rs-show-more-link"><?php echo is_string($show_more_link) ? $show_more_link : 'Details'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_availability)) : ?>
            <th class="rs-availability"><?php echo is_string($show_availability) ? $show_availability : 'Available Spots'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_availability_words)) : ?>
            <th class="rs-availability-words"><?php echo is_string($show_availability_words) ? $show_availability_words : 'Availability'; ?></th>
        <?php endif; ?>
        <?php if (! empty($show_register_link)) : ?>
            <th class="rs-show-register-link"><?php echo is_string($show_register_link) ? $show_register_link : 'Register'; ?></th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($rs_the_programs as $program): ?>
        <tr class="rs-program-<?php echo ! empty($program->slug) ? $program->slug : ''; ?>">
            <?php if (! empty($show_date)): ?>
                <td class="rs-dates">
                    <?php if (! empty($program->date)) : ?>
                        <?php echo $program->date; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_title)) : ?>
                <td class="rs-title">
                    <?php if (! empty($program->title)) : ?>
                        <?php echo $program->title; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($extra_display_field)) : ?>
                <td class="rs-custom-fields">
                    <?php if (! empty($program->custom_fields)) : ?>
                        <?php $custom_fields_array = wp_list_pluck($program->custom_fields, 'value', 'title'); ?>
                        <?php if (! empty($custom_fields_array[$extra_display_field])) : ?>
                            <?php echo $custom_fields_array[$extra_display_field]; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_teachers)) : ?>
                <td class="rs-teachers">
                    <?php if (! empty($program->teacher_list)) : ?>
                        <?php echo $program->teacher_list; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_location)) : ?>
                <td class="rs-location">
                    <?php if (! empty($program->location)) : ?>
                        <?php echo $program->location; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_price_details)) : ?>
                <td class="rs-price">
                    <?php if (! empty($program->price_details)) : ?>
                        <?php
                        // we don't want this stuff in table view
                        $rs_price_output = preg_replace('/<span class="rs-program-label">Price.*<\/span>/', '', $program->price_details);
                        $rs_price_output = preg_replace('/<p class="rs-program-price-note">.*<\/p>/', '', $rs_price_output);
                        echo $rs_price_output;
                        ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_price_first)) : ?>
                <td class="rs-price-first">
                    <?php if (! empty($program->price_first)) : ?>
                        <?php echo $program->price_first; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_more_link)) : ?>
                <td class="rs-show-more-link">
                    <?php $details_url = $program->alternate_url ? $program->alternate_url : $RS_Connect->get_page_url('programs').$program->ID.'/'.$program->slug; ?>
                    <?php echo '<a href="'.$details_url.'">View Details</a>'; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_availability)) : ?>
                <td class="rs-availability">
                        <?php echo $program->registration_spaces_available; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_availability_words)) : ?>
                <td class="rs-availability-words">
                    <?php if (! empty($program->registration_spaces_available_words)) : ?>
                        <?php echo $program->registration_spaces_available_words; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_register_link)) : ?>
                <td class="rs-show-register-link">
                    <?php if ($program->registration_wait_list): ?>
                        <a href="<?php echo $program->registration_link; ?>" target="_blank"><?php echo ! empty($wait_list_text) && is_string($wait_list_text) ? $wait_list_text : ' Join waiting list'; ?></a>
                    <?php elseif ($program->registration_bookable): ?>
                        <a href="<?php echo $program->registration_link; ?>" target="_blank"><?php echo is_string($show_register_link) ? $show_register_link : ' Register Now'; ?></a>
                    <?php else: ?>
                        <?php if (empty($program->registration_action)) { echo 'Closed'; } else { echo $program->registration_action; } ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
