<?php
global $RS_Connect;
// Maybe we set and get these as class properties
global $shortcode_atts;
global $rs_the_program;
$options = get_option('rs_remote_settings');
?>

<?php
if (is_array($shortcode_atts)) extract($shortcode_atts); ?>

<article class="rs-single rs-program">

    <h2 class="rs-title"><?php echo $rs_the_program->title; ?></h2>

    <?php if (! empty($rs_the_program->teacher_list)) : ?>
        <h3 class="rs-subtitle"><?php echo $rs_the_program->teacher_list; ?></h3>
    <?php endif; ?>

    <p class="rs-date"><?php echo $rs_the_program->date; ?></p>

    <div class="rs-action"><?php echo $rs_the_program->registration_action; ?></div>

    <div class="rs-metabox alignright">
        <?php  if ($rs_the_program->photo_details) : ?>
            <div class="rs-photo">
                <img src="<?php echo $rs_the_program->photo_details->large->url; ?>">
            </div>
        <?php endif; ?>

        <div class="rs-metabox-details">

            <?php if ($rs_the_program->early_bird_discount && empty($hide_discount)) : ?>
                <div class="rs-early-bird-discount rs-highlight"><?php echo $rs_the_program->early_bird_discount; ?></div>
            <?php endif; ?>

            <?php // Pricing ?>
            <?php if ($rs_the_program->price_details) : ?>
                <div class="rs-price"><?php echo $rs_the_program->price_details ?></div>
            <?php endif; ?>

            <?php // Datetime details ?>
            <?php if ($rs_the_program->date_time) : ?>
                <div class="rs-datetime"><span class="rs-program-label">Date &amp; Time Details:</span> <?php echo $rs_the_program->date_time; ?></div>
            <?php endif; ?>

            <?php // Location ?>
            <?php if ($rs_the_program->location) : ?>
                <div class="rs-location"><span class="rs-program-label">Location:</span> <?php echo $rs_the_program->location; ?></div>
            <?php endif; ?>

            <?php // Address ?>
            <?php if ($rs_the_program->address) : ?>
                <div class="rs-address"><span class="rs-program-label">Address:</span> <?php echo $rs_the_program->address; ?></div>
            <?php endif; ?>

            <?php // Contact details ?>
            <?php if ($rs_the_program->contact) : ?>
                <div class="rs-contact"><?php echo $rs_the_program->contact ?></div>
            <?php endif; ?>

            <?php // Custom fields ?>
            <?php if ($rs_the_program->custom) : ?>
                <div class="rs-custom-fields"><?php echo $rs_the_program->custom; ?></div>
            <?php endif; ?>

            <?php if ($rs_the_program->email && empty($options['rs_template']['hide_contact_button'])) : ?>
                <?php if (! empty($options['rs_template']['contact_button_text'])) {
                    $contact_button_text = $options['rs_template']['contact_button_text'];
                } else {
                    $contact_button_text = 'Email us about program';
                } ?>
                <a href="mailto:<?php echo $rs_the_program->email; ?>?subject=An inquiry about <?php echo $rs_the_program->title; ?>" class="rs-button"><?php echo $contact_button_text; ?></a>
            <?php endif; ?>

            <div class="rs-action"><?php echo $rs_the_program->registration_action; ?></div>
        </div>
    </div>

    <?php // Program Content ?>
    <?php if ($rs_the_program->text_full) : ?>
    <div class="rs-content">
        <?php echo $rs_the_program->text_full; ?>
    </div>
    <?php endif; ?>


    <?php // Teachers ?>
    <?php if ($rs_the_program->teacher_details->teacher_objects) : ?>
        <div class="rs-small-list rs-teacher">
            <h2 class="rs-title"><?php echo _n('Teacher', 'Teachers',
                    count($rs_the_program->teacher_details->teacher_objects)) ?></h2>

            <?php foreach ($rs_the_program->teacher_details->teacher_objects as $teacher) : ?>
                <?php $teacher_url = $RS_Connect->get_page_url('teachers') . $teacher->ID . '/' . $teacher->slug; ?>
                <div class="rs-item">
                    <h3 class="rs-item-title"><a href="<?php echo $teacher_url; ?>"><?php echo $teacher->name; ?></a></h3>
                    <?php if (isset($teacher->photo_details->medium)) { ?>
                        <div class="rs-photo">
                            <a href="<?php echo $teacher_url; ?>">
                                <img class="alignleft" src="<?php echo $teacher->photo_details->medium->url; ?>">
                            </a>
                        </div>
                    <?php } ?>
                    <div class="rs-content">
                        <div><?php echo $RS_Connect->excerpt($teacher->text); ?></div>
                        <div><a href="<?php echo $teacher_url; ?>">Learn more about <?php echo $teacher->name; ?></a></div>
                    </div>
                </div>
                <br/>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="clear:both;"></div>

</article>
