<?php
$post_id = isset($_GET['edit']) && $_GET['edit'] !== 'new' ? absint($_GET['edit']) : 0;
$video = $post_id ? get_post($post_id) : null;

$title = $video ? $video->post_title : '';
$content = $video ? $video->post_content : '';
$video_type = $video ? wp_get_post_terms($post_id, 'video_type', ['fields' => 'ids']) : [];
$client = $post_id ? get_post_meta($post_id, '_video_client', true) : '';
$link = $post_id ? get_post_meta($post_id, '_video_link', true) : '';
$thumbnail_id = $video ? get_post_thumbnail_id($post_id) : 0;
?>

<div class="collapse wcfm-collapse" id="wcfm_videos_manage">
  <div class="wcfm-page-headig">
    <span class="wcfmfa fa-video"></span>
    <span class="wcfm-page-heading-text"><?php echo $post_id ? 'Edit Video' : 'Add New Video'; ?></span>
  </div>

  <div class="wcfm-container">
    <div class="wcfm-content">
      <form id="wcfm_video_form">
        <input type="hidden" name="controller" value="wcfm-video" />
        <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>" />

        <div class="wcfm-form-row">
          <label><?php _e('Title', 'wcfm'); ?></label>
          <input type="text" name="video_title" value="<?php echo esc_attr($title); ?>" required />
        </div>

        <div class="wcfm-form-row">
          <label><?php _e('Content', 'wcfm'); ?></label>
          <?php wp_editor($content, 'video_content', ['textarea_name' => 'video_content']); ?>
        </div>

        <div class="wcfm-form-row">
          <label><?php _e('Video Type', 'wcfm'); ?></label>
          <?php wp_dropdown_categories([
            'taxonomy' => 'video_type',
            'hide_empty' => false,
            'name' => 'video_type',
            'selected' => $video_type[0] ?? 0,
            'show_option_none' => __('Select type', 'wcfm')
          ]); ?>
        </div>

        <div class="wcfm-form-row">
          <label><?php _e('Client', 'wcfm'); ?></label>
          <input type="text" name="video_client" value="<?php echo esc_attr($client); ?>" />
        </div>

        <div class="wcfm-form-row">
          <label><?php _e('Video Link', 'wcfm'); ?></label>
          <input type="url" name="video_link" value="<?php echo esc_attr($link); ?>" />
        </div>

        <div class="wcfm-form-row">
          <label><?php _e('Featured Image', 'wcfm'); ?></label>
          <input type="hidden" name="video_thumbnail" id="video_thumbnail" value="<?php echo esc_attr($thumbnail_id); ?>" />
          <img id="video_thumbnail_preview" src="<?php echo $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : ''; ?>" style="max-height: 120px;" />
          <br>
          <button type="button" class="upload_image_button button"><?php _e('Choose Image', 'wcfm'); ?></button>
        </div>

        <div class="wcfm-form-row">
          <input type="submit" class="wcfm_submit_button" value="<?php echo $post_id ? 'Update' : 'Add'; ?>" />
        </div>
      </form>
    </div>
  </div>
</div>

<script>
jQuery(function($){
  let frame;

  $('.upload_image_button').on('click', function(e){
    e.preventDefault();
    if (frame) frame.close();

    frame = wp.media({ title: 'Select Featured Image', button: { text: 'Use this image' }, multiple: false });
    frame.on('select', function(){
      const attachment = frame.state().get('selection').first().toJSON();
      $('#video_thumbnail').val(attachment.id);
      $('#video_thumbnail_preview').attr('src', attachment.url);
    });
    frame.open();
  });

  $('#wcfm_video_form').on('submit', function(e){
    e.preventDefault();
    const data = $(this).serialize();

    console.log( data );

    $.post(wcfm_params.ajax_url, data, function(response){
      if (response.success) {
        alert('Saved!');
        window.location = wcfm_params.wcfm_base_url + 'video';
      } else {
        alert('Error: ' + (response.data || 'unknown error'));
      }
    });
  });
});
</script>
