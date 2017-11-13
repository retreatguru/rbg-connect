<?php
global $RS_Connect;
global $rs_the_teacher;
global $shortcode_atts;
$options = get_option('rs_remote_settings');
?>

<?php
if (is_array($shortcode_atts)) extract($shortcode_atts); ?>

<article class="rs-single rs-teacher">

    <h2 class="rs-title"><?php echo $rs_the_teacher->name; ?></h2>

    <?php if (isset($rs_the_teacher->photo_details->large)) : ?>
        <div class="rs-photo">
            <img src="<?php echo $rs_the_teacher->photo_details->large->url; ?>">
        </div>
    <?php endif; ?>

    <?php if ($rs_the_teacher->text_full) : ?>
        <div class="rs-content">
            <?php echo $rs_the_teacher->text_full; ?>
        </div>
    <?php endif; ?>

    <div class="rs-list rs-program">
        <?php if (! empty($rs_the_teacher->programs)) : ?>
            <div></div>
            <h2 class="rs-title">Events with <?php echo $rs_the_teacher->name; ?></h2>
            <?php foreach($rs_the_teacher->programs as $program) : ?>
                <?php $program_url = $RS_Connect->get_page_url('programs').$program->ID.'/'.$program->slug; ?>
                <div class="rs-item">
                    <h3 class="rs-item-title"><a href="<?php echo $program_url; ?>"><?php echo $program->title; ?></a></h3>
                    <div class="rs-date"><?php echo date('F j, Y', $program->start); ?></div>
                    <?php if(isset($program->photo_details->medium)) : ?>
                        <div class="rs-photo">
                            <a href="<?php echo $program_url; ?>">
                                <img src="<?php echo $program->photo_details->medium->url; ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="rs-content">
                        <div><?php echo $RS_Connect->excerpt($program->text); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</article>
