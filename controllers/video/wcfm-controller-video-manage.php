<?php
/**
 * WCFM plugin controllers
 *
 * Plugin video Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Video_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_video_manage_form_data = array();
	  parse_str($_POST['wcfm_video_manage_form'], $wcfm_video_manage_form_data);
	  //print_r($wcfm_video_manage_form_data);
	  $wcfm_video_manage_messages = get_wcfm_cpt_manager_messages();
	  $has_error                  = false;
	  
		if (isset($wcfm_video_manage_form_data['title']) && !empty($wcfm_video_manage_form_data['title'])) {
		  $is_update       = false;
		  $is_publish      = false;
		  $current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		  // WCFM form custom validation filter
		  $custom_validation_results = apply_filters( 'wcfm_form_custom_validation', $wcfm_video_manage_form_data, 'video_manage' );
			if (isset($custom_validation_results['has_error']) && !empty($custom_validation_results['has_error'])) {
				$custom_validation_error = __( 'There has some error in submitted data.', 'wcfm-cpt' );
				if ( isset( $custom_validation_results['message'] ) && !empty( $custom_validation_results['message'] ) ) {
$custom_validation_error = $custom_validation_results['message']; }
				echo '{"status": false, "message": "' . $custom_validation_error . '"}';
				die;
			}
						  
			if (isset($_POST['status']) && ( $_POST['status'] == 'draft' )) {
				$video_status = 'draft';
			} else {
				if ( apply_filters( 'wcfm_is_allow_publish_video', true ) ) {
				  $video_status = 'publish';
				} else {
$video_status = 'pending';
				}
			}
		
		  // Creating new video
			$new_video = apply_filters( 'wcfm_video_content_before_save', array(
				'post_title'   => wc_clean( $wcfm_video_manage_form_data['title'] ),
				'post_status'  => $video_status,
				'post_type'    => 'video',
				//'post_excerpt' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['excerpt'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_content' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['description'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_author'  => $current_user_id,
				'post_name' => sanitize_title($wcfm_video_manage_form_data['title'])
			), $wcfm_video_manage_form_data );
			
			if (isset($wcfm_video_manage_form_data['video_id']) && $wcfm_video_manage_form_data['video_id'] == 0) {
				if ($video_status != 'draft') {
					$is_publish = true;
				}
				$new_video_id = wp_insert_post( $new_video, true );
			} else { // For Update
				$is_update       = true;
				$new_video['ID'] = $wcfm_video_manage_form_data['video_id'];
				unset( $new_video['post_author'] );
				unset( $new_video['post_name'] );
				if ( ( $video_status != 'draft' ) && ( get_post_status( $new_video['ID'] ) == 'publish' ) ) {
					if ( apply_filters( 'wcfm_is_allow_publish_live_video', true ) ) {
						$new_video['post_status'] = 'publish';
					}
				} elseif ( ( get_post_status( $new_video['ID'] ) == 'draft' ) && ( $video_status != 'draft' ) ) {
					$is_publish = true;
				}
				$new_video_id = wp_update_post( $new_video, true );
			}
			
			if (!is_wp_error($new_video_id)) {
				// For Update
				if ($is_update) {
$new_video_id = $wcfm_video_manage_form_data['video_id'];
				}
				
				// Set video Custom Taxonomies
				if (isset($wcfm_video_manage_form_data['product_custom_taxonomies']) && !empty($wcfm_video_manage_form_data['product_custom_taxonomies'])) {
					foreach ($wcfm_video_manage_form_data['product_custom_taxonomies'] as $taxonomy => $taxonomy_values) {
						if ( !empty( $taxonomy_values ) ) {
							$is_first = true;
							foreach ( $taxonomy_values as $taxonomy_value ) {
								if ($is_first) {
									  $is_first = false;
									  wp_set_object_terms( $new_video_id, (int) $taxonomy_value, $taxonomy );
								} else {
									wp_set_object_terms( $new_video_id, (int) $taxonomy_value, $taxonomy, true );
								}
							}
						}
					}
				}
				
				  // Set video Custom Taxonomies Flat
				if (isset($wcfm_video_manage_form_data['video_custom_taxonomies_flat']) && !empty($wcfm_video_manage_form_data['video_custom_taxonomies_flat'])) {
					foreach ($wcfm_video_manage_form_data['video_custom_taxonomies_flat'] as $taxonomy => $taxonomy_values) {
						if ( !empty( $taxonomy_values ) ) {
							wp_set_post_terms( $new_video_id, $taxonomy_values, $taxonomy );
						}
					}
				}
				
				  // Set video Featured Image
				if (isset($wcfm_video_manage_form_data['featured_img']) && !empty($wcfm_video_manage_form_data['featured_img'])) {
					$featured_img_id = $WCFM->wcfm_get_attachment_id($wcfm_video_manage_form_data['featured_img']);
					set_post_thumbnail( $new_video_id, $featured_img_id );
					wp_update_post( array( 'ID' => $featured_img_id, 'post_parent' => $new_video_id ) );
				} elseif (isset($wcfm_video_manage_form_data['featured_img']) && empty($wcfm_video_manage_form_data['featured_img'])) {
					delete_post_thumbnail( $new_video_id );
				}
				
				  // Custom Fields 
				if (isset($wcfm_video_manage_form_data['custom_1']) && !empty($wcfm_video_manage_form_data['custom_1'])) {
					update_post_meta( $new_video_id, 'custom_1', $wcfm_video_manage_form_data['custom_1'] );
				} else {
					update_post_meta( $new_video_id, 'custom_1', '' );
				}
				if (isset($wcfm_video_manage_form_data['custom_2']) && !empty($wcfm_video_manage_form_data['custom_2'])) {
					update_post_meta( $new_video_id, 'custom_2', $wcfm_video_manage_form_data['custom_2'] );
				} else {
					update_post_meta( $new_video_id, 'custom_2', '' );
				}
				if (isset($wcfm_video_manage_form_data['custom_3']) && !empty($wcfm_video_manage_form_data['custom_3'])) {
					update_post_meta( $new_video_id, 'custom_3', $wcfm_video_manage_form_data['custom_3'] );
				} else {
					update_post_meta( $new_video_id, 'custom_3', '' );
				}
				if (isset($wcfm_video_manage_form_data['custom_4']) && !empty($wcfm_video_manage_form_data['custom_4'])) {
					update_post_meta( $new_video_id, 'custom_4', $wcfm_video_manage_form_data['custom_4'] );
				} else {
					update_post_meta( $new_video_id, 'custom_4', '' );
				}
				
				  do_action( 'after_wcfm_video_manage_meta_save', $new_video_id, $wcfm_video_manage_form_data );
				
				  // Notify Admin on New video Creation
				if ( $is_publish ) {
					// Have to test before adding action
				} 
				
				if (!$has_error) {
					if ( get_post_status( $new_video_id ) == 'publish' ) {
						if (!$has_error) {
echo '{"status": true, "message": "' . apply_filters( 'video_published_message', $wcfm_video_manage_messages['cpt_published'], $new_video_id ) . '", "redirect": "' . apply_filters( 'wcfm_video_save_publish_redirect', get_wcfm_cpt_manage_url( 'video', $new_video_id ), $new_video_id ) . '", "id": "' . $new_video_id . '", "title": "' . get_the_title( $new_video_id ) . '"}';
						}	
					} elseif ( get_post_status( $new_video_id ) == 'pending' ) {
						if (!$has_error) {
echo '{"status": true, "message": "' . apply_filters( 'video_pending_message', $wcfm_video_manage_messages['cpt_pending'], $new_video_id ) . '", "redirect": "' . apply_filters( 'wcfm_video_save_pending_redirect', get_wcfm_cpt_manage_url( 'video', $new_video_id ), $new_video_id ) . '", "id": "' . $new_video_id . '", "title": "' . get_the_title( $new_video_id ) . '"}';
						}
					} else {
						if (!$has_error) {
echo '{"status": true, "message": "' . apply_filters( 'video_saved_message', $wcfm_video_manage_messages['cpt_saved'], $new_video_id ) . '", "redirect": "' . apply_filters( 'wcfm_video_save_draft_redirect', get_wcfm_cpt_manage_url( 'video', $new_video_id ), $new_video_id ) . '", "id": "' . $new_video_id . '"}';
						}
					}
				}
				  die;
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_video_manage_messages['no_title'] . '"}';
		}
	  die;
	}
}
