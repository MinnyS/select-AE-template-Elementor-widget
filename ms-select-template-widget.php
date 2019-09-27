<?php

//don't allow direct access to file
if (!defined('ABSPATH')) {
    exit;
}

//code for MS Select Template widget
class MS_Select_Template_Widget extends \Elementor\Widget_Base {
	
	public function get_name() {
		return 'MS-AE-Select-Template-Widget';
	}
	
	public function get_title() {
		return __('MS AE Select Template', 'wts_ae');
	}
	
	public function get_icon() {
		return 'fa fa-folder-open'; //folder icon indicating template
	}
	
	public function get_categories() {
		return ['general'];
	}
	
	//dynamic controls
	protected function _register_controls() {
		
		//start 'content' controls section
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'wts_ae' ),
			]
		);
		
		//create empty array for AE templates $key->$value info
		global $ms_ae_templates_info;
		$ms_ae_templates_info = array();
		
		//conditions/parameters/arguments for new WP_Query
		$args = array(
			'post_type' => 'ae_global_templates', //Anywhere Elementor template post type
			'post_status' => 'publish', //only list published posts/templates
			'order_by' => 'title', //order by post title
			'order' => 'ASC', //alphabetical order
		);
		
		$the_query = new WP_Query( $args ); //query the database
		
		$ms_has_templates = $the_query->have_posts(); //whether or not AE template posts are available
		
		//if posts are available
		if( $ms_has_templates ) {
			
			//for every post
			while( $the_query->have_posts() ) {
			
				$the_query->the_post(); //get the post info
				
				//add info to AE template info array as $key->$value pair
				$ms_ae_templates_info[ get_the_id() ] = get_the_title(); //key as post id, value as post title
				
			}

		} else {
		
			echo 'So sorry, no AE Templates were found.  Please create and publish an Anywhere Elementor template.';
		
		}
		
		$ms_ae_templates_info['default'] = '--no template selected--'; //add on top default option when no template selected
		
		//select AE template control
		$this->add_control(
			'ae-template',
			[
				'label' => __( ($ms_has_templates == true ? 'AE Template' : 'No AE templates found') , 'wts_ae' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $ms_ae_templates_info,
				'default' => 'default', //default will always be option with 'default' key
			]
		);
		
		$this->end_controls_section(); //end content controls section
		
	}
	
	protected function render() {
	
		$settings = $this->get_settings_for_display(); //get control settings
		
		$ms_template_shortcode_opening = '[INSERT_ELEMENTOR id="'; //opening for AE shortcode
		$ms_template_shortcode_closing = '"]'; //closing for AE shortcode
		
		//wrap in div with special classes
		echo '<div class="shortcode-ae-elementor-template shortcode-elementor-template anywhere-elementor anywhere-elementor-template elementor-template">';
		
		//if a template is selected
		if ( $settings['ae-template'] !== 'default') {
			echo do_shortcode( $ms_template_shortcode_opening . $settings['ae-template'] . $ms_template_shortcode_closing );
		}
		
		echo '</div>'; //close div
		
	}
	
}
