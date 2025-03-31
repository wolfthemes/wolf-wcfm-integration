<?php
class WCFM_Video_Controller {
  public function __construct() {
    $this->process();
  }

  private function process() {
    $user_id = get_current_user_id();
    if (!$user_id || !current_user_can('edit_posts')) {
      wp_send_json_error('Unauthorized');
    }

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $title = sanitize_text_field($_POST['video_title'] ?? '');
    $content = wp_kses_post($_POST['video_content'] ?? '');
    $client = sanitize_text_field($_POST['video_client'] ?? '');
    $link = esc_url_raw($_POST['video_link'] ?? '');
    $thumbnail = absint($_POST['video_thumbnail'] ?? 0);
    $video_type = absint($_POST['video_type'] ?? 0);

    $post_data = [
      'post_title'   => $title,
      'post_content' => $content,
      'post_type'    => 'video',
      'post_status'  => 'publish',
      'post_author'  => $user_id,
    ];

    if ($post_id) {
      $post_data['ID'] = $post_id;
      $result = wp_update_post($post_data, true);
    } else {
      $result = wp_insert_post($post_data, true);
      $post_id = $result;
    }

    if (is_wp_error($result)) {
      wp_send_json_error($result->get_error_message());
    }

    // Save meta
    update_post_meta($post_id, '_video_client', $client);
    update_post_meta($post_id, '_video_link', $link);
    wp_set_post_terms($post_id, [$video_type], 'video_type', false);

    if ($thumbnail) {
      set_post_thumbnail($post_id, $thumbnail);
    }

    wp_send_json_success(['post_id' => $post_id]);
  }
}
