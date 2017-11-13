<?php
global $RS_Connect;
global $rs_the_teachers;
global $shortcode_atts;
$options = get_option('rs_remote_settings');
if (is_array($shortcode_atts)) extract($shortcode_atts);
?>

<?php if (! empty($rs_the_teachers)): ?>
    <div class="rs-list rs-teacher">
    <?php foreach($rs_the_teachers as $teacher): ?>
        <?php $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium'; ?>
        <?php $details_url = $RS_Connect->get_page_url('teachers').$teacher->ID.'/'.$teacher->slug; ?>
        <div class="rs-item">


            <?php if (isset($teacher->photo_details->{$image_size})) : ?>
                <div class="rs-photo">
                    <img src="<?php echo $teacher->photo_details->{$image_size}->url; ?>">
                </div>
            <?php endif; ?>

            <?php if ($teacher->text) : ?>
                <div class="rs-content">
                    <h2 class="rs-title"><a href="<?php echo $details_url; ?>"><?php echo $teacher->name; ?></a></h2>
                    <div><?php echo $teacher->text; ?></div>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
