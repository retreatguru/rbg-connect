<table class="pure-table">
    <thead>
        <tr>
            <th>Start Date - End Date</th>
            <th>Available Spots</th>
            <th>Availability</th>
        </tr>
    </thead>

    <tbody>

        <?php global $rs_the_programs; foreach($rs_the_programs as $program): ?>

            <tr class="rs-program rs-group shortcode">

                <td>
                <?php if ( $program->date ) : ?>
                   <?php echo $program->date; ?>
                <?php endif; ?>
                </td>

                <td class="availability">
                <?php echo $program->registration_spaces_available; ?>
                </td>

                <td class="status">
                    <?php if ($program->registration_bookable): ?>
                        <a href="<?php echo $program->registration_link; ?>" target="_blank">Register Now</a>
                    <?php else: ?>
                      <?php if(empty($program->registration_action)) { echo "Closed"; } else { echo $program->registration_action; } ?>
                    <?php endif; ?>
                </td>

            </tr>

        <? endforeach; ?>

    </tbody>
</table>
