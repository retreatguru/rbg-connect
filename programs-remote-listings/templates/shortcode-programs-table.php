<?php
global $RS_Connect;
global $shortcode_atts;
global $rs_the_programs;
if (is_array($shortcode_atts)) extract($shortcode_atts);
$programs_page_title = $RS_Connect->get_programs_page()->post_title;
?>
<table class="pure-table rs-program rs-group shortcode table">
    <thead>
    <tr>
        <?php if (! empty($show_date)){ ?><th class="rs-dates">Dates</th><?php } ?>
        <?php if (! empty($show_title)){ ?><th class="rs-title"><?php _e($programs_page_title) ?></th><?php } ?>
        <?php if (! empty($extra_display_field)){ ?><th class="rs-custom-field"><?php echo $extra_display_field; ?></th><?php } ?>
        <?php if (! empty($show_teachers)){ ?><th class="rs-teachers">Hosts</th><?php } ?>
        <?php if (! empty($show_location)){ ?><th class="rs-location">Location</th><?php } ?>
        <?php if (! empty($show_price_details)){ ?><th class="rs-price">Price</th><?php } ?>
        <?php if (! empty($show_price_first)){ ?><th class="rs-price-first">Price From</th><?php } ?>
        <?php if (! empty($show_more_link)){ ?><th class="rs-show-more-link" >Details</th><?php } ?>
        <?php if (! empty($show_availability)){ ?><th class="rs-availability">Available Spots</th><?php } ?>
        <?php if (! empty($show_availability_words)){ ?><th class="rs-availability-words">Availability</th><?php } ?>
        <?php if (! empty($show_register_link)){ ?><th class="rs-show-register-link">Register</th><?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($rs_the_programs as $program): ?>
        <tr>
            <?php if (! empty($show_date)){ ?>
                <td class="rs-dates">
                    <?php if (! empty($program->date)) : ?>
                        <?php echo $program->date; ?>
                    <?php endif; ?>
                </td>
            <?php } ?>

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
                <td class="rs-availability">
                    <?php if (! empty($program->registration_spaces_available_words)) : ?>
                        <?php echo $program->registration_spaces_available_words; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if (! empty($show_register_link)) : ?>
                <td class="rs-show-register-link">
                    <?php if ($program->registration_wait_list): ?>
                        <a href="<?php echo $program->registration_link; ?>" target="_blank">Join waiting list</a>
                    <?php elseif ($program->registration_bookable): ?>
                        <a href="<?php echo $program->registration_link; ?>" target="_blank">Register now</a>
                    <?php else: ?>
                        <?php if (empty($program->registration_action)) { echo 'Closed'; } else { echo $program->registration_action; } ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
