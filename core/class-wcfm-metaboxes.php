<?php

if (!defined('ABSPATH')) {
exit;
}

class Wolf_WCFM_Metaboxes {

	protected $post_id;
	protected $fields = [];
	protected $filter_tag;

	public function __construct($post_id, $fields = [], $filter_tag = '') {
		$this->post_id    = $post_id;
		$this->fields     = $fields;
		$this->filter_tag = $filter_tag;
	}

	public function populate_field_values() {
		if (!$this->post_id || empty($this->fields)) return;

		foreach ($this->fields as $key => $field) {
			$this->fields[$key]['value'] = get_post_meta($this->post_id, $key, true);
		}
	}

	public function get_fields() {
		$this->populate_field_values();

		if ($this->filter_tag) {
			return apply_filters($this->filter_tag, $this->fields, $this->post_id);
		}

		return $this->fields;
	}

	public function render_fields() {
		global $WCFM;
		$WCFM->wcfm_fields->wcfm_generate_form_field($this->get_fields());
	}
}

