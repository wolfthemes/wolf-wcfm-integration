<?php
/**
 * WCFM plugin controllers
 *
 * Plugin work Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/work
 * @version   1.0.0
 */

class WCFM_Work_Controller {

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
			'post_type'        => 'work',
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

		if( isset($_POST['work_status']) && !empty($_POST['work_status']) ) $args['post_status'] = $_POST['work_status'];

		// Multi Vendor Support
		$is_marketplace = wcfm_is_marketplace();
		if( $is_marketplace ) {
			if( isset($_POST['work_vendor']) && !empty($_POST['work_vendor']) ) {
				if( !wcfm_is_vendor() ) {
					$args['author'] = $_POST['work_vendor'];
				}
			}
			if( wcfm_is_vendor() ) {
				$args['author'] = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		}

		$args = apply_filters( 'wcfm_work_args', $args );

		$wcfm_work_array = get_posts( $args );

		$work_count = 0;
		$filtered_work_count = 0;
		if( wcfm_is_vendor() ) {
			// Get work Count
			$for_count_args['posts_per_page'] = -1;
			$for_count_args['offset'] = 0;
			$for_count_args = apply_filters( 'wcfm_work_args', $for_count_args );
			$wcfm_work_count = get_posts( $for_count_args );
			$work_count = count($wcfm_work_count);

			// Get Filtered Post Count
			$args['posts_per_page'] = -1;
			$args['offset'] = 0;
			$wcfm_filterd_work_array = get_posts( $args );
			$filtered_work_count = count($wcfm_filterd_work_array);
		} else {
			// Get work Count
			$wcfm_work_counts = wp_count_posts('post');
			foreach($wcfm_work_counts as $wcfm_work_type => $wcfm_work_count ) {
				if( in_array( $wcfm_work_type, array( 'publish', 'draft', 'pending' ) ) ) {
					$work_count += $wcfm_work_count;
				}
			}

			// Get Filtered Post Count
			$filtered_work_count = $work_count;
		}

		// Generate work JSON
		$wcfm_work_json = '';
		$wcfm_work_json = '{
			"draw": ' . $_POST['draw'] . ',
			"recordsTotal": ' . $work_count . ',
			"recordsFiltered": ' . $filtered_work_count . ',
			"data": ';

		$wcfm_work_json_array = array();

		$wcfm_work_json_array['draw'] = $_POST['draw'];
		$wcfm_work_json_array['recordsTotal'] = absint( $work_count );
		$wcfm_work_json_array['recordsFiltered'] = absint( $filtered_work_count );

		if(!empty($wcfm_work_array)) {
			$index = 0;
			$wcfm_work_json_arr = array();
			foreach($wcfm_work_array as $wcfm_work_single) {

				if ( get_the_post_thumbnail_url( $wcfm_work_single->ID ) ) {
					$f_image = '<img width="40" height="40" class="attachment-thumbnail size-thumbnail wp-post-image" src="' . get_the_post_thumbnail_url( $wcfm_work_single->ID ) . '" />';
				} else {
					$f_image = '';
				}

				// Thumb
				if( apply_filters( 'wcfm_is_allow_edit_work', true ) ) {
					$wcfm_work_json_arr[$index][] =  '<a href="' . get_wcfm_cpt_manage_url( 'work', $wcfm_work_single->ID ) . '">' . $f_image . '</a>';
				} else {
					$wcfm_work_json_arr[$index][] = $f_image;
				}

				// Title
				if( apply_filters( 'wcfm_is_allow_edit_work', true ) ) {
					$wcfm_work_json_arr[$index][] =  '<a href="' . get_wcfm_cpt_manage_url( 'work', $wcfm_work_single->ID ) . '" class="wcfm_work_title wcfm_dashboard_item_title">' . $wcfm_work_single->post_title . '</a>';
				} else {
					if( $wcfm_work_single->post_status == 'publish' ) {
						$wcfm_work_json_arr[$index][] =  apply_filters( 'wcfm_work_title_dashboard', $wcfm_work_single->post_title, $wcfm_work_single->ID );
					} elseif( apply_filters( 'wcfm_is_allow_edit_work', true ) ) {
						$wcfm_work_json_arr[$index][] =  apply_filters( 'wcfm_work_title_dashboard', '<a href="' . get_wcfm_cpt_manage_url( 'work', $wcfm_work_single->ID ) . '" class="wcfm_work_title wcfm_dashboard_item_title">' . $wcfm_work_single->post_title . '</a>', $wcfm_work_single->ID );
					} else {
						$wcfm_work_json_arr[$index][] =  apply_filters( 'wcfm_work_title_dashboard', $wcfm_work_single->post_title, $wcfm_work_single->ID );
					}
				}

				// Status
				if( $wcfm_work_single->post_status == 'publish' ) {
					$wcfm_work_json_arr[$index][] =  '<span class="work-status work-status-' . $wcfm_work_single->post_status . '">' . __( 'Published', 'wcfm-cpt' ) . '</span>';
				} else {
					$wcfm_work_json_arr[$index][] =  '<span class="work-status work-status-' . $wcfm_work_single->post_status . '">' . __( ucfirst( $wcfm_work_single->post_status ), 'wcfm-cpt' ) . '</span>';
				}

				// Views
				$wcfm_work_json_arr[$index][] =  '<span class="view_count">' . (int) get_post_meta( $wcfm_work_single->ID, '_wcfm_work_views', true ) . '</span>';

				// Taxonomies
				$taxonomies = '';
				$product_taxonomies = get_object_taxonomies( 'work', 'objects' );
				if( !empty( $product_taxonomies ) ) {
					foreach( $product_taxonomies as $product_taxonomy ) {
						if( !in_array( $product_taxonomy->name, array( 'post_tag' ) ) ) {
							if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
								// Fetching Saved Values
								$taxonomy_values = get_the_terms( $wcfm_work_single->ID, $product_taxonomy->name );
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
				$wcfm_work_json_arr[$index][] =  $taxonomies;

				// Author
				$author = get_user_by( 'id', $wcfm_work_single->post_author );
				if( $author ) {
					$wcfm_work_json_arr[$index][] =  $author->display_name;
				} else {
					$wcfm_work_json_arr[$index][] =  '&ndash;';
				}

				// Date
				$wcfm_work_json_arr[$index][] =  date_i18n( wc_date_format(), strtotime($wcfm_work_single->post_date) );

				// Action
				$actions = '<a class="wcfm-action-icon" target="_blank" href="' . get_permalink( $wcfm_work_single->ID ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wcfm-cpt' ) . '"></span></a>';

				if( $wcfm_work_single->post_status == 'publish' ) {
					$actions .= ( apply_filters( 'wcfm_is_allow_edit_work', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_cpt_manage_url( 'work', $wcfm_work_single->ID ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wcfm-cpt' ) . '"></span></a>' : '';
					$actions .= ( apply_filters( 'wcfm_is_allow_delete_work', true ) ) ? '<a class="wcfm-action-icon wcfm_work_delete" href="#" data-workid="' . $wcfm_work_single->ID . '"><span class="fa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wcfm-cpt' ) . '"></span></a>' : '';
				} else {
					$actions .= ( apply_filters( 'wcfm_is_allow_edit_work', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_cpt_manage_url( 'work', $wcfm_work_single->ID ) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wcfm-cpt' ) . '"></span></a>' : '';
					$actions .= ( apply_filters( 'wcfm_is_allow_delete_work', true ) ) ? '<a class="wcfm_work_delete wcfm-action-icon" href="#" data-workid="' . $wcfm_work_single->ID . '"><span class="fa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wcfm-cpt' ) . '"></span></a>' : '';
				}

				$wcfm_work_json_arr[$index][] =  apply_filters ( 'wcfm_work_actions',  $actions, $wcfm_work_single );


				$index++;
			}
		}

		$wcfm_work_json_array['data'] = $wcfm_work_json_arr;

		wp_send_json($wcfm_work_json_array);

	}
}
