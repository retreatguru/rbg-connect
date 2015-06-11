<?php
global $RS_Connect;
if(is_array($shortcode_atts)) extract($shortcode_atts);
?>
<table class="pure-table rs-program rs-group shortcode table">
    <thead>
    <tr>
        <?php if(isset($show_date)){ ?><th class="rs-dates">Dates</th><?php } ?>
        <?php if(isset($show_availability)){ ?><th class="rs-availability">Available Spots</th><?php } ?>
        <?php if(isset($show_title)){ ?><th class="rs-title">Name</th><?php } ?>
        <?php if(isset($show_teachers)){ ?><th class="rs-teachers">Hosts</th><?php } ?>
        <?php if(isset($show_more_link)){ ?><th class="rs-show-more-link" >Details</th><?php } ?>
        <?php if(isset($show_register_link)){ ?><th class="rs-show-register-link">Register</th><?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($rs_the_programs as $program): ?>
        <tr>
            <?php if(isset($show_date)){ ?>
            <td class="rs-dates">
                <?php if ( isset($program->date) ) : ?>
                    <?php echo $program->date; ?>
                <?php endif; ?>
                <?php } ?>

                <?php if(isset($show_availability)) : ?>
            <td class="rs-availability">
                <?php if ( isset($program->date) ) : ?>
                    <?php echo $program->registration_spaces_available; ?>
                <?php endif; ?>
            </td>
        <?php endif; ?>

            <?php if(isset($show_title)) : ?>
                <td class="rs-title">
                    <?php if ( isset($program->title) ) : ?>
                        <?php echo $program->title; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if(isset($show_teachers)) : ?>
                <td class="rs-teachers">
                    <?php if ( isset($program->teacher_details->teacher_objects) ) : ?>
                        <?php echo $program->teacher_details->teacher_list; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if(isset($show_more_link)) : ?>
                <td class="rs-show-more-link">
                    <?php echo '<a href="/'.$RS_Connect->style.'/'.$program->ID.'/'.$program->slug.'">View Details</a>'; ?>
                </td>
            <?php endif; ?>

            <?php if(isset($show_register_link)) : ?>
                <td class="rs-show-register-link">
                    <?php if ($program->registration_bookable): ?>
                        <a href="<?php echo $program->registration_link; ?>" target="_blank">Register Now</a>
                    <?php else: ?>
                        <?php if(empty($program->registration_action)) { echo "Closed"; } else { echo $program->registration_action; } ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
