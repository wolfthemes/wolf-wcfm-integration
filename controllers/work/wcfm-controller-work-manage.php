<?php
/**
 * WCFM plugin controllers
 *
 * Plugin work Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Work_Manage_Controller {

	public function __construct() {
		global $WCFM;

		$this->processing();
	}

	public function processing() {
		global $WCFM, $wpdb, $_POST;

		$wcfm_work_manage_form_data = array();
	  parse_str($_POST['wcfm_work_manage_form'], $wcfm_work_manage_form_data);
	  //print_r($wcfm_work_manage_form_data);
	  $wcfm_work_manage_messages = get_wcfm_cpt_manager_messages();
	  $has_error                  = false;

		if (isset($wcfm_work_manage_form_data['title']) && !empty($wcfm_work_manage_form_data['title'])) {
		  $is_update       = false;
		  $is_publish      = false;
		  $current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

		  // WCFM form custom validation filter
		  $custom_validation_results = apply_filters( 'wcfm_form_custom_validation', $wcfm_work_manage_form_data, 'work_manage' );
			if (isset($custom_validation_results['has_error']) && !empty($custom_validation_results['has_error'])) {
				$custom_validation_error = __( 'There has some error in submitted data.', 'wcfm-cpt' );
				if ( isset( $custom_validation_results['message'] ) && !empty( $custom_validation_results['message'] ) ) {
$custom_validation_error = $custom_validation_results['message']; }
				echo '{"status": false, "message": "' . $custom_validation_error . '"}';
				die;
			}

			if (isset($_POST['status']) && ( $_POST['status'] == 'draft' )) {
				$work_status = 'draft';
			} else {
				if ( apply_filters( 'wcfm_is_allow_publish_work', true ) ) {
				  $work_status = 'publish';
				} else {
$work_status = 'pending';
				}
			}

		  // Creating new work
			$new_work = apply_filters( 'wcfm_work_content_before_save', array(
				'post_title'   => wc_clean( $wcfm_work_manage_form_data['title'] ),
				'post_status'  => $work_status,
				'post_type'    => 'work',
				//'post_excerpt' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['excerpt'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_content' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['description'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_author'  => $current_user_id,
				'post_name' => sanitize_title($wcfm_work_manage_form_data['title'])
			), $wcfm_work_manage_form_data );

			if (isset($wcfm_work_manage_form_data['work_id']) && $wcfm_work_manage_form_data['work_id'] == 0) {
				if ($work_status != 'draft') {
					$is_publish = true;
				}
				$new_work_id = wp_insert_post( $new_work, true );
			} else { // For Update
				$is_update       = true;
				$new_work['ID'] = $wcfm_work_manage_form_data['work_id'];
				unset( $new_work['post_author'] );
				unset( $new_work['post_name'] );
				if ( ( $work_status != 'draft' ) && ( get_post_status( $new_work['ID'] ) == 'publish' ) ) {
					if ( apply_filters( 'wcfm_is_allow_publish_live_work', true ) ) {
						$new_work['post_status'] = 'publish';
					}
				} elseif ( ( get_post_status( $new_work['ID'] ) == 'draft' ) && ( $work_status != 'draft' ) ) {
					$is_publish = true;
				}
				$new_work_id = wp_update_post( $new_work, true );
			}

			if (!is_wp_error($new_work_id)) {
				// For Update
				if ($is_update) {
$new_work_id = $wcfm_work_manage_form_data['work_id'];
				}

				// Set work Custom Taxonomies
				if (isset($wcfm_work_manage_form_data['product_custom_taxonomies']) && !empty($wcfm_work_manage_form_data['product_custom_taxonomies'])) {
					foreach ($wcfm_work_manage_form_data['product_custom_taxonomies'] as $taxonomy => $taxonomy_values) {
						if ( !empty( $taxonomy_values ) ) {
							$is_first = true;
							foreach ( $taxonomy_values as $taxonomy_value ) {
								if ($is_first) {
									  $is_first = false;
									  wp_set_object_terms( $new_work_id, (int) $taxonomy_value, $taxonomy );
								} else {
									wp_set_object_terms( $new_work_id, (int) $taxonomy_value, $taxonomy, true );
								}
							}
						}
					}
				}

				  // Set work Custom Taxonomies Flat
				if (isset($wcfm_work_manage_form_data['work_custom_taxonomies_flat']) && !empty($wcfm_work_manage_form_data['work_custom_taxonomies_flat'])) {
					foreach ($wcfm_work_manage_form_data['work_custom_taxonomies_flat'] as $taxonomy => $taxonomy_values) {
						if ( !empty( $taxonomy_values ) ) {
							wp_set_post_terms( $new_work_id, $taxonomy_values, $taxonomy );
						}
					}
				}

				  // Set work Featured Image
				if (isset($wcfm_work_manage_form_data['featured_img']) && !empty($wcfm_work_manage_form_data['featured_img'])) {
					$featured_img_id = $WCFM->wcfm_get_attachment_id($wcfm_work_manage_form_data['featured_img']);
					set_post_thumbnail( $new_work_id, $featured_img_id );
					wp_update_post( array( 'ID' => $featured_img_id, 'post_parent' => $new_work_id ) );
				} elseif (isset($wcfm_work_manage_form_data['featured_img']) && empty($wcfm_work_manage_form_data['featured_img'])) {
					delete_post_thumbnail( $new_work_id );
				}

				  // Custom Fields
				if (isset($wcfm_work_manage_form_data['custom_1']) && !empty($wcfm_work_manage_form_data['custom_1'])) {
					update_post_meta( $new_work_id, 'custom_1', $wcfm_work_manage_form_data['custom_1'] );
				} else {
					update_post_meta( $new_work_id, 'custom_1', '' );
				}
				if (isset($wcfm_work_manage_form_data['custom_2']) && !empty($wcfm_work_manage_form_data['custom_2'])) {
					update_post_meta( $new_work_id, 'custom_2', $wcfm_work_manage_form_data['custom_2'] );
				} else {
					update_post_meta( $new_work_id, 'custom_2', '' );
				}
				if (isset($wcfm_work_manage_form_data['custom_3']) && !empty($wcfm_work_manage_form_data['custom_3'])) {
					update_post_meta( $new_work_id, 'custom_3', $wcfm_work_manage_form_data['custom_3'] );
				} else {
					update_post_meta( $new_work_id, 'custom_3', '' );
				}
				if (isset($wcfm_work_manage_form_data['custom_4']) && !empty($wcfm_work_manage_form_data['custom_4'])) {
					update_post_meta( $new_work_id, 'custom_4', $wcfm_work_manage_form_data['custom_4'] );
				} else {
					update_post_meta( $new_work_id, 'custom_4', '' );
				}

				  do_action( 'after_wcfm_work_manage_meta_save', $new_work_id, $wcfm_work_manage_form_data );

				  // Notify Admin on New work Creation
				if ( $is_publish ) {
					// Have to test before adding action
				}

				if (!$has_error) {
					if ( get_post_status( $new_work_id ) == 'publish' ) {
						if (!$has_error) {
echo '{"status": true, "message": "' . apply_filters( 'work_published_message', $wcfm_work_manage_messages['cpt_published'], $new_work_id ) . '", "redirect": "' . apply_filters( 'wcfm_work_save_publish_redirect', get_wcfm_cpt_manage_url( 'work', $new_work_id ), $new_work_id ) . '", "id": "' . $new_work_id . '", "title": "' . get_the_title( $new_work_id ) . '"}';
						}
					} elseif ( get_post_status( $new_work_id ) == 'pending' ) {
						if (!$has_error) {
echo '{"status": true, "message": "' . apply_filters( 'work_pending_message', $wcfm_work_manage_messages['cpt_pending'], $new_work_id ) . '", "redirect": "' . apply_filters( 'wcfm_work_save_pending_redirect', get_wcfm_cpt_manage_url( 'work', $new_work_id ), $new_work_id ) . '", "id": "' . $new_work_id . '", "title": "' . get_the_title( $new_work_id ) . '"}';
						}
					} else {
						if (!$has_error) {
echo '{"status": true, "message": "' . apply_filters( 'work_saved_message', $wcfm_work_manage_messages['cpt_saved'], $new_work_id ) . '", "redirect": "' . apply_filters( 'wcfm_work_save_draft_redirect', get_wcfm_cpt_manage_url( 'work', $new_work_id ), $new_work_id ) . '", "id": "' . $new_work_id . '"}';
						}
					}
				}
				  die;
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_work_manage_messages['no_title'] . '"}';
		}
	  die;
	}
}
