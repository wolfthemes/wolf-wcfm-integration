<?php
/**
 * WCFM plugin controllers
 *
 * Plugin event Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/event
 * @version   1.0.0
 */

class WCFM_Event_Controller {
	
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
			'post_type'        => 'event',
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
		
		if( isset($_POST['event_status']) && !empty($_POST['event_status']) ) $args['post_status'] = $_POST['event_status'];
  	
		// Multi Vendor Support
		$is_marketplace = wcfm_is_marketplace();
		if( $is_marketplace ) {
			if( isset($_POST['event_vendor']) && !empty($_POST['event_vendor']) ) {
				if( !wcfm_is_vendor() ) {
					$args['author'] = $_POST['event_vendor'];
				}
			}
			if( wcfm_is_vendor() ) {
				$args['author'] = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		}
		
		$args = apply_filters( 'wcfm_event_args', $args );
		
		$wcfm_event_array = get_posts( $args );
		
		$event_count = 0;
		$filtered_event_count = 0;
		if( wcfm_is_vendor() ) {
			// Get event Count
			$for_count_args['posts_per_page'] = -1;
			$for_count_args['offset'] = 0;
			$for_count_args = apply_filters( 'wcfm_event_args', $for_count_args );
			$wcfm_event_count = get_posts( $for_count_args );
			$event_count = count($wcfm_event_count);
			
			// Get Filtered Post Count
			$args['posts_per_page'] = -1;
			$args['offset'] = 0;
			$wcfm_filterd_event_array = get_posts( $args );
			$filtered_event_count = count($wcfm_filterd_event_array);
		} else {
			// Get event Count
			$wcfm_event_counts = wp_count_posts('post');
			foreach($wcfm_event_counts as $wcfm_event_type => $wcfm_event_count ) {
				if( in_array( $wcfm_event_type, array( 'publish', 'draft', 'pending' ) ) ) {
					$event_count += $wcfm_event_count;
				}
			}
			
			// Get Filtered Post Count
			$filtered_event_count = $event_count; 
		}
		
		// Generate event JSON
		$wcfm_event_json = '';
		$wcfm_event_json = '{
			"draw": ' . $_POST['draw'] . ',
			"recordsTotal": ' . $event_count . ',
			"recordsFiltered": ' . $filtered_event_count . ',
			"data": ';

		$wcfm_event_json_array = array();

		$wcfm_event_json_array['draw'] = $_POST['draw'];
		$wcfm_event_json_array['recordsTotal'] = absint( $event_count );
		$wcfm_event_json_array['recordsFiltered'] = absint( $filtered_event_count );

		if(!empty($wcfm_event_array)) {
			$index = 0;
			$wcfm_event_json_arr = array();
			foreach($wcfm_event_array as $wcfm_event_single) {
				
				// Thumb
				if( apply_filters( 'wcfm_is_allow_edit_event', true ) ) {
					$wcfm_event_json_arr[$index][] =  '<a href="' . get_wcfm_cpt_manage_url( 'event', $wcfm_event_single->ID ) . '"><img width="40" height="40" class="attachment-thumbnail size-thumbnail wp-post-image" src="' . get_the_post_thumbnail_url( $wcfm_event_single->ID ) . '" /></a>';
				} else {
					$wcfm_event_json_arr[$index][] =  '<img width="40" height="40" class="attachment-thumbnail size-thumbnail wp-post-image" src="' . get_the_post_thumbnail_url( $wcfm_event_single->ID ) . '" />';
				}
				
				// Title
				if( apply_filters( 'wcfm_is_allow_edit_event', true ) ) {
					$wcfm_event_json_arr[$index][] =  '<a href="' . get_wcfm_cpt_manage_url( 'event', $wcfm_event_single->ID ) . '" class="wcfm_event_title wcfm_dashboard_item_title">' . $wcfm_event_single->post_title . '</a>';
				} else {
					if( $wcfm_event_single->post_status == 'publish' ) {
						$wcfm_event_json_arr[$index][] =  apply_filters( 'wcfm_event_title_dashboard', $wcfm_event_single->post_title, $wcfm_event_single->ID );
					} elseif( apply_filters( 'wcfm_is_allow_edit_event', true ) ) {
						$wcfm_event_json_arr[$index][] =  apply_filters( 'wcfm_event_title_dashboard', '<a href="' . get_wcfm_cpt_manage_url( 'event', $wcfm_event_single->ID ) . '" class="wcfm_event_title wcfm_dashboard_item_title">' . $wcfm_event_single->post_title . '</a>', $wcfm_event_single->ID );
					} else {
						$wcfm_event_json_arr[$index][] =  apply_filters( 'wcfm_event_title_dashboard', $wcfm_event_single->post_title, $wcfm_event_single->ID );
					}
				}
				
				// Status
				if( $wcfm_event_single->post_status == 'publish' ) {
					$wcfm_event_json_arr[$index][] =  '<span class="event-status event-status-' . $wcfm_event_single->post_status . '">' . __( 'Published', 'wcfm-cpt' ) . '</span>';
				} else {
					$wcfm_event_json_arr[$index][] =  '<span class="event-status event-status-' . $wcfm_event_single->post_status . '">' . __( ucfirst( $wcfm_event_single->post_status ), 'wcfm-cpt' ) . '</span>';
				}
				
				// Views
				$wcfm_event_json_arr[$index][] =  '<span class="view_count">' . (int) get_post_meta( $wcfm_event_single->ID, '_wcfm_event_views', true ) . '</span>';
				
				// Taxonomies
				$taxonomies = '';
				$product_taxonomies = get_object_taxonomies( 'event', 'objects' );
				if( !empty( $product_taxonomies ) ) {
					foreach( $product_taxonomies as $product_taxonomy ) {
						if( !in_array( $product_taxonomy->name, array( 'post_tag' ) ) ) {
							if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
								// Fetching Saved Values
								$taxonomy_values = get_the_terms( $wcfm_event_single->ID, $product_taxonomy->name );
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
				$wcfm_event_json_arr[$index][] =  $taxonomies;
				
				// Author
				$author = get_user_by( 'id', $wcfm_event_single->post_author );
				if( $author ) {
					$wcfm_event_json_arr[$index][] =  $author->display_name;
				} else {
					$wcfm_event_json_arr[$index][] =  '&ndash;';
				}
				
				// Date
				$wcfm_event_json_arr[$index][] =  date_i18n( wc_date_format(), strtotime($wcfm_event_single->post_date) );
				
				// Action
				$actions = '<a class="wcfm-action-icon" target="_blank" href="' . get_permalink( $wcfm_event_single->ID ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wcfm-cpt' ) . '"></span></a>';
				
				if( $wcfm_event_single->post_status == 'publish' ) {
					$actions .= ( apply_filters( 'wcfm_is_allow_edit_event', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_cpt_manage_url( 'event', $wcfm_event_single->ID ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wcfm-cpt' ) . '"></span></a>' : '';
					$actions .= ( apply_filters( 'wcfm_is_allow_delete_event', true ) ) ? '<a class="wcfm-action-icon wcfm_event_delete" href="#" data-eventid="' . $wcfm_event_single->ID . '"><span class="fa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wcfm-cpt' ) . '"></span></a>' : '';
				} else {
					$actions .= ( apply_filters( 'wcfm_is_allow_edit_event', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_cpt_manage_url( 'event', $wcfm_event_single->ID ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wcfm-cpt' ) . '"></span></a>' : '';
					$actions .= ( apply_filters( 'wcfm_is_allow_delete_event', true ) ) ? '<a class="wcfm_event_delete wcfm-action-icon" href="#" data-eventid="' . $wcfm_event_single->ID . '"><span class="fa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wcfm-cpt' ) . '"></span></a>' : '';
				}
				
				$wcfm_event_json_arr[$index][] =  apply_filters ( 'wcfm_event_actions',  $actions, $wcfm_event_single );
				
				
				$index++;
			}												
		}

		$wcfm_event_json_array['data'] = $wcfm_event_json_arr;

		wp_send_json($wcfm_event_json_array);

	}
}