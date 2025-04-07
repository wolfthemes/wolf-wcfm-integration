<?php

return array(
	'_video_short_title' => array(
		'label'       => esc_html__( 'Video Short Title', '%TEXTDOMAIN%' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'A short version of the title in the video post slider.', '%TEXTDOMAIN%' ),
	),

	'_video_tagline' => array(
		'label'       => esc_html__( 'Video Tagline', '%TEXTDOMAIN%' ),
		'type'        => 'text',
		'class'       => 'wcfm-text',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'A short tagline to display below the short title.', '%TEXTDOMAIN%' ),
	),

	'_wvc_video_post_preview' => array(
		'label'       => esc_html__( 'Video Preview', '%TEXTDOMAIN%' ),
		'type'        => 'file',
		'class'       => 'wcfm_file_upload',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'An mp4, Vimeo, or YouTube URL to use as preview in the video post slider.', '%TEXTDOMAIN%' ),
	),

	'_video_preview_high_res' => array(
		'label'       => esc_html__( 'HD Video Preview', '%TEXTDOMAIN%' ),
		'type'        => 'file',
		'class'       => 'wcfm_file_upload',
		'label_class' => 'wcfm_title',
		'hints'       => esc_html__( 'A better quality video preview for the video scroller.', '%TEXTDOMAIN%' ),
	),

	'_post_layout' => array(
		'label'       => esc_html__( 'Layout', '%TEXTDOMAIN%' ),
		'type'        => 'select',
		'class'       => 'wcfm-select wcfm_eleg',
		'label_class' => 'wcfm_title',
		'options'     => array(
			''              => '&mdash; ' . esc_html__( 'Default', '%TEXTDOMAIN%' ) . ' &mdash;',
			'standard'      => esc_html__( 'Standard', '%TEXTDOMAIN%' ),
			'sidebar-right' => esc_html__( 'Sidebar Right', '%TEXTDOMAIN%' ),
			'sidebar-left'  => esc_html__( 'Sidebar Left', '%TEXTDOMAIN%' ),
			'fullwidth'     => esc_html__( 'Full Width', '%TEXTDOMAIN%' ),
		),
	),
);
