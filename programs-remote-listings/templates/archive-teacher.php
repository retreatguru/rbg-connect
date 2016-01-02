<?php
/**
 * The template for programs archive
 */

get_header();
$options = get_option('rs_settings');

global $rs_api_vars;
global $RS_Connect;

if (isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>

<h1>Teachers</h1>
<?php
$teachers = array_reverse($RS_Connect->get_teachers($rs_api_vars));

if (! empty($teachers)) {
    foreach ($teachers as $teacher) : ?>
        <div class="rs-teacher rs-group">
            <div class="teacher type-teacher status-publish has-post-thumbnail hentry rs-teacher rs-group">
                <?php if (isset($teacher->photo_details->thumbnail)) { ?>
                    <div class="rs-teacher-thumbnail"><a href="<?php echo get_site_url(); ?>/teacher/<?php echo $teacher->ID . "/" . $teacher->slug; ?>"><img width="150" height="150" src="<?php echo $teacher->photo_details->thumbnail->url; ?>" class="attachment-thumbnail wp-post-image" alt="DavidRome700" /></a></div>
                <?php } ?>
                <h2 class="rs-teacher-title"><a href="<?php echo get_site_url(); ?>/teacher/<?php echo $teacher->ID . "/" . $teacher->slug; ?>"><?php echo $teacher->name; ?></a></h2>
                <p class="rs-teacher-excerpt"><?php echo wp_trim_words($teacher->text, 100); ?></p>
            </div>
        </div>
    <?php endforeach; } else { echo 'Sorry, no teachers exist here.'; } ?>

<?php if (isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>

<?php get_footer(); ?>
