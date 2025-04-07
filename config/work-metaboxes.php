<?php

return array(
	'_post_work_alt_title' => array(
		'label'       => esc_html__( 'Single Post Alt Title', '%TEXTDOMAIN%' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'An alternative title to show in the title location on the single post (e.g: "Details & Info").', '%TEXTDOMAIN%' ),
	),

	'_work_video_bg' => array(
		'label'       => esc_html__( 'Entry Video Background', '%TEXTDOMAIN%' ),
		'type'        => 'file',
		'class'       => 'wcfm_file_upload',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'A video background to show in the work grid if the video format is used. By default, the first video in the post is used.', '%TEXTDOMAIN%' ),
	),

	'_work_masonry_img' => array(
		'label'       => esc_html__( 'Masonry Thumbnail', '%TEXTDOMAIN%' ),
		'type'        => 'upload',
		'class'       => 'wcfm_gallery_upload',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'An optional alternative featured image to use in the masonry layout.', '%TEXTDOMAIN%' ),
	),

	'_work_client' => array(
		'label'       => esc_html__( 'Client', '%TEXTDOMAIN%' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_work_link' => array(
		'label'       => esc_html__( 'Link', '%TEXTDOMAIN%' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
	),

	'_post_width' => array(
		'label'       => esc_html__( 'Width', '%TEXTDOMAIN%' ),
		'type'        => 'select',
		'class'       => 'wcfm-select wcfm_eleg',
		'label_class' => 'wcfm_title',
		'options'     => array(
			'standard'  => esc_html__( 'Standard', '%TEXTDOMAIN%' ),
			'wide'      => esc_html__( 'Wide', '%TEXTDOMAIN%' ),
			'fullwidth' => esc_html__( 'Full Width', '%TEXTDOMAIN%' ),
		),
	),

	'_post_layout' => array(
		'label'       => esc_html__( 'Layout', '%TEXTDOMAIN%' ),
		'type'        => 'select',
		'class'       => 'wcfm-select wcfm_eleg',
		'label_class' => 'wcfm_title',
		'options'     => array(
			'centered'      => esc_html__( 'Centered', '%TEXTDOMAIN%' ),
			'sidebar-right' => esc_html__( 'Excerpt & Info at Right', '%TEXTDOMAIN%' ),
			'sidebar-left'  => esc_html__( 'Excerpt & Info at Left', '%TEXTDOMAIN%' ),
		),
	),

	'_post_work_info_position' => array(
		'label'       => esc_html__( 'Excerpt & Info Position', '%TEXTDOMAIN%' ),
		'type'        => 'select',
		'class'       => 'wcfm-select wcfm_eleg',
		'label_class' => 'wcfm_title',
		'options'     => array(
			'after'  => esc_html__( 'After Content', '%TEXTDOMAIN%' ),
			'before' => esc_html__( 'Before Content', '%TEXTDOMAIN%' ),
			'none'   => esc_html__( 'Hidden', '%TEXTDOMAIN%' ),
		),
	),
);
