<?php
/*
Plugin Name: Simple Range Slider for Gravityforms
Plugin URI:  http://maxsebes.nl
Description: Simple Range Slider Plugin for Gravityforms
Version:     1.0.0
Author:      Max
Author URI:  http://maxsebes.nl
*/

defined( 'ABSPATH' ) or die( 'Direct path not allowed!' );

// Set default values when adding a slider
function gsf_set_defaults() {
	?>
	    case "slider" :
	        field.slider_step = 1;
	    break;
	<?php
} // end gsf_set_defaults
add_action( 'gform_editor_js_set_default_values', 'gsf_set_defaults' );

// Execute javascript for proper loading of field
function gsf_editor_js(){
	?>
		<script type='text/javascript'>
			jQuery(document).ready(function($) {
				
				// Bind to the load field settings event to initialize the slider settings
				$(document).bind("gform_load_field_settings", function(event, field, form){
					jQuery("#slider_step").val(field['slider_step']);
				});

			});
		</script>
	<?php
} // end gsf_editor_js
add_action( 'gform_editor_js', 'gsf_editor_js' );

function gsf_slider_settings( $position, $form_id ) {
	
	// Create settings on position 1550 (right after range option)
	if ( 25 == $position ) {
		?>
			<li class="slider_step field_setting">
				<div style="clear:both;">
					Step:
				</div>
				<div style="width:25%;"><input type="number" id="slider_step" step=".01" style="width:100%;" onchange="SetFieldProperty('slider_step', this.value);" /></div>
			</li>
		<?php
	}
} // end gsf_slider_settings
add_filter( 'gform_field_standard_settings' , 'gsf_slider_settings' , 10, 2 );


class GF_Field_Slider extends GF_Field {

    public $type = 'slider';
	
	public function get_form_editor_field_title() {
    return esc_attr__( 'Slider', 'gravityforms' );
	}
		
	public function get_form_editor_button() {
    return array(
        'group' => 'advanced_fields',
        'text'  => $this->get_form_editor_field_title()
		);
	}
	
	function get_form_editor_field_settings() {
    return array(
        'conditional_logic_field_setting',
        'prepopulate_field_setting',
        'error_message_setting',
        'label_setting',
        'label_placement_setting',
        'admin_label_setting',
        'size_setting',
        'rules_setting',
        'duplicate_setting',
        'default_value_setting',
        'placeholder_setting',
        'description_setting',
        'css_class_setting',
		'range_setting',
		'slider_step'
	);
	}
	
	public function is_conditional_logic_supported() {
    return true;
	}
	
	public function get_field_input( $form, $value = '', $entry = null ) {
    $form_id         = $form['id'];
    $is_entry_detail = $this->is_entry_detail();
    $id              = (int) $this->id;

    if ( $is_entry_detail ) {
        $input = "<input type='hidden' id='input_{$id}' name='input_{$id}' value='{$value}' />";

        return $input;
    }

    $disabled_text         = $this->is_form_editor() ? 'disabled="disabled"' : '';
    $logic_event           = $this->get_conditional_logic_event( 'change' );
    $placeholder_attribute = $this->get_field_placeholder_attribute();
		
	$min = $this->rangeMin;
	$max = $this->rangeMax;

	$min_attr = is_numeric( $min ) ? "min='{$min}'" : '';
	$max_attr = is_numeric( $max ) ? "max='{$max}'" : '';
		
	$step = ( isset( $this->slider_step ) && '' != $this->slider_step ) ? $this->slider_step : 1;
	$step_attr       = "step='{$this->slider_step}'";

    $input = "<div class='ginput_container' id='gf_range_slider_container_{$form_id}'><input id='slider_{$form_id}' name='input_%d' class='gf_range_slider' type='range' {$step_attr}{$min_attr} {$max_attr}{$disabled_text} {$placeholder_attribute} " . $this->get_tabindex() . "' value='{$value}'/></div>";

    return $input;
}
	
	public function sanitize_entry_value( $value, $form_id ) {    

   //makes sure submitted value is a positive integer
   $value = absint( $value );

   //return sanitized value
   return $value;
}
	
	
		
}

GF_Fields::register( new GF_Field_Slider() );