<?php
global $wp, $WCFM, $WCFMcpt;

// Load the class
require_once WWCFI_DIR . '/core/class-wcfm-metaboxes.php';

// Load metabox config
$video_fields_config = require WWCFI_DIR . '/config/video-metaboxes.php';

// Instantiate the generator
$metabox = new Wolf_WCFM_Metaboxes($video_id, $video_fields_config, 'wcfm_video_fields_custom');
?>

<!-- collapsible 1 -->
<div class="page_collapsible video_manager_custom" id="wcfm_video_manager_form_custom_head">
	<label class="fa fa-database"></label><?php printf( __('%s Options', 'wcfm-cpt'), 'Video' ); ?><span></span>
</div>
<div class="wcfm-container">
	<div id="wcfm_video_manager_form_custom_expander" class="wcfm-content">
		<?php $metabox->render_fields(); ?>
	</div>
</div>
<div class="wcfm_clearfix"></div>
