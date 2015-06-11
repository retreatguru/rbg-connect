<?php
global $RS_Connect;
if(is_array($shortcode_atts)) extract($shortcode_atts);
?>
<table class="pure-table rs-program rs-group shortcode table">
    <thead>
    <tr>
        <?php if(isset($show_date)){ ?><th>Dates</th><?php } ?>
        <?php if(isset($show_availability)){ ?><th style="text-align: center;">Available Spots</th><?php } ?>
        <?php if(isset($show_title)){ ?><th>Name</th><?php } ?>
        <?php if(isset($show_teachers)){ ?><th>Hosts</th><?php } ?>
        <?php if(isset($show_more_link)){ ?><th style="text-align: center;">Details</th><?php } ?>
        <?php if(isset($show_register_link)){ ?><th style="text-align: center;">Register</th><?php } ?>
    </tr>
    </thead>

    <tbody>

    <?php foreach($rs_the_programs as $program): ?>

        <tr>

            <?php if(isset($show_date)){ ?>
            <td>
                <?php if ( isset($program->date) ) : ?>
                    <?php echo $program->date; ?>
                <?php endif; ?>
                <?php } ?>

                <?php if(isset($show_availability)) : ?>
            <td class="availability" style="text-align: center;">
                <?php if ( isset($program->date) ) : ?>
                    <?php echo $program->registration_spaces_available; ?>
                <?php endif; ?>
            </td>
        <?php endif; ?>

            <?php if(isset($show_title)) : ?>
                <td class="title">
                    <?php if ( isset($program->title) ) : ?>
                        <?php echo $program->title; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if(isset($show_teachers)) : ?>
                <td class="teachers" style="text-align: left;">
                    <?php if ( isset($program->teacher_details->teacher_objects) ) : ?>
                        <?php echo $program->teacher_details->teacher_list; ?>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if(isset($show_more_link)) : ?>
                <td class="show-more-link" style="text-align: center;">
                    <?php echo '<a href="/'.$RS_Connect->style.'/'.$program->ID.'/'.$program->slug.'">View Details</a>'; ?>
                </td>
            <?php endif; ?>

            <?php if(isset($show_register_link)) : ?>
                <td class="status" style="text-align: center;">
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
