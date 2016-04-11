<?php
$rs_programs = RS_Connect_Api::get_programs();
?>

<table class="wp-list-table widefat fixed posts">
    <thead>
    <tr>
        <th width="300">Program</th>
        <th width="100">Dates</th>
        <th width="100">Registration</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ((array) $rs_programs as $program): ?>
        <?php $details_url = $program->alternate_url ? $program->alternate_url : get_site_url().'/'.$this->style.'/'.$program->ID.'/'.$program->slug; ?>
        <tr>
            <td>
                <a href="<?php echo RS_Connect_Api::get_base_url(); ?>/wp-admin/post.php?action=edit&post=<?php echo $program->ID; ?>"><?php echo $program->title; ?></a>
                - <a href="<?php echo $details_url; ?>">view</a></td>
            <td><?php echo $program->date; ?></td>
            <td><?php echo ucfirst($program->registration_status); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>