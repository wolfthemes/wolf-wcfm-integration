<?php
/**
 * WCFM plugin controllers
 *
 * Plugin video Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/video
 * @version   1.0.0
 */

class WCFM_Video_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
		
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
			'posts_per_page'   => $length,
			'offset'           => $offset,
			//'category'         => '',
			//'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'video',
			'post_mime_type'   => '',
			'post_parent'      => '',
			//'author'	   => get_current_user_id(),
			'post_status'      => array('draft', 'pending', 'publish'),
			'suppress_filters' => 0 
		);
		$for_count_args = $args;
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$args['s'] = $_POST['search']['value'];
		}
		
		if( isset($_POST['video_status']) && !empty($_POST['video_status']) ) $args['post_status'] = $_POST['video_status'];
  	
		// Multi Vendor Support
		$is_marketplace = wcfm_is_marketplace();
		if( $is_marketplace ) {
			if( isset($_POST['video_vendor']) && !empty($_POST['video_vendor']) ) {
				if( !wcfm_is_vendor() ) {
					$args['author'] = $_POST['video_vendor'];
				}
			}
			if( wcfm_is_vendor() ) {
				$args['author'] = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		}
		
		$args = apply_filters( 'wcfm_video_args', $args );
		
		$wcfm_video_array = get_posts( $args );
		
		$video_count = 0;
		$filtered_video_count = 0;
		if( wcfm_is_vendor() ) {
			// Get video Count
			$for_count_args['posts_per_page'] = -1;
			$for_count_args['offset'] = 0;
			$for_count_args = apply_filters( 'wcfm_video_args', $for_count_args );
			$wcfm_video_count = get_posts( $for_count_args );
			$video_count = count($wcfm_video_count);
			
			// Get Filtered Post Count
			$args['posts_per_page'] = -1;
			$args['offset'] = 0;
			$wcfm_filterd_video_array = get_posts( $args );
			$filtered_video_count = count($wcfm_filterd_video_array);
		} else {
			// Get video Count
			$wcfm_video_counts = wp_count_posts('post');
			foreach($wcfm_video_counts as $wcfm_video_type => $wcfm_video_count ) {
				if( in_array( $wcfm_video_type, array( 'publish', 'draft', 'pending' ) ) ) {
					$video_count += $wcfm_video_count;
				}
			}
			
			// Get Filtered Post Count
			$filtered_video_count = $video_count; 
		}
		
		// Generate video JSON
		$wcfm_video_json = '';
		$wcfm_video_json = '{
			"draw": ' . $_POST['draw'] . ',
			"recordsTotal": ' . $video_count . ',
			"recordsFiltered": ' . $filtered_video_count . ',
			"data": ';

		$wcfm_video_json_array = array();

		$wcfm_video_json_array['draw'] = $_POST['draw'];
		$wcfm_video_json_array['recordsTotal'] = absint( $video_count );
		$wcfm_video_json_array['recordsFiltered'] = absint( $filtered_video_count );

		if(!empty($wcfm_video_array)) {
			$index = 0;
			$wcfm_video_json_arr = array();
			foreach($wcfm_video_array as $wcfm_video_single) {
				
				// Thumb
				if( apply_filters( 'wcfm_is_allow_edit_video', true ) ) {
					$wcfm_video_json_arr[$index][] =  '<a href="' . get_wcfm_cpt_manage_url( 'video', $wcfm_video_single->ID ) . '"><img width="40" height="40" class="attachment-thumbnail size-thumbnail wp-post-image" src="' . get_the_post_thumbnail_url( $wcfm_video_single->ID ) . '" /></a>';
				} else {
					$wcfm_video_json_arr[$index][] =  '<img width="40" height="40" class="attachment-thumbnail size-thumbnail wp-post-image" src="' . get_the_post_thumbnail_url( $wcfm_video_single->ID ) . '" />';
				}
				
				// Title
				if( apply_filters( 'wcfm_is_allow_edit_video', true ) ) {
					$wcfm_video_json_arr[$index][] =  '<a href="' . get_wcfm_cpt_manage_url( 'video', $wcfm_video_single->ID ) . '" class="wcfm_video_title wcfm_dashboard_item_title">' . $wcfm_video_single->post_title . '</a>';
				} else {
					if( $wcfm_video_single->post_status == 'publish' ) {
						$wcfm_video_json_arr[$index][] =  apply_filters( 'wcfm_video_title_dashboard', $wcfm_video_single->post_title, $wcfm_video_single->ID );
					} elseif( apply_filters( 'wcfm_is_allow_edit_video', true ) ) {
						$wcfm_video_json_arr[$index][] =  apply_filters( 'wcfm_video_title_dashboard', '<a href="' . get_wcfm_cpt_manage_url( 'video', $wcfm_video_single->ID ) . '" class="wcfm_video_title wcfm_dashboard_item_title">' . $wcfm_video_single->post_title . '</a>', $wcfm_video_single->ID );
					} else {
						$wcfm_video_json_arr[$index][] =  apply_filters( 'wcfm_video_title_dashboard', $wcfm_video_single->post_title, $wcfm_video_single->ID );
					}
				}
				
				// Status
				if( $wcfm_video_single->post_status == 'publish' ) {
					$wcfm_video_json_arr[$index][] =  '<span class="video-status video-status-' . $wcfm_video_single->post_status . '">' . __( 'Published', 'wcfm-cpt' ) . '</span>';
				} else {
					$wcfm_video_json_arr[$index][] =  '<span class="video-status video-status-' . $wcfm_video_single->post_status . '">' . __( ucfirst( $wcfm_video_single->post_status ), 'wcfm-cpt' ) . '</span>';
				}
				
				// Views
				$wcfm_video_json_arr[$index][] =  '<span class="view_count">' . (int) get_post_meta( $wcfm_video_single->ID, '_wcfm_video_views', true ) . '</span>';
				
				// Taxonomies
				$taxonomies = '';
				$product_taxonomies = get_object_taxonomies( 'video', 'objects' );
				if( !empty( $product_taxonomies ) ) {
					foreach( $product_taxonomies as $product_taxonomy ) {
						if( !in_array( $product_taxonomy->name, array( 'post_tag' ) ) ) {
							if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
								// Fetching Saved Values
								$taxonomy_values = get_the_terms( $wcfm_video_single->ID, $product_taxonomy->name );
								if( !empty($taxonomy_values) ) {
									$taxonomies .= "<strong>" . __( $product_taxonomy->label, 'wc-frontend-manager' ) . '</strong>: ';
									$is_first = true;
									foreach($taxonomy_values as $pkey => $ptaxonomy) {
										if( !$is_first ) $taxonomies .= ', ';
										$is_first = false;
										$taxonomies .= '<a style="color: #dd4b39;" href="' . get_term_link( $ptaxonomy->term_id ) . '" target="_blank">' . $ptaxonomy->name . '</a>';
									}
								}
							}
						}
					}
				}
				
				if( !$taxonomies ) $taxonomies = '&ndash;';
				$wcfm_video_json_arr[$index][] =  $taxonomies;
				
				// Author
				$author = get_user_by( 'id', $wcfm_video_single->post_author );
				if( $author ) {
					$wcfm_video_json_arr[$index][] =  $author->display_name;
				} else {
					$wcfm_video_json_arr[$index][] =  '&ndash;';
				}
				
				// Date
				$wcfm_video_json_arr[$index][] =  date_i18n( wc_date_format(), strtotime($wcfm_video_single->post_date) );
				
				// Action
				$actions = '<a class="wcfm-action-icon" target="_blank" href="' . get_permalink( $wcfm_video_single->ID ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wcfm-cpt' ) . '"></span></a>';
				
				if( $wcfm_video_single->post_status == 'publish' ) {
					$actions .= ( apply_filters( 'wcfm_is_allow_edit_video', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_cpt_manage_url( 'video', $wcfm_video_single->ID ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wcfm-cpt' ) . '"></span></a>' : '';
					$actions .= ( apply_filters( 'wcfm_is_allow_delete_video', true ) ) ? '<a class="wcfm-action-icon wcfm_video_delete" href="#" data-videoid="' . $wcfm_video_single->ID . '"><span class="fa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wcfm-cpt' ) . '"></span></a>' : '';
				} else {
					$actions .= ( apply_filters( 'wcfm_is_allow_edit_video', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_cpt_manage_url( 'video', $wcfm_video_single->ID ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wcfm-cpt' ) . '"></span></a>' : '';
					$actions .= ( apply_filters( 'wcfm_is_allow_delete_video', true ) ) ? '<a class="wcfm_video_delete wcfm-action-icon" href="#" data-videoid="' . $wcfm_video_single->ID . '"><span class="fa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wcfm-cpt' ) . '"></span></a>' : '';
				}
				
				$wcfm_video_json_arr[$index][] =  apply_filters ( 'wcfm_video_actions',  $actions, $wcfm_video_single );
				
				
				$index++;
			}												
		}

		$wcfm_video_json_array['data'] = $wcfm_video_json_arr;

		wp_send_json($wcfm_video_json_array);

	}
}