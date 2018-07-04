<?php
global $RS_Connect;
global $rs_the_teacher;
global $shortcode_atts;
$options = get_option('rs_remote_settings');
?>

<?php
if (is_array($shortcode_atts)) extract($shortcode_atts); ?>

<article class="page type-page status-publish entry hentry single-teacher"
         id="rs-single-teacher-id-<?php echo $rs_the_teacher->ID; ?>">
    <header class="entry-header">
        <h1 class="rs-program-title"><?php echo $rs_the_teacher->name; ?></h1>
    </header>

    <div class="entry-content">
        <?php // Program Details ?>
        <div class="rs-teacher-content" style="padding:20px;">
            <?php if (isset($rs_the_teacher->photo_details->medium)) : ?>
                <img src="<?php echo $rs_the_teacher->photo_details->medium->url; ?>" class="alignleft" style="padding:0 20px 20px 0px; float: left;">
            <?php endif; ?>

            <?php if ($rs_the_teacher->text_full) : ?>
                <div class="rs-teacher-custom-wrap"><?php echo $rs_the_teacher->text_full; ?></div>
            <?php endif; ?>
        </div>
        <div class="rs-teacher-programs" style="clear: left; margin:20px;">
            <?php if (! empty($rs_the_teacher->programs)) : ?>
                <h3 style="margin-top: 30px;">Events with <?php echo $rs_the_teacher->name; ?></h3>

                <?php foreach($rs_the_teacher->programs as $program) : ?>
                    <?php $program_url = $RS_Connect->get_page_url('programs').$RS_Connect->get_single_page_url($program); ?>
                    <div class="program" style="float:left; clear:left;">
                        <?php if(! empty($program->photo_details->thumbnail->url)) : ?>
                        <a href="<?php echo $program_url; ?>"><img src="<?php echo $program->photo_details->thumbnail->url; ?>" style="float:left; margin:5px 15px 15px 0;"></a>
                        <?php endif; ?>
                        <strong><a href="<?php echo $program_url; ?>"><?php echo $program->title; ?></a></strong><br/>
                        <?php echo date('F j, Y', $program->start); ?>
                        <p><?php echo $RS_Connect->excerpt($program->text); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="clearfix"></div>
    </div>

</article>
