<?php
return array(
	'_wolf_event_start_date' => array(
		'label'       => esc_html__( 'Date', 'wolf-events' ),
		'type'        => 'datepicker',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'Formatted like "dd-mm-yyyy"', 'wolf-events' ),
	),

	'_wolf_event_end_date' => array(
		'label'       => esc_html__( 'End Date', 'wolf-events' ),
		'type'        => 'datepicker',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_venue' => array(
		'label'       => esc_html__( 'Venue', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_location' => array(
		'label'       => esc_html__( 'Location', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'Display location name as you wish (e.g: "Bruges, Belgium")', 'wolf-events' ),
	),

	'_wolf_event_city' => array(
		'label'       => esc_html__( 'City', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_country' => array(
		'label'       => esc_html__( 'Country', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_country_short' => array(
		'label'       => esc_html__( 'Country - short (e.g : GER)', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_state' => array(
		'label'       => esc_html__( 'State', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_time' => array(
		'label'       => esc_html__( 'Time', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'e.g: 20:30 or 8:30PM', 'wolf-events' ),
	),

	'_wolf_event_address' => array(
		'label'       => esc_html__( 'Postal Address', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_zip' => array(
		'label'       => esc_html__( 'Zip', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_phone' => array(
		'label'       => esc_html__( 'Phone', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_email' => array(
		'label'       => esc_html__( 'Contact Email', 'wolf-events' ),
		'type'        => 'text', // 'email' is not a native WCFM field type
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_website' => array(
		'label'       => esc_html__( 'Contact Website', 'wolf-events' ),
		'type'        => 'text', // WCFM doesn't have 'url', use 'text'
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_map' => array(
		'label'       => esc_html__( 'Google map embed code', 'wolf-events' ),
		'type'        => 'textarea',
		'class'       => 'wcfm-textarea',
		'label_class' => 'wcfm_title',
		'hints'       => sprintf(
			__( '<a class="wolf-help-img" href="%s" target="_blank">Where to find it?</a>', 'wolf-events' ),
			WE_URI . '/assets/img/admin/google-map.jpg'
		),
	),

	'_wolf_event_fb' => array(
		'label'       => esc_html__( 'Facebook event page', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_bit' => array(
		'label'       => esc_html__( 'Bandsintown event page', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_ticket' => array(
		'label'       => esc_html__( 'Buy Ticket link', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'http://www.example.com', 'wolf-events' ),
	),

	'_wolf_event_price' => array(
		'label'       => esc_html__( 'Price (e.g : $15)', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_wolf_event_currency' => array(
		'label'       => esc_html__( 'Currency (e.g : USD)', 'wolf-events' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	/* '_wolf_event_free_hidden' => array( */
	/* 	'type'        => 'hidden', */
	/* 	'value'       => '0', */
	/* 	'name'  => '_wolf_event_free', // same name as checkbox */
	/* ), */

	'_wolf_event_free' => array(
		'label'       => esc_html__( 'Free', 'wolf-events' ),
		'type'        => 'checkbox',
		'class'       => 'wcfm-checkbox',
		'label_class' => 'wcfm_title',

		'value'       => 'yes',
/* 'dfvalue' => ( get_post_meta( $post_id, '_wolf_event_free', true ) === 'yes' ) ? 'yes' : '', */
	),

	/**/
	/* '_wolf_event_soldout_hidden' => array( */
	/* 	'type'        => 'hidden', */
	/* 	'value'       => '0', */
	/* 	'name'  => '_wolf_event_soldout', // same name as checkbox */
	/* ), */
	'_wolf_event_soldout' => array(
		'label'       => esc_html__( 'Sold Out', 'wolf-events' ),
		'type'        => 'checkbox',
		'class'       => 'wcfm-checkbox',
		'label_class' => 'wcfm_title',
		'value'       => 'yes',
/* 'dfvalue' => ( get_post_meta( $post_id, '_wolf_event_soldout', true ) === 'yes' ) ? 'yes' : '', */
	),
/* '_wolf_event_cancel_hidden' => array( */
/* 		'type'        => 'hidden', */
/* 		'value'       => '0', */
/* 		'name'  => '_wolf_event_soldout', // same name as checkbox */
/* 	), */

	'_wolf_event_cancel' => array(
		'label'       => esc_html__( 'Cancelled', 'wolf-events' ),
		'type'        => 'checkbox',
		'class'       => 'wcfm-checkbox',
		'label_class' => 'wcfm_title',
		'value'       => 'yes',
/* 'dfvalue' => ( get_post_meta( $post_id, '_wolf_event_cancel', true ) === 'yes' ) ? 'yes' : '', */
	),
);
