<?php
if (isset($_GET['edit'])) {
  include plugin_dir_path(__FILE__) . 'wcfm-view-videos-manage.php';
  return;
}
?>

<div class="collapse wcfm-collapse" id="wcfm_videos_listing">

  <div class="wcfm-page-headig">
    <span class="wcfmfa fa-video"></span>
    <span class="wcfm-page-heading-text"><?php _e('Videos', 'wcfm'); ?></span>
    <?php do_action('wcfm_page_heading'); ?>
  </div>

  <div class="wcfm-collapse-content">
    <div class="wcfm-top-element-container">
      <a class="add_new_wcfm_ele_dashboard text_tip" href="<?php echo add_query_arg('edit', 'new', wcfm_get_endpoint_url('video')); ?>" data-tip="<?php _e('Add New Video', 'wcfm'); ?>">
        <span class="wcfmfa fa-video"></span>
        <span class="text"><?php _e('Add New', 'wcfm'); ?></span>
      </a>
      <div class="wcfm-clearfix"></div>
    </div>

    <div class="wcfm-container">
      <div id="wcfm_videos_listing_expander" class="wcfm-content">
        <table class="wcfm-table display" id="wcfm-videos">
          <thead>
            <tr>
              <th><?php _e('Thumbnail', 'wcfm'); ?></th>
              <th><?php _e('Title', 'wcfm'); ?></th>
              <th><?php _e('Type', 'wcfm'); ?></th>
              <th><?php _e('Status', 'wcfm'); ?></th>
              <th><?php _e('Actions', 'wcfm'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $videos = get_posts([
              'post_type' => 'video',
              'author' => get_current_user_id(),
              'posts_per_page' => -1,
              'post_status' => ['publish', 'draft'],
            ]);
            foreach ($videos as $video):
              $thumb = get_the_post_thumbnail($video->ID, [60, 60]) ?: '<span class="wcfmfa fa-image"></span>';
              $terms = wp_get_post_terms($video->ID, 'video_type', ['fields' => 'names']);
              $type = $terms ? implode(', ', $terms) : '-';
            ?>
              <tr>
                <td><?php echo $thumb; ?></td>
                <td><?php echo esc_html($video->post_title); ?></td>
                <td><?php echo esc_html($type); ?></td>
                <td><?php echo ucfirst($video->post_status); ?></td>
                <td>
                  <a class="wcfm-action-icon" href="<?php echo add_query_arg('edit', $video->ID, wcfm_get_endpoint_url('video')); ?>">
                    <span class="wcfmfa fa-edit text_tip" data-tip="<?php _e('Edit', 'wcfm'); ?>"></span>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
