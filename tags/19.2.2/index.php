<?php
/*
Plugin Name: Garrett County Planning Tool (GCPT)
Plugin URI: https://mygarrettcounty.com
Author: Garrett County Health Department
Author URI: https://garretthealth.org
Description: This plug-in is an open source population health framework built in Garrett County, Maryland, merged with the UCPT.
Version: 19.2.2
Text Domain: ucpt
Requires at least: 5.2.2
Tested up to: 5.2.2
License: GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**
  *Because Health EQUITY MATTERS.
  */

/**
  *BuddyPress Check
  */
  
	register_activation_hook( __FILE__, 'ucpt_child_plugin_activate' );
	function ucpt_child_plugin_activate(){
		// Require BuddyPress
		if ( ! is_plugin_active( 'buddypress/bp-loader.php' ) and current_user_can( 'activate_plugins' ) ) {
			// Error Message
			wp_die('Sorry, but this plugin requires BuddyPress to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
		}
	}
		
/**
  *Globals and Resets
  */
  
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	define('UCPT_URL', plugin_dir_url( __FILE__ ));
	define('UCPT_OPTIONS', get_option( 'ucpt_manage_settings' ));
	define('UCPT_EDITOR_SETTINGS', array( 'media_buttons' => false ));
  
	function ucpt_custom_field_meta($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}
	
/**
  *Libraries
  */
  
	function ucpt_charts() {
		wp_enqueue_script('chart_js', UCPT_URL . 'js/charts/Chart.min.js');
	}

	function ucpt_style() {
		wp_enqueue_style('ucpt_css', UCPT_URL . 'css/ucpt.css');
		wp_enqueue_style('data_css_1', UCPT_URL . 'css/tables/jquery.dataTables.min.css');
		wp_enqueue_style('data_css_2', UCPT_URL . 'css/tables/buttons.dataTables.min.css');
	}

	function ucpt_script_head() {
		wp_enqueue_script('data_js_cf', UCPT_URL . 'js/tables/noConflict.js');
	}
		
	function ucpt_script() {
		wp_enqueue_script('data_js_1', UCPT_URL . 'js/tables/jquery.dataTables.js');
		wp_enqueue_script('data_js_2', UCPT_URL . 'js/tables/dataTables.fixedColumns.min.js');
		wp_enqueue_script('data_js_3', UCPT_URL . 'js/tables/dataTables.buttons.min.js');
		wp_enqueue_script('data_js_4', UCPT_URL . 'js/tables/jszip.min.js');
		wp_enqueue_script('data_js_5', UCPT_URL . 'js/tables/buttons.html5.min.js');
		wp_enqueue_script('data_js_6', UCPT_URL . 'js/tables/buttons.print.min.js');
		wp_enqueue_script('data_js_7', UCPT_URL . 'js/tables/dataTables.select.min.js');
	}
	
	function ucpt_credits() {
		if (UCPT_OPTIONS['ucpt_manage_credits'] == true) {
		?>
			<div class="division">
				<p>
					<h3>Planning Tool Info:</h3>
				</p>
				<p>
					<a href="https://equityengage.com">Open source health equity platform</a> powered by the <a href="https://equityengage.com/universal-community-planning-tool/">Universal Community Planning Tool</a> (UCPT), <a href="https://buddypress.org/">BuddyPress</a>, and <a href="https://wordpress.org/">WordPress</a>. 
					<br />
					Built with ❤️ in <a href="https://garretthealth.org">Garrett County, Maryland</a>. 
					<br />
					Expanded development and open source release of this plug-in was sponsored by the <a href="https://phnci.org">Public Health National Center for Innovations (PHNCI)</a>, a division of the <a href="http://www.phaboard.org/">Public Health Accreditation Board (PHAB)</a>, and the <a href="https://www.rwjf.org/">Robert Wood Johnson Foundation (RWJF)</a>.
					<br />
					<small>Related: assessments, informatics, population health, hyper local data, measurement, open data, open source, community engagement, health equity, community solutions, data dashboard</small>
				</p>
			</div>
		<?php
		}
	}
	
	$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if (strpos($url,'groups') !== false) {
		add_action('wp_head','ucpt_style');
		add_action('wp_enqueue_scripts','ucpt_script_head');
		add_action('wp_enqueue_scripts','ucpt_script');
		add_action('wp_enqueue_scripts', 'ucpt_charts');
	} 
	else {
	}

/**
  *Options Page Settings
  */

	// WordPress Options API
	add_action( 'admin_menu', 'ucpt_manage_add_admin_menu' );
	add_action( 'admin_init', 'ucpt_manage_settings_init' );

	function ucpt_manage_add_admin_menu(  ) { 
		add_menu_page( 'GCPT', 'GCPT', 'manage_options', 'ucpt_manager_module', 'ucpt_manage_options_page' );
	}

	function ucpt_manage_settings_init(  ) { 
		register_setting( 'pluginPage', 'ucpt_manage_settings' );
		add_settings_section(
			'ucpt_manage_pluginPage_section', 
			__( 'GCPT Configuration', 'ucpt_manage' ), 
			'ucpt_manage_settings_section_callback', 
			'pluginPage'
		);
		add_settings_field( 
			'ucpt_manage_credits', 
			__( 'Show credits?', 'ucpt_manage' ), 
			'ucpt_manage_credits_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
		);
		add_settings_field( 
			'ucpt_manage_start_date', 
			__( 'UCPT Data Start Date', 'ucpt_manage' ), 
			'ucpt_manage_start_date_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
		);
		add_settings_field( 
			'ucpt_manage_measure_number', 
			__( 'UCPT Measure Number', 'ucpt_manage' ), 
			'ucpt_manage_measure_number_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
		);
		for ($i = 1; $i <= 10; $i++) {
			add_settings_field( 
			'ucpt_manage_priority_' . $i, 
			__( 'Priority Focus Area #' . $i, 'ucpt_manage' ), 
			'ucpt_manage_priority_' . $i . '_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
			);
		}
		for ($e = 1; $e <= 15; $e++) {
			add_settings_field( 
			'ucpt_manage_custom_categories_' . $e, 
			__( 'Custom Data Category #' . $e, 'ucpt_manage' ), 
			'ucpt_manage_custom_categories_' . $e . '_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
			);
		}
		for ($o = 1; $o <= 10; $o++) {
			add_settings_field( 
			'ucpt_manage_custom_embed_' . $o, 
			__( 'Custom Embed Host URL #' . $o . ' (i.e.; http://google.com/)', 'ucpt_manage' ), 
			'ucpt_manage_custom_embed_' . $o . '_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
			);
		}			
		for ($g = 1; $g <= 10; $g++) {
			add_settings_field( 
			'ucpt_manage_custom_location_' . $g, 
			__( 'Custom Location ' . $g . ' (i.e.; 21550, Garrett County, etc...)', 'ucpt_manage' ), 
			'ucpt_manage_custom_location_' . $g . '_render', 
			'pluginPage', 
			'ucpt_manage_pluginPage_section' 
			);
		}			
	}
	
	function ucpt_manage_credits_render(  ) { 
		if (UCPT_OPTIONS['ucpt_manage_credits'] == true) {
			echo '<input type="checkbox" name="ucpt_manage_settings[ucpt_manage_credits]" checked> Yes';
		}
		if (UCPT_OPTIONS['ucpt_manage_credits'] == false) {
			echo '<input type="checkbox" name="ucpt_manage_settings[ucpt_manage_credits]"> Yes';
		}
	}
	function ucpt_manage_start_date_render(  ) {
		echo '<input type="date" name="ucpt_manage_settings[ucpt_manage_start_date]" value="' . UCPT_OPTIONS['ucpt_manage_start_date'] . '">';
	}
	function ucpt_manage_measure_number_render(  ) { 
		echo '<input type="number" name="ucpt_manage_settings[ucpt_manage_measure_number]" max="15" value="' . UCPT_OPTIONS['ucpt_manage_measure_number'] . '">';
	}
	function ucpt_manage_priority_1_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_1]" value="' . UCPT_OPTIONS['ucpt_manage_priority_1'] . '">';
	}
	function ucpt_manage_priority_2_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_2]" value="' . UCPT_OPTIONS['ucpt_manage_priority_2'] . '">';
	}
	function ucpt_manage_priority_3_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_3]" value="' . UCPT_OPTIONS['ucpt_manage_priority_3'] . '">';
	}
	function ucpt_manage_priority_4_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_4]" value="' . UCPT_OPTIONS['ucpt_manage_priority_4'] . '">';
	}
	function ucpt_manage_priority_5_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_5]" value="' . UCPT_OPTIONS['ucpt_manage_priority_5'] . '">';
	}
	function ucpt_manage_priority_6_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_6]" value="' . UCPT_OPTIONS['ucpt_manage_priority_6'] . '">';
	}
	function ucpt_manage_priority_7_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_7]" value="' . UCPT_OPTIONS['ucpt_manage_priority_7'] . '">';
	}
	function ucpt_manage_priority_8_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_8]" value="' . UCPT_OPTIONS['ucpt_manage_priority_8'] . '">';
	}
	function ucpt_manage_priority_9_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_9]" value="' . UCPT_OPTIONS['ucpt_manage_priority_9'] . '">';
	}
	function ucpt_manage_priority_10_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_priority_10]" value="' . UCPT_OPTIONS['ucpt_manage_priority_10'] . '">';
	}
	function ucpt_manage_custom_categories_1_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_1]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_1'] . '">';
	}
	function ucpt_manage_custom_categories_2_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_2]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_2'] . '">';
	}
	function ucpt_manage_custom_categories_3_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_3]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_3'] . '">';
	}
	function ucpt_manage_custom_categories_4_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_4]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_4'] . '">';
	}
	function ucpt_manage_custom_categories_5_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_5]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_5'] . '">';
	}
	function ucpt_manage_custom_categories_6_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_6]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_6'] . '">';
	}
	function ucpt_manage_custom_categories_7_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_7]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_7'] . '">';
	}
	function ucpt_manage_custom_categories_8_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_8]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_8'] . '">';
	}
	function ucpt_manage_custom_categories_9_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_9]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_9'] . '">';
	}
	function ucpt_manage_custom_categories_10_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_10]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_10'] . '">';
	}
	function ucpt_manage_custom_categories_11_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_11]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_11'] . '">';
	}
	function ucpt_manage_custom_categories_12_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_12]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_12'] . '">';
	}
	function ucpt_manage_custom_categories_13_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_13]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_13'] . '">';
	}
	function ucpt_manage_custom_categories_14_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_14]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_14'] . '">';
	}
	function ucpt_manage_custom_categories_15_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_categories_15]" value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_15'] . '">';
	}
	function ucpt_manage_custom_embed_1_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_1]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_1'] . '">';
	}
	function ucpt_manage_custom_embed_2_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_2]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_2'] . '">';
	}
	function ucpt_manage_custom_embed_3_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_3]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_3'] . '">';
	}
	function ucpt_manage_custom_embed_4_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_4]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_4'] . '">';
	}
	function ucpt_manage_custom_embed_5_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_5]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_5'] . '">';
	}
	function ucpt_manage_custom_embed_6_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_6]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_6'] . '">';
	}
	function ucpt_manage_custom_embed_7_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_7]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_7'] . '">';
	}
	function ucpt_manage_custom_embed_8_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_8]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_8'] . '">';
	}
	function ucpt_manage_custom_embed_9_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_9]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_9'] . '">';
	}
	function ucpt_manage_custom_embed_10_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_embed_10]" value="' . UCPT_OPTIONS['ucpt_manage_custom_embed_10'] . '">';
	}
	function ucpt_manage_custom_location_1_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_1]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_1'] . '">';
	}
	function ucpt_manage_custom_location_2_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_2]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_2'] . '">';
	}
	function ucpt_manage_custom_location_3_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_3]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_3'] . '">';
	}
	function ucpt_manage_custom_location_4_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_4]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_4'] . '">';
	}
	function ucpt_manage_custom_location_5_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_5]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_5'] . '">';
	}
	function ucpt_manage_custom_location_6_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_6]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_6'] . '">';
	}
	function ucpt_manage_custom_location_7_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_7]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_7'] . '">';
	}
	function ucpt_manage_custom_location_8_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_8]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_8'] . '">';
	}
	function ucpt_manage_custom_location_9_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_9]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_9'] . '">';
	}
	function ucpt_manage_custom_location_10_render(  ) { 
		echo '<input type="text" name="ucpt_manage_settings[ucpt_manage_custom_location_10]" value="' . UCPT_OPTIONS['ucpt_manage_custom_location_10'] . '">';
	}
	function ucpt_manage_settings_section_callback(  ) { 
		echo __( 'Use these settings to customize your GCPT.', 'ucpt_manage' );
	}
	
	function ucpt_manage_options_page(  ) { 
		echo '<form action="options.php" method="post">';
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		echo '</form>';
	}
		
/*
Strategy Cards
*/

	function ucpt_strategy_page( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_Strategy extends BP_Group_Extension {
				var $enable_create_step = true;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'strategy',
						'name' => 'Strategy',
						'nav_item_position' => 40,
						'screens' => array(
							'create' => array(
								'position' => 10,
							),
						)
					);
					parent::init( $args );
				}
				function admin_screen( $group_id = null ) {
				echo "<p>These settings are configured via the front-end of your planning tool.</p>";
				}
				function admin_screen_save( $group_id = null ) {
				}			
				function settings_screen( $group_id = null ) {
				?>
				<div class="division">
					<p>
						<h3>Strategy Planning Worksheet</h3>
					</p>
					<p>An innovative feature, unique to the planning tool, is the ability to dynamically track strategy progress over time. This workspace is designed for multiple stakeholders to collaborate on one specific strategy.  Please explain the specific strategy you will be measuring in the space below.</p>

						<label for="ucpt_goal"><h3>Goal</h3></label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_goal'), 'ucpt_goal', UCPT_EDITOR_SETTINGS ); ?>

						<label for="ucpt_desc"><h3>Strategy Description</h3></label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_desc'), 'ucpt_desc', UCPT_EDITOR_SETTINGS ); ?> 

						<label for="ucpt_level"><h3>Level of Change</h3></label>
							<select name="ucpt_level">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_level'); ?>"><?php echo ucpt_custom_field_meta('ucpt_level'); ?></option>
								<option value="Policy">Policy</option>
								<option value="Systems">Systems</option>
								<option value="Programs">Program</option>
							</select>							
						<label for="ucpt_focus"><h3>Primary Focus Area</h3></label>
							<select name="ucpt_focus" style="max-width:90%;">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_focus'); ?>"><?php echo ucpt_custom_field_meta('ucpt_focus'); ?></option>
								<?php
								for ($i = 1; $i <= 10; $i++) {
									if (UCPT_OPTIONS['ucpt_manage_priority_' . $i] != "") {
										echo '<option value="' . UCPT_OPTIONS['ucpt_manage_priority_' . $i] . '">' . UCPT_OPTIONS['ucpt_manage_priority_' . $i] . '</option>';
									}
								}
								?>
							</select>
						
						<?php
							if (UCPT_OPTIONS['ucpt_manage_custom_categories_1'] != "") {	
						?>
						<label for="ucpt_category"><h3>Data Category Tag</h3></label>
							<select name="ucpt_category" style="max-width:90%;">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_category'); ?>"><?php echo ucpt_custom_field_meta('ucpt_category'); ?></option>
								<?php
								for ($i = 1; $i <= 15; $i++) {
									if (UCPT_OPTIONS['ucpt_manage_custom_categories_' . $i] != "") {
										echo '<option value="' . UCPT_OPTIONS['ucpt_manage_custom_categories_' . $i] . '">' . UCPT_OPTIONS['ucpt_manage_custom_categories_' . $i] . '</option>';
									}
								}
								?>
								<option value="">Reset the Data Category Tag to blank.</option>
							</select>
						<?php
							}
						?>

						<label for="ucpt_date_start"><h3>Estimated Implementation Date</h3></label>
							<input id="ucpt_date_start" type="date" name="ucpt_date_start" value="<?php echo ucpt_custom_field_meta('ucpt_date_start'); ?>" />

						<label for="ucpt_date_end"><h3>Estimated Completion Date</h3></label>
							<input id="ucpt_date_end" type="date" name="ucpt_date_end" value="<?php echo ucpt_custom_field_meta('ucpt_date_end'); ?>" />

						<label for="ucpt_cis_ease"><h3>Estimated Ease of Implementation</h3></label>
							<select name="ucpt_cis_ease">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_cis_ease'); ?>"><?php echo ucpt_custom_field_meta('ucpt_cis_ease'); ?></option>
								<option value="Very Easy">Very Easy</option>
								<option value="Easy">Easy</option>
								<option value="Moderate">Moderate</option>
								<option value="Hard">Hard</option>
								<option value="Very Hard">Very Hard</option>
							</select>

						<label for="ucpt_cis_cost"><h3>Estimated Cost of Implementation</h3></label>
							<select name="ucpt_cis_cost">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_cis_cost'); ?>"><?php echo ucpt_custom_field_meta('ucpt_cis_cost'); ?></option>
								<option value="Very Low">Very Low</option>
								<option value="Low">Low</option>
								<option value="Moderate">Moderate</option>
								<option value="High">High</option>
								<option value="Very High">Very High</option>
							</select>

						<label for="ucpt_cis_benefit"><h3>Estimated Potential Community Benefit</h3></label>
							<select name="ucpt_cis_benefit">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_cis_benefit'); ?>"><?php echo ucpt_custom_field_meta('ucpt_cis_benefit'); ?></option>
								<option value="Very High">Very High</option>
								<option value="High">High</option>
								<option value="Moderate">Moderate</option>
								<option value="Low">Low</option>
								<option value="Very Low">Very Low</option>
							</select>

						<label for="ucpt_research"><h3>Research</h3></label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_research'), 'ucpt_research', UCPT_EDITOR_SETTINGS ); ?> 
					<br />
				</div>
				<?php
				}    
				function settings_screen_save( $group_id = NULL ) {
						$plain_fields = array(
							'ucpt_goal',
							'ucpt_desc',
							'ucpt_level',
							'ucpt_focus',
							'ucpt_category',
							'ucpt_date_start',
							'ucpt_date_end',
							'ucpt_cis_ease',
							'ucpt_cis_cost',
							'ucpt_cis_benefit',
							'ucpt_research'
						);
						foreach( $plain_fields as $field ) {
							$key = $field;
							if ( isset( $_POST[$key] ) ) {
								$value = wp_filter_post_kses($_POST[$key]);
								groups_update_groupmeta( $group_id, $field, $value );
							}

						}
						$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );
						$activity_update = "Strategy card settings were updated by " . $editor_record . ".";
						if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
							groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
						}
				}  
				function display( $group_id = null ) {
					global $bp;
					$group_cover_image_url = bp_attachments_get_attachment('url', array(
						  'object_dir' => 'groups',
						  'item_id' => bp_get_group_id(),
						));
						$ucpt_cover = $group_cover_image_url;
						$ucpt_group_name = bp_get_group_name();
						$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
						if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						?>
						<p>
							<form method="get" action="<?php echo $ucpt_perma; ?>admin/strategy"><button type="submit" align="right">Edit Strategy Card</button></form>
						</p>
						<?php
						}
						?>
						<div style="background: linear-gradient(rgba(10, 118, 211, 0.85), rgba(8, 94, 168, 0.85)), url('<?php echo $ucpt_cover; ?>'); background-size:100%; width=100%; min-height: 150px; padding: 30px;"><div style="font-size: 32px; color: #fff;">Health Improvement Strategy</div><br /><div style="font-size: 18px; color: #efefef;">"<?php echo $ucpt_group_name; ?>"</div><br/><div style="font-size: 10px; color: #efefef;">"<?php echo $ucpt_perma; ?>"</div></div>
						<div class="division">
						<p><h3>Goal:</h3> <?php echo ucpt_custom_field_meta("ucpt_goal"); ?></p>
						</div>
						<div class="division">
						<p><h3>Strategy Description:</h3> <?php echo ucpt_custom_field_meta("ucpt_desc"); ?></p>
						</div>
						<div class="division">
						<p><h3>Level of Change:</h3> <?php echo ucpt_custom_field_meta("ucpt_level"); ?></p>
						</div>
						<div class="division">
						<p><h3>Primary Focus Area:</h3> <?php echo ucpt_custom_field_meta("ucpt_focus"); ?></p>
						</div>
						<div class="division">
						<p><h3>Data Category Tag:</h3> <?php echo ucpt_custom_field_meta("ucpt_category"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Implementation Date:</h3> <?php echo ucpt_custom_field_meta("ucpt_date_start"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Completion Date:</h3> <?php echo ucpt_custom_field_meta("ucpt_date_end"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Ease of Implementation:</h3> <?php echo ucpt_custom_field_meta("ucpt_cis_ease"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Cost of Implementation:</h3> <?php echo ucpt_custom_field_meta("ucpt_cis_cost"); ?></p>
						</div>
						<div class="division">
						<p><h3>Potential Community Benefit:</h3> <?php echo ucpt_custom_field_meta("ucpt_cis_benefit"); ?></p>
						</div>
						<div class="division">
						<p><h3>Research:</h3> <?php echo ucpt_custom_field_meta("ucpt_research"); ?></p>
						</div>
						<?php
						ucpt_credits();
				} 
			}
			bp_register_group_extension( 'UCPT_Strategy' );
			 
			endif;
		}
			
	add_action( 'bp_include', 'ucpt_strategy_page' );

/*
Raw Data +
*/

	function ucpt_data_page( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_Data_Pages extends BP_Group_Extension {
				var $enable_create_step = true;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'raw-data',
						'name' => 'Raw Data +',
						'nav_item_position' => 42,
						'screens' => array(
							'create' => array(
								'position' => 12,
							),
						)
					);
					parent::init( $args );
				}
				function admin_screen( $group_id = null ) {
				echo "<p>These settings are configured via the front-end of your planning tool.</p>";
				}
				function admin_screen_save( $group_id = null ) {
				}
				function settings_screen( $group_id = null ) {						
					?>
					<script>
						$(document).ready( function () {
							$('#myDataTableEdit').DataTable(
								{
									scrollX: true,
									order: [],
									scrollY: '500px',
									fixedColumns: true,
									scrollCollapse: true,
									paging: false,
									bFilter: false,
									ordering: false,
								}
							);
						} );
					</script>
						<div class="division">
							<h3>Data Planning Worksheet</h3>
							<p>The purpose of this planning tool is to collect community data for comparison, tracking, and overall community health improvement. Data plays a critical role in ensuring that our strategies are effective, and can be correlated to specific actions within the community. Data must be numerical to allow for cross comparison of variables.</p>
							<?php
								$ucpt_time = UCPT_OPTIONS['ucpt_manage_start_date'];
								$ucpt_start_date = date('Y', strtotime($ucpt_time));
								$ucpt_current_date = date('Y');
								$ucpt_cycle = ($ucpt_current_date - $ucpt_start_date) + 1;
							?>
						<p>
							<table id="myDataTableEdit" border="1" bordercolor="#ededed" width="100%" class="table table-striped">
								<thead>
									<tr>
										<th style="background-color:#fff;">Measurements</th>
										<th>Target Goal</th>
										<th>Status</th>
										<th>Desired Trend</th>
										<th>Contributor</th>
											<th>July <?php echo $ucpt_current_date - 1; ?></th>
											<th>August <?php echo $ucpt_current_date - 1; ?></th>
											<th>September <?php echo $ucpt_current_date - 1; ?></th>
											<th>October <?php echo $ucpt_current_date - 1; ?></th>
											<th>November <?php echo $ucpt_current_date - 1; ?></th>
											<th>December <?php echo $ucpt_current_date - 1; ?></th>
											<th>January <?php echo $ucpt_current_date; ?></th>
											<th>February <?php echo $ucpt_current_date; ?></th>
											<th>March <?php echo $ucpt_current_date; ?></th>
											<th>April <?php echo $ucpt_current_date; ?></th>
											<th>May <?php echo $ucpt_current_date; ?></th>
											<th>June <?php echo $ucpt_current_date; ?></th>
											<th>July <?php echo $ucpt_current_date; ?></th>
											<th>August <?php echo $ucpt_current_date; ?></th>
											<th>September <?php echo $ucpt_current_date; ?></th>
											<th>October <?php echo $ucpt_current_date; ?></th>
											<th>November <?php echo $ucpt_current_date; ?></th>
											<th>December <?php echo $ucpt_current_date; ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
									for ($i = 1; $i <= $max_measures; $i++) {
									?>
										<tr>
											<td>
												<textarea id="ucpt_measure_<?php echo $i; ?>" name="ucpt_measure_<?php echo $i; ?>" placeholder="Measure <?php echo $i; ?>" style="width:150px;height:100px;"><?php echo ucpt_custom_field_meta('ucpt_measure_' . $i); ?></textarea>
											</td>
											<td>
												<input id="ucpt_measure_<?php echo $i; ?>_goal" type="number" step="0.01" name="ucpt_measure_<?php echo $i; ?>_goal" placeholder="Target Goal" value="<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_goal'); ?>" />
											</td>
											<td>
											<select name="ucpt_measure_<?php echo $i; ?>_status">
												<option value="<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_status'); ?>"><?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_status'); ?></option>
												<option value="Active">Active</option>
												<option value="Archived">Archived</option>
											</select>
											</td>
											<td>
											<select name="ucpt_measure_<?php echo $i; ?>_trend">
												<option value="<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_trend'); ?>"><?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_trend'); ?></option>
												<option value="Increase">Increase</option>
												<option value="Decrease">Decrease</option>
											</select>
											</td>
											<td>
												<input id="ucpt_measure_<?php echo $i; ?>_contributor" type="text" name="ucpt_measure_<?php echo $i; ?>_contributor" placeholder="Person, agency, etc..." value="<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_contributor'); ?>" />
											</td>
											<?php
											$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
											$y_prev = $ucpt_cycle - 1;
											for ($m = 7; $m <= 12; $m++) {
											?>											
												<td>
													<input id="ucpt_m_<?php echo $i; ?>_y<?php echo $y_prev; ?>_m<?php echo $m; ?>" type="number" step="0.01" name="ucpt_m_<?php echo $i; ?>_y<?php echo $y_prev; ?>_m<?php echo $m; ?>" placeholder="Baseline" value="<?php echo ucpt_custom_field_meta('ucpt_m_' . $i . '_y' . $y_prev . '_m' . $m); ?>" />
												</td>
											<?php
											}
											$y = $ucpt_cycle;
											for ($m = 1; $m <= 12; $m++) {
											?>											
												<td>
													<input id="ucpt_m_<?php echo $i; ?>_y<?php echo $y; ?>_m<?php echo $m; ?>" type="number" step="0.01" name="ucpt_m_<?php echo $i; ?>_y<?php echo $y; ?>_m<?php echo $m; ?>" placeholder="Baseline" value="<?php echo ucpt_custom_field_meta('ucpt_m_' . $i . '_y' . $y . '_m' . $m); ?>" />
												</td>
											<?php
											}
											?>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</p>
						<h3>Data Narrative</h3>
						<p>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_data_narrative'), 'ucpt_data_narrative', UCPT_EDITOR_SETTINGS ); ?> 
						</p>
							<?php 
							$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );
							?>
						<h3>Last Modification</h3>
						<p>
							<input id="ucpt_data_edit" type="text" name="ucpt_data_edit" readonly="readonly" value="<?php echo $editor_record; ?> edited this group on <?php echo date("F d, Y"); ?>." />
							<input id="ucpt_data_edit_log" type="hidden" name="ucpt_data_edit_log" readonly="readonly" value="<?php echo ucpt_custom_field_meta('ucpt_data_edit_log'); ?>" />
						</p>
					</div>
					<?php
				}  
				function settings_screen_save( $group_id = NULL ) {
					$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
					$measure_count = 1;
					$ucpt_time = UCPT_OPTIONS['ucpt_manage_start_date'];
					$ucpt_start_date = date('Y', strtotime($ucpt_time));
					$ucpt_current_date = date('Y');
					$ucpt_cycle = ($ucpt_current_date - $ucpt_start_date) + 1;
					while ($measure_count <= $max_measures) {
						$plain_fields = array(
							'ucpt_measure_' . $measure_count,
							'ucpt_measure_' . $measure_count . '_goal',
							'ucpt_measure_' . $measure_count . '_status',
							'ucpt_measure_' . $measure_count . '_trend',
							'ucpt_measure_' . $measure_count . '_contributor'
						);
						foreach( $plain_fields as $field ) {
							$key = $field;
							if ( isset( $_POST[$key] ) ) {
								$value = wp_filter_post_kses($_POST[$key]);
								groups_update_groupmeta( $group_id, $field, $value );
							}
						}
						$y_prev = $ucpt_cycle - 1;
						$y = $ucpt_cycle;
						$plain_fields_data_points = array(
							'ucpt_m_' . $measure_count . '_y' . $y_prev . '_m7',
							'ucpt_m_' . $measure_count . '_y' . $y_prev . '_m8',
							'ucpt_m_' . $measure_count . '_y' . $y_prev . '_m9',
							'ucpt_m_' . $measure_count . '_y' . $y_prev . '_m10',
							'ucpt_m_' . $measure_count . '_y' . $y_prev . '_m11',
							'ucpt_m_' . $measure_count . '_y' . $y_prev . '_m12',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m1',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m2',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m3',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m4',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m5',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m6',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m7',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m8',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m9',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m10',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m11',
							'ucpt_m_' . $measure_count . '_y' . $y . '_m12'
						);
						foreach( $plain_fields_data_points as $field_data_points ) {
							$key_data_points = $field_data_points;
							if ( isset( $_POST[$key_data_points] ) ) {
								$value_data_points = wp_filter_post_kses($_POST[$key_data_points]);
								groups_update_groupmeta( $group_id, $field_data_points, $value_data_points );
							}
						}
						$measure_count++;
					}
					
					if ( isset( $_POST["ucpt_data_narrative"] ) ) {
							$value = $_POST["ucpt_data_narrative"];
							groups_update_groupmeta( $group_id, "ucpt_data_narrative", $value );
					}
					
					if ( isset( $_POST["ucpt_data_edit"] ) ) {
							$temp = $_POST["ucpt_data_edit"];
							$current = $_POST["ucpt_data_edit_log"];
							$value = $temp . "<br />" . $current;
							groups_update_groupmeta( $group_id, "ucpt_data_edit_log", $value );
					}
					$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );
					$activity_update = "Raw data + was updated by " . $editor_record . ".";
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
					}
				}  
				function display( $group_id = null ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;
					$group_cover_image_url = bp_attachments_get_attachment('url', array(
						  'object_dir' => 'groups',
						  'item_id' => bp_get_group_id(),
						));
					$ucpt_cover = $group_cover_image_url;
					$ucpt_group_name = bp_get_group_name();
					$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
					?>
					<p>
						<form method="get" action="<?php echo $ucpt_perma; ?>admin/raw-data"><button type="submit" align="right">Edit Data</button></form>
					</p>
					<?php
					}
					echo "<div style='background: linear-gradient(rgba(10, 118, 211, 0.85), rgba(8, 94, 168, 0.85)), url(" . $ucpt_cover . "); background-size:100%; width=100%; min-height: 150px; padding: 30px;'><div style='font-size: 32px; color: #fff;'>Raw Data +</div><br /><div style='font-size: 18px; color: #efefef;'>" . $ucpt_group_name . "</div><br/><div style='font-size: 10px; color: #efefef;'>" . $ucpt_perma ."</div></div>";
					?>
					<script>
					$(document).ready( function () {
						$('#myDataTable').DataTable(
							{
								scrollX: true,
								order: [],
								scrollY: '500px',
								fixedColumns: true,
								scrollCollapse: true,
								paging: false,
								dom: 'Bfrtip',
								buttons: [
									'selectAll', 'selectNone', 'copy', 'csv', 'excel', {
										text: 'JSON',
										action: function ( e, dt, button, config ) {
											var data = dt.buttons.exportData();
						 
											$.fn.dataTable.fileSave(
												new Blob( [ JSON.stringify( data ) ] ),
												'Export.json'
											);
										}
									}
								],
								select: {
									style: 'multi'
								}
							}
						);
					} );
					</script>
					<div class="division">
						<?php
							$ucpt_time = UCPT_OPTIONS['ucpt_manage_start_date'];
							$ucpt_start_date = date('Y', strtotime($ucpt_time));
							$ucpt_current_date = date('Y');
							$ucpt_cycle = ($ucpt_current_date - $ucpt_start_date) + 1;	
						?>
						<p>
							<table id="myDataTable" border="1" bordercolor="#ededed" width="100%" class="table table-striped">
								<thead>
									<tr>
										<th style="background-color:#fff;">Measurements</th>
										<th>Target Goal</th>
										<th>Status</th>
										<th>Desired Trend</th>
										<th>Contributor</th>
										<?php
										$ucpt_time_count = 0;
										while ($ucpt_time_count < $ucpt_cycle) {
										?>
											<th>January <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>February <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>March <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>April <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>May <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>June <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>July <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>August <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>September <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>October <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>November <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>December <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<?php
										$ucpt_time_count++;
										}
										?>
								</tr>
								</thead>
								<tbody>
								<?php
								$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
								for ($i = 1; $i <= $max_measures; $i++) {
									if (ucpt_custom_field_meta('ucpt_measure_' . $i . '') != "") {
								?>
									<tr>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . ''); ?>
										</td>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_goal'); ?>
										</td>
										<td>
										<?php if (ucpt_custom_field_meta('ucpt_measure_' . $i . '_status') == "Archived") { ?>
											<span style="background-color: #d71616; color: #fff; padding: 3px; ">Archived</span>
											<?php } else { ?>
											<span style="background-color: #129f49; color: #fff; padding: 3px; ">Active</span>
										<?php } ?>
										</td>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_trend'); ?>
										</td>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_contributor'); ?>
										</td>
										<?php
										$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
										$ucpt_time_count = 0;
										while ($ucpt_time_count < $ucpt_cycle) {
											$y = $ucpt_time_count + 1;
												for ($m = 1; $m <= 12; $m++) {
											?>											
												<td>
													<?php echo ucpt_custom_field_meta('ucpt_m_' . $i . '_y' . $y . '_m' . $m . ''); ?>
												</td>
											<?php
												}
										$ucpt_time_count++;
										}
										?>
									</tr>
								<?php
									}
								}
								?>
								</tbody>
							</table>
						</p>
					</div>
					
					<div class="division">
						<h3>Data Narrative</h3>
						<p>
							<?php echo ucpt_custom_field_meta('ucpt_data_narrative'); ?>
						</p>
					</div>
					
					<div class="division">
						<h3>Scaled Data Visualization</h3>
						<?php
						$chart_id = substr(md5(rand()), 0, 6);
						?>
						<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
						<script>
						 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
							var data = {
						  "labels": [
							"<?php
								$ucpt_time_count_main = 0;
								while ($ucpt_time_count_main < $ucpt_cycle) {
								$current_year = $ucpt_start_date + $ucpt_time_count_main;
							?>1/<?php echo $current_year; ?>",
							"2/<?php echo $current_year; ?>",
							"3/<?php echo $current_year; ?>",
							"4/<?php echo $current_year; ?>",
							"5/<?php echo $current_year; ?>",
							"6/<?php echo $current_year; ?>",
							"7/<?php echo $current_year; ?>",
							"8/<?php echo $current_year; ?>",
							"9/<?php echo $current_year; ?>",
							"10/<?php echo $current_year; ?>",
							"11/<?php echo $current_year; ?>",
							"12/<?php echo $current_year; ?>",
							"<?php
								$ucpt_time_count_main++;
								}
							?>",
						  ],
						  "datasets": [
							<?php
								$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
								$measure_count = 1;
								while ($measure_count <= $max_measures) {
								if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
							?>
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
							  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
							  "fill": false,
							  "data": [
								"<?php
								$ucpt_time_count_main_data = 0;
								while ($ucpt_time_count_main_data < $ucpt_cycle) {
								$current_year_data = $ucpt_time_count_main_data + 1;
								?><?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m1'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m2'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m3'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m4'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m5'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m6'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m7'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m8'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m9'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m10'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m11'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m12'); ?>",
								"<?php
								$ucpt_time_count_main_data++;
								}
								?>",
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "spanGaps": false
							},
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
							  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
							  "hidden": true,
							  "fill": false,
							  "data": [
								"<?php
								$ucpt_time_count_main = 0;
								while ($ucpt_time_count_main < $ucpt_cycle) {
								?><?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php
								$ucpt_time_count_main++;
								}
								?>",
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "borderDash": [5,1],
							  "spanGaps": false
							},
							<?php
								}
							$measure_count++;
							}
							?>
						  ]
						};
							var options = {
						  "title": {
							"display": true,
							"text": "Group Measures",
							"position": "bottom",
							"fontStyle": "bold",
							"fullWidth": true
						  },
						  "legend": {
							"display": true,
							"position": "bottom",
							"fullWidth": true
						  },
						  "scales": {
							"yAxes": [
							  {
								"ticks": {
								  "beginAtZero": true
								},
								"gridLines": {
								  "display": true,
								  "lineWidth": 1,
								  "drawOnChartArea": true,
								  "color": "#000000",
								  "zeroLineColor": "#000000",
								  "zeroLineWidth": 1,
								  "drawTicks": true
								}
							  }
							],
							"xAxes": {
							  "0": {
								"gridLines": {
								  "drawOnChartArea": false,
								  "offsetGridLines": false,
								  "zeroLineColor": "#000000",
								  "display": true,
								  "lineWidth": 2,
								  "drawTicks": true,
								  "zeroLineWidth": 2,
								  "color": "#000000"
								},
								"ticks": {
								  "display": true,
								  "beginAtZero": true
								}
							  }
							}
						  },
						  "elements": {
							"line": {
							  "borderColor": "#000000",
							  "lineTension": 0
							}
						  }
						};

							var myChart = new Chart(ctx, {
								type: 'line',
								data: data,
								options: options
							});
							

						</script>
					</div>
					
					<div class="division">
						<h3>Need more visualizations?</h3>
						<p>
							Check out the performance management tab to see if this group has additional data analysis options!
						</p>
					</div>
					
					<div class="division">
						<h3>Last Modification</h3>
						<p>
							<?php echo ucpt_custom_field_meta('ucpt_data_edit_log'); ?>
						</p>
					</div>
					
					<?php
					ucpt_credits();
				}
				
			}
			bp_register_group_extension( 'UCPT_Data_Pages' );
			 
			endif;
		}
				
	add_action( 'bp_include', 'ucpt_data_page' );
		
/*
CHIP
*/

	function custom_field_chip($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	function ucpt_chip_page( $group_id = null ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_CHIP_Pages extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'chip',
						'name' => 'CHIP Dash',
						'nav_item_position' => 42
					);
					parent::init( $args );
				}
				function settings_screen( $group_id = null ) {
				?>
				<div class="division">
					<h3>CHIP Strategy Configuration</h3>
					<p>Include your strategy in the upcoming Community Health Improvement Plan!</p>
					<?php
						if (custom_field_chip('ucpt_friendly') != "" || is_super_admin()) {
							
					?>
						<label for="ucpt_friendly">CHIP Friendly Title</label>
							<input id="ucpt_friendly" type="text" name="ucpt_friendly" value="<?php echo custom_field_chip('ucpt_friendly'); ?>" />
						<label for="ucpt_nar">Narrative</label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_nar'), 'ucpt_nar', UCPT_EDITOR_SETTINGS ); ?>
					<?php
						}
						else {
							echo "Once an admin has reviewed your strategy, you will be able to set these options under your group's manage tab.";
						}
					?>
					<br />
				</div>
				<?php
				}
				function settings_screen_save( $group_id = NULL ) {
					$plain_fields = array(
						'ucpt_friendly',
						'ucpt_nar'
					);
					foreach( $plain_fields as $field ) {
						$key = $field;
						if ( isset( $_POST[$key] ) ) {
							$value = wp_filter_post_kses($_POST[$key]);
							groups_update_groupmeta( $group_id, $field, $value );
						}
					}
					$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );

					$activity_update = "CHIP settings were updated by " . $editor_record . ".";
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
					}
				}  
				function display( $group_id = NULL ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;
					if (custom_field_chip('ucpt_friendly') == "") {
						?>
						<div class="division">
							<p>This group is not yet ready for inclusion in the CHIP. Please complete both the strategy card and raw data card for this group for consideration.</p>
						</div>
						<?php
					}
					if (custom_field_chip('ucpt_friendly') != "") {
						$group_cover_image_url = bp_attachments_get_attachment('url', array(
						  'object_dir' => 'groups',
						  'item_id' => bp_get_group_id(),
						));
						$ucpt_cover = $group_cover_image_url;
						$ucpt_group_name = bp_get_group_name();
						$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
						$ucpt_avatar = 	bp_get_group_avatar( 'type=full&width=15&height=15' );
						?>
						<div style="background: linear-gradient(rgba(10, 118, 211, 0.85), rgba(8, 94, 168, 0.85)), url('<?php echo $ucpt_cover; ?>'); background-size:100%; width=100%; min-height: 150px; padding: 30px;"><div style="font-size: 32px; color: #fff;">Health Improvement Strategy</div><br /><div style="font-size: 18px; color: #efefef;">"<?php echo $ucpt_group_name; ?>"</div><br/><div style="font-size: 10px; color: #efefef;">"<?php echo $ucpt_perma; ?>"</div></div>
						<div class="division">
						<p><h3>Goal:</h3> <?php echo ucpt_custom_field_meta("ucpt_goal"); ?></p>
						</div>
						<div class="division">
						<p><h3>Strategy Description:</h3> <?php echo ucpt_custom_field_meta("ucpt_desc"); ?></p>
						</div>
						<div class="division">
						<p><h3>Level of Change:</h3> <?php echo ucpt_custom_field_meta("ucpt_level"); ?></p>
						</div>
						<div class="division">
						<p><h3>Primary Focus Area:</h3> <?php echo ucpt_custom_field_meta("ucpt_focus"); ?></p>
						</div>
						<div class="division">
						<p><h3>Data Category Tag:</h3> <?php echo ucpt_custom_field_meta("ucpt_category"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Implementation Date:</h3> <?php echo ucpt_custom_field_meta("ucpt_date_start"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Completion Date:</h3> <?php echo ucpt_custom_field_meta("ucpt_date_end"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Ease of Implementation:</h3> <?php echo ucpt_custom_field_meta("ucpt_cis_ease"); ?></p>
						</div>
						<div class="division">
						<p><h3>Estimated Cost of Implementation:</h3> <?php echo ucpt_custom_field_meta("ucpt_cis_cost"); ?></p>
						</div>
						<div class="division">
						<p><h3>Potential Community Benefit:</h3> <?php echo ucpt_custom_field_meta("ucpt_cis_benefit"); ?></p>
						</div>
						<div class="division">
						<p><h3>Research:</h3> <?php echo ucpt_custom_field_meta("ucpt_research"); ?></p>
						</div>
						<script>
						$(document).ready( function () {
							$('#myDataTable').DataTable(
								{
									scrollX: true,
									order: [],
									scrollY: '500px',
									fixedColumns: true,
									scrollCollapse: true,
									paging: false,
									dom: 'Bfrtip',
									buttons: [
										'selectAll', 'selectNone', 'copy', 'csv', 'excel', {
											text: 'JSON',
											action: function ( e, dt, button, config ) {
												var data = dt.buttons.exportData();
							 
												$.fn.dataTable.fileSave(
													new Blob( [ JSON.stringify( data ) ] ),
													'Export.json'
												);
											}
										}
									],
									select: {
										style: 'multi'
									}
								}
							);
						} );
						</script>
						<div class="division">
							<?php
								$ucpt_time = UCPT_OPTIONS['ucpt_manage_start_date'];
								$ucpt_start_date = date('Y', strtotime($ucpt_time));
								$ucpt_current_date = date('Y');
								$ucpt_cycle = ($ucpt_current_date - $ucpt_start_date) + 1;	
							?>
						<p>
							<table id="myDataTable" border="1" bordercolor="#ededed" width="100%" class="table table-striped">
								<thead>
									<tr>
										<th style="background-color:#fff;">Measurements</th>
										<th>Target Goal</th>
										<th>Status</th>
										<th>Desired Trend</th>
										<th>Contributor</th>
										<?php
										$ucpt_time_count = 0;
										while ($ucpt_time_count < $ucpt_cycle) {
										?>
											<th>January <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>February <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>March <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>April <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>May <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>June <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>July <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>August <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>September <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>October <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>November <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
											<th>December <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<?php
										$ucpt_time_count++;
										}
										?>
								</tr>
								</thead>
								<tbody>
								<?php
								$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
								for ($i = 1; $i <= $max_measures; $i++) {
									if (ucpt_custom_field_meta('ucpt_measure_' . $i . '') != "") {
								?>
									<tr>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . ''); ?>
										</td>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_goal'); ?>
										</td>
										<td>
										<?php if (ucpt_custom_field_meta('ucpt_measure_' . $i . '_status') == "Archived") { ?>
											<span style="background-color: #d71616; color: #fff; padding: 3px; ">Archived</span>
											<?php } else { ?>
											<span style="background-color: #129f49; color: #fff; padding: 3px; ">Active</span>
										<?php } ?>
										</td>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_trend'); ?>
										</td>
										<td>
											<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_contributor'); ?>
										</td>
										<?php
										$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
										$ucpt_time_count = 0;
										while ($ucpt_time_count < $ucpt_cycle) {
											$y = $ucpt_time_count + 1;
												for ($m = 1; $m <= 12; $m++) {
											?>											
												<td>
													<?php echo ucpt_custom_field_meta('ucpt_m_' . $i . '_y' . $y . '_m' . $m . ''); ?>
												</td>
											<?php
												}
										$ucpt_time_count++;
										}
										?>
									</tr>
								<?php
									}
								}
								?>
								</tbody>
							</table>
						</p>
						</div>
						<div class="division">
							<h3>Data Narrative</h3>
							<p>
								<?php echo ucpt_custom_field_meta('ucpt_data_narrative'); ?>
							</p>
						</div>
						
						<div class="division">
							<h3>Scaled Data Visualization</h3>
							<?php
							$chart_id = substr(md5(rand()), 0, 6);
							?>
							<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
							<script>
							 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
								var data = {
							  "labels": [
								"<?php
									$ucpt_time_count_main = 0;
									while ($ucpt_time_count_main < $ucpt_cycle) {
									$current_year = $ucpt_start_date + $ucpt_time_count_main;
								?>1/<?php echo $current_year; ?>",
								"2/<?php echo $current_year; ?>",
								"3/<?php echo $current_year; ?>",
								"4/<?php echo $current_year; ?>",
								"5/<?php echo $current_year; ?>",
								"6/<?php echo $current_year; ?>",
								"7/<?php echo $current_year; ?>",
								"8/<?php echo $current_year; ?>",
								"9/<?php echo $current_year; ?>",
								"10/<?php echo $current_year; ?>",
								"11/<?php echo $current_year; ?>",
								"12/<?php echo $current_year; ?>",
								"<?php
									$ucpt_time_count_main++;
									}
								?>",
							  ],
							  "datasets": [
								<?php
									$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
									$measure_count = 1;
									while ($measure_count <= $max_measures) {
									if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
								?>
								{
								  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
								  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
								  "fill": false,
								  "data": [
									"<?php
									$ucpt_time_count_main_data = 0;
									while ($ucpt_time_count_main_data < $ucpt_cycle) {
									$current_year_data = $ucpt_time_count_main_data + 1;
									?><?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m1'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m2'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m3'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m4'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m5'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m6'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m7'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m8'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m9'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m10'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m11'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m12'); ?>",
									"<?php
									$ucpt_time_count_main_data++;
									}
									?>",
								  ],
								  "borderColor": "<?php echo "#" . $chart_color; ?>",
								  "spanGaps": false
								},
								{
								  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
								  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
								  "hidden": true,
								  "fill": false,
								  "data": [
									"<?php
									$ucpt_time_count_main = 0;
									while ($ucpt_time_count_main < $ucpt_cycle) {
									?><?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php
									$ucpt_time_count_main++;
									}
									?>",
								  ],
								  "borderColor": "<?php echo "#" . $chart_color; ?>",
								  "borderDash": [5,1],
								  "spanGaps": false
								},
								<?php
									}
								$measure_count++;
								}
								?>
							  ]
							};
								var options = {
							  "title": {
								"display": true,
								"text": "Group Measures",
								"position": "bottom",
								"fontStyle": "bold",
								"fullWidth": true
							  },
							  "legend": {
								"display": true,
								"position": "bottom",
								"fullWidth": true
							  },
							  "scales": {
								"yAxes": [
								  {
									"ticks": {
									  "beginAtZero": true
									},
									"gridLines": {
									  "display": true,
									  "lineWidth": 1,
									  "drawOnChartArea": true,
									  "color": "#000000",
									  "zeroLineColor": "#000000",
									  "zeroLineWidth": 1,
									  "drawTicks": true
									}
								  }
								],
								"xAxes": {
								  "0": {
									"gridLines": {
									  "drawOnChartArea": false,
									  "offsetGridLines": false,
									  "zeroLineColor": "#000000",
									  "display": true,
									  "lineWidth": 2,
									  "drawTicks": true,
									  "zeroLineWidth": 2,
									  "color": "#000000"
									},
									"ticks": {
									  "display": true,
									  "beginAtZero": true
									}
								  }
								}
							  },
							  "elements": {
								"line": {
								  "borderColor": "#000000",
								  "lineTension": 0
								}
							  }
							};

								var myChart = new Chart(ctx, {
									type: 'line',
									data: data,
									options: options
								});
								

							</script>
						</div>
						
						<?php 
						$ucpt_time_count = 0;
						while ($ucpt_time_count < $ucpt_cycle) {
						$y = $ucpt_time_count + 1;
						$y_prev = $y - 1;
						$current_year = $ucpt_start_date + $ucpt_time_count;
						?>					

						<div class="division">
							<h3>Fiscal Year <?php echo $current_year;?> Data Visualization (July-June)</h3>
							<?php
							$chart_id = substr(md5(rand()), 0, 6);
							?>
							<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
							<script>
							 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
								var data = {
							  "labels": [
								"7/<?php echo $current_year - 1; ?>",
								"8/<?php echo $current_year - 1; ?>",
								"9/<?php echo $current_year - 1; ?>",
								"10/<?php echo $current_year - 1; ?>",
								"11/<?php echo $current_year - 1; ?>",
								"12/<?php echo $current_year - 1; ?>",
								"1/<?php echo $current_year; ?>",
								"2/<?php echo $current_year; ?>",
								"3/<?php echo $current_year; ?>",
								"4/<?php echo $current_year; ?>",
								"5/<?php echo $current_year; ?>",
								"6/<?php echo $current_year; ?>",
							  ],
							  "datasets": [
								<?php
									$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
									$measure_count = 1;
									while ($measure_count <= $max_measures) {
									if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
								?>
								{
								  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
								  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
								  "fill": false,
								  "data": [
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m7'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m8'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m9'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m10'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m11'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m12'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m1'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m2'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m3'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m4'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m5'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m6'); ?>"
								  ],
								  "borderColor": "<?php echo "#" . $chart_color; ?>",
								  "spanGaps": false
								},
								{
								  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
								  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
								  "hidden": true,
								  "fill": false,
								  "data": [
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>"
								  ],
								  "borderColor": "<?php echo "#" . $chart_color; ?>",
								  "borderDash": [5,1],
								  "spanGaps": false
								},
								<?php
									}
								$measure_count++;
								}
								?>
							  ]
							};
								var options = {
							  "title": {
								"display": true,
								"text": "Group Measures",
								"position": "bottom",
								"fontStyle": "bold",
								"fullWidth": true
							  },
							  "legend": {
								"display": true,
								"position": "bottom",
								"fullWidth": true
							  },
							  "scales": {
								"yAxes": [
								  {
									"ticks": {
									  "beginAtZero": true
									},
									"gridLines": {
									  "display": true,
									  "lineWidth": 1,
									  "drawOnChartArea": true,
									  "color": "#000000",
									  "zeroLineColor": "#000000",
									  "zeroLineWidth": 1,
									  "drawTicks": true
									}
								  }
								],
								"xAxes": {
								  "0": {
									"gridLines": {
									  "drawOnChartArea": false,
									  "offsetGridLines": false,
									  "zeroLineColor": "#000000",
									  "display": true,
									  "lineWidth": 2,
									  "drawTicks": true,
									  "zeroLineWidth": 2,
									  "color": "#000000"
									},
									"ticks": {
									  "display": true,
									  "beginAtZero": true
									}
								  }
								}
							  },
							  "elements": {
								"line": {
								  "borderColor": "#000000",
								  "lineTension": 0
								}
							  }
							};

								var myChart = new Chart(ctx, {
									type: 'line',
									data: data,
									options: options
								});
								

							</script>
						</div>						
						
						<div class="division">
							<h3>Calendar Year <?php echo $current_year;?> Data Visualization</h3>
							<?php
							$chart_id = substr(md5(rand()), 0, 6);
							?>
							<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
							<script>
							 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
								var data = {
							  "labels": [
								"1/<?php echo $current_year; ?>",
								"2/<?php echo $current_year; ?>",
								"3/<?php echo $current_year; ?>",
								"4/<?php echo $current_year; ?>",
								"5/<?php echo $current_year; ?>",
								"6/<?php echo $current_year; ?>",
								"7/<?php echo $current_year; ?>",
								"8/<?php echo $current_year; ?>",
								"9/<?php echo $current_year; ?>",
								"10/<?php echo $current_year; ?>",
								"11/<?php echo $current_year; ?>",
								"12/<?php echo $current_year; ?>"
							  ],
							  "datasets": [
								<?php
									$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
									$measure_count = 1;
									while ($measure_count <= $max_measures) {
									if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
								?>
								{
								  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
								  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
								  "fill": false,
								  "data": [
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m1'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m2'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m3'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m4'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m5'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m6'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m7'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m8'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m9'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m10'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m11'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m12'); ?>"
								  ],
								  "borderColor": "<?php echo "#" . $chart_color; ?>",
								  "spanGaps": false
								},
								{
								  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
								  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
								  "hidden": true,
								  "fill": false,
								  "data": [
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
									"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>"
								  ],
								  "borderColor": "<?php echo "#" . $chart_color; ?>",
								  "borderDash": [5,1],
								  "spanGaps": false
								},
								<?php
									}
								$measure_count++;
								}
								?>
							  ]
							};
								var options = {
							  "title": {
								"display": true,
								"text": "Group Measures",
								"position": "bottom",
								"fontStyle": "bold",
								"fullWidth": true
							  },
							  "legend": {
								"display": true,
								"position": "bottom",
								"fullWidth": true
							  },
							  "scales": {
								"yAxes": [
								  {
									"ticks": {
									  "beginAtZero": true
									},
									"gridLines": {
									  "display": true,
									  "lineWidth": 1,
									  "drawOnChartArea": true,
									  "color": "#000000",
									  "zeroLineColor": "#000000",
									  "zeroLineWidth": 1,
									  "drawTicks": true
									}
								  }
								],
								"xAxes": {
								  "0": {
									"gridLines": {
									  "drawOnChartArea": false,
									  "offsetGridLines": false,
									  "zeroLineColor": "#000000",
									  "display": true,
									  "lineWidth": 2,
									  "drawTicks": true,
									  "zeroLineWidth": 2,
									  "color": "#000000"
									},
									"ticks": {
									  "display": true,
									  "beginAtZero": true
									}
								  }
								}
							  },
							  "elements": {
								"line": {
								  "borderColor": "#000000",
								  "lineTension": 0
								}
							  }
							};

								var myChart = new Chart(ctx, {
									type: 'line',
									data: data,
									options: options
								});
								

							</script>
						</div>
						
						<?php
						$ucpt_time_count++;
						}
						?>
						
						<div class="division">
							<h3>Last Modification</h3>
							<p>
								<?php echo ucpt_custom_field_meta('ucpt_data_edit_log'); ?>
							</p>
						</div>
						
						<div class="division">
							<h3>Contributing Community Participants</h3>
							<p>
								<?php 
									if ( bp_group_has_members( 'group_id='.bp_get_group_id().'&exclude_admins_mods=0&per_page=500' ) ) {
									while ( bp_group_members() ) {
										bp_group_the_member(); 
										echo bp_group_member_link() . ", ";
									}
									}
								?>
							</p>
						</div>
						
						<?php
						ucpt_credits();
					}
				}
			}

			bp_register_group_extension( 'UCPT_CHIP_Pages' );
			 
			endif;
	}
		
	add_action( 'bp_include', 'ucpt_chip_page' );			
			
	// Pull CHIP

	function pullCHIP() {
			$params['meta_query'] = array(
				array(
					'key'     => 'ucpt_friendly',
					'value'   => '',
					'compare' => '!='
				)
			);
		if ( function_exists('bp_is_active') && bp_is_active('groups') ) {
		
			if ( bp_has_groups($params) ) {
			
				while ( bp_groups() ) : 
				
					bp_the_group(); 
				
					$group_cover_image_url = bp_attachments_get_attachment('url', array(
					  'object_dir' => 'groups',
					  'item_id' => bp_get_group_id(),
					));
					$ucpt_cover = $group_cover_image_url;
					$ucpt_group_name = bp_get_group_name();
					$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
					$ucpt_avatar = 	bp_get_group_avatar( 'type=full&width=15&height=15' );
					echo "<div style='background-color: #ffffff; margin: 15px 30px 15px 30px; padding: 20px; '>";
					echo "<div style='background: linear-gradient(rgba(10, 118, 211, 0.85), rgba(8, 94, 168, 0.85)), url(" . $ucpt_cover . "); background-size:100%; width=100%; min-height: 150px; padding: 30px;'><div style='font-size: 32px; color: #fff;'>" . custom_field_chip('ucpt_friendly') . "</div><br /><div style='font-size: 18px; color: #efefef;'>" . $ucpt_avatar . " " . $ucpt_group_name . "</div><br/><div style='font-size: 10px; color: #efefef;'>" . $ucpt_perma ."</div></div><br />";
					echo "<p><b>Contributing Community Participants:</b><br />";
						if ( bp_group_has_members( 'group_id='.bp_get_group_id().'&exclude_admins_mods=0&per_page=500' ) ) {
							while ( bp_group_members() ) {
								bp_group_the_member(); 
								echo bp_group_member_link() . " / ";
							}
						}
					echo "</p>";
					echo '<p><form method="get" action="' .  $ucpt_perma . 'chip"><button type="submit">Access CHIP Data</button></form></p>';
					echo "<p>Generated by the <a href='https://equityengage.com'>Universal Community Planning Tool (UCPT)</a>.</p>";
					echo "</div>";
				endwhile;			
			} 
					
		}

	}

	add_shortcode('pullCHIP', 'pullCHIP');	
		
/*
Community Management
*/

	function custom_field_cm($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	function ucpt_cm( $group_id = null ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_CM extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = false;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'cm',
						'name' => 'Community Management',
						'nav_item_position' => 84
					);
					parent::init( $args );
				}
				function settings_screen( $group_id = null ) {
				?>
				<div class="division">
					<h3>Community Management Configuration</h3>
					<p>Use these features to categorize your group on MyGC.</p>
						<label for="ucpt_archived">Group Status</label>
							<select name="ucpt_archived">
								<option value="<?php echo custom_field_cm('ucpt_archived'); ?>"><?php echo custom_field_cm('ucpt_archived'); ?></option>
								<option value="Archived">Archived</option>
								<option value="Active">Active</option>
							</select>
				</div>
				<?php
				}
				function settings_screen_save( $group_id = NULL ) {
					$plain_fields = array(
						'ucpt_archived'
					);
					foreach( $plain_fields as $field ) {
						$key = $field;
						if ( isset( $_POST[$key] ) ) {
							$value = wp_filter_post_kses($_POST[$key]);
							groups_update_groupmeta( $group_id, $field, $value );
						}
					}
					$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );

					$activity_update = "Group status settings were updated by " . $editor_record . ".";
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
					}
				}  
				function display( $group_id = NULL ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;	
				}
			}

			bp_register_group_extension( 'UCPT_CM' );
			 
		endif;
	}
		
	add_action( 'bp_include', 'ucpt_cm' );

/*
Performance Management
*/

	function custom_field_pm($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	function add_page_to_group_pm( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_PM_Pages extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'pm',
						'name' => 'Performance',
						'nav_item_position' => 43
					);
					parent::init( $args );
				}
				function settings_screen( $group_id = null ) {
				?>
					<div class="division">
						<h3>Performance Management Group Configuration</h3>
						<p>Configure this group for inclusion in the performance management and Qi processes.</p>
							<label for="ucpt_pm">Performance Management Group</label>
								<select name="ucpt_pm">
									<option value="<?php echo ucpt_custom_field_meta('ucpt_pm'); ?>"><?php echo ucpt_custom_field_meta('ucpt_pm'); ?></option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
								</select>
							<label for="ucpt_qi">Quality Improvement Project Active</label>
								<select name="ucpt_qi">
									<option value="<?php echo ucpt_custom_field_meta('ucpt_qi'); ?>"><?php echo ucpt_custom_field_meta('ucpt_qi'); ?></option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
								</select>
							<label for="ucpt_qi_number">Number of Quality Improvement Projects</label>
								<input id="ucpt_qi_number" max="5" min="1" type="number" name="ucpt_qi_number" placeholder="Up to 5" value="<?php echo ucpt_custom_field_meta('ucpt_qi_number'); ?>" />
					</div>
					<?php
				}    
				function settings_screen_save( $group_id = NULL ) {
					$plain_fields = array(
								'ucpt_pm',
								'ucpt_qi',
								'ucpt_qi_number'
							);

					foreach( $plain_fields as $field ) {
						$key = $field;
						if ( isset( $_POST[$key] ) ) {
							$value = wp_filter_post_kses($_POST[$key]);
							groups_update_groupmeta( $group_id, $field, $value );
						}

					}
					
					$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );
					$activity_update = "Performance Management settings were updated by " . $editor_record . ".";
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
					}
				}
				function display( $group_id = null ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;
					?>
					<?php
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
					?>
					<p>
						<form method="get" action="<?php $ucpt_perma = bp_get_group_permalink( $bp->groups->current_group ); echo $ucpt_perma; ?>admin/pm"><button type="submit" align="right">Edit Performance Management Settings</button></form>
					</p>
					<?php
					}
					if (ucpt_custom_field_meta('ucpt_pm') == "No" or ucpt_custom_field_meta('ucpt_pm') =="") {
						echo "<div class='division'><p>This group does not yet qualify for PM. To turn on Performance Management, visit the Performance Management settings for your group under the Manage tab (only visible to signed-in group administrators).</p></div>";
					}
					if (ucpt_custom_field_meta('ucpt_pm') == "Yes") {		
					$group_cover_image_url = bp_attachments_get_attachment('url', array(
						  'object_dir' => 'groups',
						  'item_id' => bp_get_group_id(),
						));
					$ucpt_cover = $group_cover_image_url;
					$ucpt_group_name = bp_get_group_name();
					$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
					?>
					<p>
						<form method="get" action="<?php echo $ucpt_perma; ?>admin/raw-data"><button type="submit" align="right">Edit Data</button></form>
					</p>
					<?php
					}
					echo "<div style='background: linear-gradient(rgba(10, 118, 211, 0.85), rgba(8, 94, 168, 0.85)), url(" . $ucpt_cover . "); background-size:100%; width=100%; min-height: 150px; padding: 30px;'><div style='font-size: 32px; color: #fff;'>Performance Management</div><br /><div style='font-size: 18px; color: #efefef;'>" . $ucpt_group_name . "</div><br/><div style='font-size: 10px; color: #efefef;'>" . $ucpt_perma ."</div></div>";
					?>
					<script>
					$(document).ready( function () {
						$('#myDataTable').DataTable(
							{
								scrollX: true,
								order: [],
								scrollY: '500px',
								fixedColumns: true,
								scrollCollapse: true,
								paging: false,
								dom: 'Bfrtip',
								buttons: [
									'selectAll', 'selectNone', 'copy', 'csv', 'excel', {
										text: 'JSON',
										action: function ( e, dt, button, config ) {
											var data = dt.buttons.exportData();
						 
											$.fn.dataTable.fileSave(
												new Blob( [ JSON.stringify( data ) ] ),
												'Export.json'
											);
										}
									}
								],
								select: {
									style: 'multi'
								}
							}
						);
					} );
					</script>
					<div class="division">
						<?php
							$ucpt_time = UCPT_OPTIONS['ucpt_manage_start_date'];
							$ucpt_start_date = date('Y', strtotime($ucpt_time));
							$ucpt_current_date = date('Y');
							$ucpt_cycle = ($ucpt_current_date - $ucpt_start_date) + 1;	
						?>
					<p>
						<table id="myDataTable" border="1" bordercolor="#ededed" width="100%" class="table table-striped">
							<thead>
								<tr>
									<th style="background-color:#fff;">Measurements</th>
									<th>Target Goal</th>
									<th>Status</th>
									<th>Desired Trend</th>
									<th>Contributor</th>
									<?php
									$ucpt_time_count = 0;
									while ($ucpt_time_count < $ucpt_cycle) {
									?>
										<th>January <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>February <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>March <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>April <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>May <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>June <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>July <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>August <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>September <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>October <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>November <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
										<th>December <?php echo $ucpt_start_date + $ucpt_time_count; ?></th>
									<?php
									$ucpt_time_count++;
									}
									?>
							</tr>
							</thead>
							<tbody>
							<?php
							$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
							for ($i = 1; $i <= $max_measures; $i++) {
								if (ucpt_custom_field_meta('ucpt_measure_' . $i . '') != "") {
							?>
								<tr>
									<td>
										<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . ''); ?>
									</td>
									<td>
										<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_goal'); ?>
									</td>
									<td>
									<?php if (ucpt_custom_field_meta('ucpt_measure_' . $i . '_status') == "Archived") { ?>
										<span style="background-color: #d71616; color: #fff; padding: 3px; ">Archived</span>
										<?php } else { ?>
										<span style="background-color: #129f49; color: #fff; padding: 3px; ">Active</span>
									<?php } ?>
									</td>
									<td>
										<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_trend'); ?>
									</td>
									<td>
										<?php echo ucpt_custom_field_meta('ucpt_measure_' . $i . '_contributor'); ?>
									</td>
									<?php
									$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
									$ucpt_time_count = 0;
									while ($ucpt_time_count < $ucpt_cycle) {
										$y = $ucpt_time_count + 1;
											for ($m = 1; $m <= 12; $m++) {
										?>											
											<td>
												<?php echo ucpt_custom_field_meta('ucpt_m_' . $i . '_y' . $y . '_m' . $m . ''); ?>
											</td>
										<?php
											}
									$ucpt_time_count++;
									}
									?>
								</tr>
							<?php
								}
							}
							?>
							</tbody>
						</table>
					</p>
					</div>
					<div class="division">
						<h3>Data Narrative</h3>
						<p>
							<?php echo ucpt_custom_field_meta('ucpt_data_narrative'); ?>
						</p>
					</div>
					
					<div class="division">
						<h3>Scaled Data Visualization</h3>
						<?php
						$chart_id = substr(md5(rand()), 0, 6);
						?>
						<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
						<script>
						 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
							var data = {
						  "labels": [
							"<?php
								$ucpt_time_count_main = 0;
								while ($ucpt_time_count_main < $ucpt_cycle) {
								$current_year = $ucpt_start_date + $ucpt_time_count_main;
							?>1/<?php echo $current_year; ?>",
							"2/<?php echo $current_year; ?>",
							"3/<?php echo $current_year; ?>",
							"4/<?php echo $current_year; ?>",
							"5/<?php echo $current_year; ?>",
							"6/<?php echo $current_year; ?>",
							"7/<?php echo $current_year; ?>",
							"8/<?php echo $current_year; ?>",
							"9/<?php echo $current_year; ?>",
							"10/<?php echo $current_year; ?>",
							"11/<?php echo $current_year; ?>",
							"12/<?php echo $current_year; ?>",
							"<?php
								$ucpt_time_count_main++;
								}
							?>",
						  ],
						  "datasets": [
							<?php
								$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
								$measure_count = 1;
								while ($measure_count <= $max_measures) {
								if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
							?>
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
							  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
							  "fill": false,
							  "data": [
								"<?php
								$ucpt_time_count_main_data = 0;
								while ($ucpt_time_count_main_data < $ucpt_cycle) {
								$current_year_data = $ucpt_time_count_main_data + 1;
								?><?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m1'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m2'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m3'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m4'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m5'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m6'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m7'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m8'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m9'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m10'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m11'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $current_year_data . '_m12'); ?>",
								"<?php
								$ucpt_time_count_main_data++;
								}
								?>",
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "spanGaps": false
							},
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
							  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
							  "hidden": true,
							  "fill": false,
							  "data": [
								"<?php
								$ucpt_time_count_main = 0;
								while ($ucpt_time_count_main < $ucpt_cycle) {
								?><?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php
								$ucpt_time_count_main++;
								}
								?>",
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "borderDash": [5,1],
							  "spanGaps": false
							},
							<?php
								}
							$measure_count++;
							}
							?>
						  ]
						};
							var options = {
						  "title": {
							"display": true,
							"text": "Group Measures",
							"position": "bottom",
							"fontStyle": "bold",
							"fullWidth": true
						  },
						  "legend": {
							"display": true,
							"position": "bottom",
							"fullWidth": true
						  },
						  "scales": {
							"yAxes": [
							  {
								"ticks": {
								  "beginAtZero": true
								},
								"gridLines": {
								  "display": true,
								  "lineWidth": 1,
								  "drawOnChartArea": true,
								  "color": "#000000",
								  "zeroLineColor": "#000000",
								  "zeroLineWidth": 1,
								  "drawTicks": true
								}
							  }
							],
							"xAxes": {
							  "0": {
								"gridLines": {
								  "drawOnChartArea": false,
								  "offsetGridLines": false,
								  "zeroLineColor": "#000000",
								  "display": true,
								  "lineWidth": 2,
								  "drawTicks": true,
								  "zeroLineWidth": 2,
								  "color": "#000000"
								},
								"ticks": {
								  "display": true,
								  "beginAtZero": true
								}
							  }
							}
						  },
						  "elements": {
							"line": {
							  "borderColor": "#000000",
							  "lineTension": 0
							}
						  }
						};

							var myChart = new Chart(ctx, {
								type: 'line',
								data: data,
								options: options
							});
							

						</script>
					</div>
					
					<?php 
					$ucpt_time_count = 0;
					while ($ucpt_time_count < $ucpt_cycle) {
					$y = $ucpt_time_count + 1;
					$y_prev = $y - 1;
					$current_year = $ucpt_start_date + $ucpt_time_count;
					?>					

					<div class="division">
						<h3>Fiscal Year <?php echo $current_year;?> Data Visualization (July-June)</h3>
						<?php
						$chart_id = substr(md5(rand()), 0, 6);
						?>
						<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
						<script>
						 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
							var data = {
						  "labels": [
							"7/<?php echo $current_year - 1; ?>",
							"8/<?php echo $current_year - 1; ?>",
							"9/<?php echo $current_year - 1; ?>",
							"10/<?php echo $current_year - 1; ?>",
							"11/<?php echo $current_year - 1; ?>",
							"12/<?php echo $current_year - 1; ?>",
							"1/<?php echo $current_year; ?>",
							"2/<?php echo $current_year; ?>",
							"3/<?php echo $current_year; ?>",
							"4/<?php echo $current_year; ?>",
							"5/<?php echo $current_year; ?>",
							"6/<?php echo $current_year; ?>",
						  ],
						  "datasets": [
							<?php
								$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
								$measure_count = 1;
								while ($measure_count <= $max_measures) {
								if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
							?>
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
							  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
							  "fill": false,
							  "data": [
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m7'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m8'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m9'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m10'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m11'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y_prev . '_m12'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m1'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m2'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m3'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m4'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m5'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m6'); ?>"
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "spanGaps": false
							},
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
							  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
							  "hidden": true,
							  "fill": false,
							  "data": [
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>"
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "borderDash": [5,1],
							  "spanGaps": false
							},
							<?php
								}
							$measure_count++;
							}
							?>
						  ]
						};
							var options = {
						  "title": {
							"display": true,
							"text": "Group Measures",
							"position": "bottom",
							"fontStyle": "bold",
							"fullWidth": true
						  },
						  "legend": {
							"display": true,
							"position": "bottom",
							"fullWidth": true
						  },
						  "scales": {
							"yAxes": [
							  {
								"ticks": {
								  "beginAtZero": true
								},
								"gridLines": {
								  "display": true,
								  "lineWidth": 1,
								  "drawOnChartArea": true,
								  "color": "#000000",
								  "zeroLineColor": "#000000",
								  "zeroLineWidth": 1,
								  "drawTicks": true
								}
							  }
							],
							"xAxes": {
							  "0": {
								"gridLines": {
								  "drawOnChartArea": false,
								  "offsetGridLines": false,
								  "zeroLineColor": "#000000",
								  "display": true,
								  "lineWidth": 2,
								  "drawTicks": true,
								  "zeroLineWidth": 2,
								  "color": "#000000"
								},
								"ticks": {
								  "display": true,
								  "beginAtZero": true
								}
							  }
							}
						  },
						  "elements": {
							"line": {
							  "borderColor": "#000000",
							  "lineTension": 0
							}
						  }
						};

							var myChart = new Chart(ctx, {
								type: 'line',
								data: data,
								options: options
							});
							

						</script>
					</div>						
					
					<div class="division">
						<h3>Calendar Year <?php echo $current_year;?> Data Visualization</h3>
						<?php
						$chart_id = substr(md5(rand()), 0, 6);
						?>
						<canvas id="<?php echo $chart_id; ?>" height="200" width="200"></canvas>
						<script>
						 var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
							var data = {
						  "labels": [
							"1/<?php echo $current_year; ?>",
							"2/<?php echo $current_year; ?>",
							"3/<?php echo $current_year; ?>",
							"4/<?php echo $current_year; ?>",
							"5/<?php echo $current_year; ?>",
							"6/<?php echo $current_year; ?>",
							"7/<?php echo $current_year; ?>",
							"8/<?php echo $current_year; ?>",
							"9/<?php echo $current_year; ?>",
							"10/<?php echo $current_year; ?>",
							"11/<?php echo $current_year; ?>",
							"12/<?php echo $current_year; ?>"
						  ],
						  "datasets": [
							<?php
								$max_measures = UCPT_OPTIONS['ucpt_manage_measure_number'];
								$measure_count = 1;
								while ($measure_count <= $max_measures) {
								if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '') != "") {
							?>
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?>",
							  "backgroundColor": "<?php $chart_color = substr(md5(rand()), 0, 6); echo "#" . $chart_color; ?>",
							  "fill": false,
							  "data": [
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m1'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m2'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m3'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m4'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m5'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m6'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m7'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m8'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m9'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m10'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m11'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_m_' . $measure_count . '_y' . $y . '_m12'); ?>"
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "spanGaps": false
							},
							{
							  "label": "<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count ); ?> Target Goal",
							  "backgroundColor": "<?php echo "#" . $chart_color; ?>",
							  "hidden": true,
							  "fill": false,
							  "data": [
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>",
								"<?php echo ucpt_custom_field_meta('ucpt_measure_' . $measure_count . '_goal'); ?>"
							  ],
							  "borderColor": "<?php echo "#" . $chart_color; ?>",
							  "borderDash": [5,1],
							  "spanGaps": false
							},
							<?php
								}
							$measure_count++;
							}
							?>
						  ]
						};
							var options = {
						  "title": {
							"display": true,
							"text": "Group Measures",
							"position": "bottom",
							"fontStyle": "bold",
							"fullWidth": true
						  },
						  "legend": {
							"display": true,
							"position": "bottom",
							"fullWidth": true
						  },
						  "scales": {
							"yAxes": [
							  {
								"ticks": {
								  "beginAtZero": true
								},
								"gridLines": {
								  "display": true,
								  "lineWidth": 1,
								  "drawOnChartArea": true,
								  "color": "#000000",
								  "zeroLineColor": "#000000",
								  "zeroLineWidth": 1,
								  "drawTicks": true
								}
							  }
							],
							"xAxes": {
							  "0": {
								"gridLines": {
								  "drawOnChartArea": false,
								  "offsetGridLines": false,
								  "zeroLineColor": "#000000",
								  "display": true,
								  "lineWidth": 2,
								  "drawTicks": true,
								  "zeroLineWidth": 2,
								  "color": "#000000"
								},
								"ticks": {
								  "display": true,
								  "beginAtZero": true
								}
							  }
							}
						  },
						  "elements": {
							"line": {
							  "borderColor": "#000000",
							  "lineTension": 0
							}
						  }
						};

							var myChart = new Chart(ctx, {
								type: 'line',
								data: data,
								options: options
							});
							

						</script>
					</div>
					
					<?php
					$ucpt_time_count++;
					}
					?>
					
					<div class="division">
						<h3>Last Modification</h3>
						<p>
							<?php echo ucpt_custom_field_meta('ucpt_data_edit_log'); ?>
						</p>
					</div>
					
					<?php
					ucpt_credits();						
					}
				}
				
			}

			bp_register_group_extension( 'UCPT_PM_Pages' );
			 
			endif;
	}
		
	add_action( 'bp_include', 'add_page_to_group_pm' );
	add_filter( 'bp_after_has_groups_parse_args', 'ucpt_alter_groups_parse_args' );
	
	function ucpt_alter_groups_parse_args( $loop ) {
	$loop['per_page'] = 250;
		return $loop;
	}

	// PMRT Group Query

	function pullPMRT( $group_id = NULL ) {
	
		$pmrt_key = sanitize_text_field($_POST['ucpt_category']);
		?>
		<div style="background-color:#f1f1f1; padding: 20px;">
		<p>
			<form method="post" action="">
				<h2>PMRT Advanced Data Query</h2>
					<select name="ucpt_category" style="max-width:90%;">
						<option value="<?php echo $pmrt_key; ?>"><?php echo $pmrt_key; ?></option>
						<?php
						$ucpt_pm_options = get_option( 'ucpt_manage_settings' );
						$category = 1;
						$max_categories = 15;
						while ($category <= $max_categories) {
							if ($ucpt_pm_options['ucpt_manage_custom_categories_' . $category] != "") {
						?>
						<option value="<?php echo $ucpt_pm_options['ucpt_manage_custom_categories_' . $category]; ?>"><?php echo $ucpt_pm_options['ucpt_manage_custom_categories_' . $category]; ?></option>
						<?php
							}
						$category++;
						}
						?>
					</select>
				<br /><br />
				<button type="submit">Fetch Detailed PMRT View</button> <button type="submit" value="" name="ucpt_category">Clear Filters</button>
			</form>
		</p>
		</div>
		<br />
		
		<?php
		if ( $pmrt_key != "" ) {

			$pmrt_params['meta_query'] = array(
				array(
				 'type' => 'alphabetical',
				 'key'     => 'ucpt_category',
				 'value'   => $pmrt_key,
				 'compare' => '==',
				 'per_page' => 250
				)
			);
			if ( function_exists('bp_is_active') && bp_is_active('groups') ) {
			
				if ( bp_has_groups($pmrt_params) ) {
				
					while ( bp_groups() ) : 
					
						bp_the_group(); 
						$ucpt_measures_options_pmrt = get_option( 'ucpt_manage_settings' );
						$max_measures_pmrt = $ucpt_measures_options_pmrt['ucpt_manage_measure_number'];
						$measure_count_pmrt = 1;
						$group_id = bp_get_group_id();
						$ucpt_group_name = bp_get_group_name();
						$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
						$ucpt_group_type = bp_get_group_type();
						$ucpt_group_desc = bp_get_group_description_excerpt();
						$ucpt_group_av = bp_get_group_avatar( 'type=thumb&width=10&height=10' );
						$ucpt_group_active = bp_get_group_last_active();
						$ucpt_group_created = bp_get_group_date_created();
						$ucpt_group_member_count = bp_get_group_member_count();
						$ucpt_group_admins = groups_get_group_admins( $group_id );
						$ucpt_group_admins_new = bp_group_admin_ids();
						?>
						<table>
						<?php
						echo "<tr><th>" . $ucpt_group_av . " " . $ucpt_group_name . " | " . $ucpt_group_type . " | " . $ucpt_group_member_count . "</th></tr>";
						echo "<tr><td>Last Updated " . $ucpt_group_active . " | Created " . $ucpt_group_created . "</td></tr>";
						echo "<tr><td>Group Admins: ";
							if (bp_has_members( '&include=' . bp_group_admin_ids() )) {
								while (bp_members()) { 
									bp_the_member();
									echo "<a href='" . bp_get_member_permalink() . "'>" . bp_get_member_name() . "</a> ";
								}
							}
						echo "</td></tr>";
						echo "<tr><td>" . $ucpt_group_desc . "</td></tr>";
						while ($measure_count_pmrt <= $max_measures_pmrt) {
							if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count_pmrt) != "") {
								echo "<tr><td><a href='" . $ucpt_perma . "raw-data'>" . $ucpt_group_name . " " . ucpt_custom_field_meta('ucpt_measure_' . $measure_count_pmrt) . "</a></td></tr>";
								$total_fetched++;
							}
							$measure_count_pmrt++;
						}
						endwhile;
						?>
						</table>
						<?php
					echo $total_fetched . ' Total hyper local measures matching this query.';
				} 
						
			}
		}
	}

	add_shortcode('pullPMRT', 'pullPMRT');
		
/*
Quality Improvement
*/

	function custom_field_qi($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	function add_page_to_group_qi( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_QI_Pages extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'qi',
						'name' => 'Quality',
						'nav_item_position' => 42
					);
					parent::init( $args );
				}
				function settings_screen( $group_id = null ) {
				?>
					<?php 
						$qi_reps = ucpt_custom_field_meta('ucpt_qi_number'); 
						$qi_count = 1;
						if ($qi_reps == "") {
							$qi_reps = 1;
						}
					?>
						<input type="hidden" id="qi_reps" name="qi_reps" value="<?php echo $qi_reps; ?>">
					<?php
						while ($qi_count <= $qi_reps) {
					?>
						<div class="division">
							<h3>Quality Improvement Project #<?php echo $qi_count; ?></h3>
								<label for="ucpt_qi_problem_<?php echo $qi_count; ?>"><h3>Problem Statement</h3></label>
									<?php wp_editor( ucpt_custom_field_meta('ucpt_qi_problem_' . $qi_count), 'ucpt_qi_problem_' . $qi_count, UCPT_EDITOR_SETTINGS ); ?>		
								
								<label for="ucpt_qi_desired_<?php echo $qi_count; ?>"><h3>Desired Result</h3></label>
									<?php wp_editor( ucpt_custom_field_meta('ucpt_qi_desired_' . $qi_count), 'ucpt_qi_desired_' . $qi_count, UCPT_EDITOR_SETTINGS ); ?>
								
								<label for="ucpt_qi_related_<?php echo $qi_count; ?>"><h3>Related Measures</h3></label>
									<?php wp_editor( ucpt_custom_field_meta('ucpt_qi_related_' . $qi_count), 'ucpt_qi_related_' . $qi_count, UCPT_EDITOR_SETTINGS ); ?>
									
								<label for="ucpt_qi_start_<?php echo $qi_count; ?>"><h3>Estimated Implementation Date</h3></label>
									<input id="ucpt_qi_start_<?php echo $qi_count; ?>" type="date" name="ucpt_qi_start_<?php echo $qi_count; ?>" value="<?php echo ucpt_custom_field_meta('ucpt_qi_start' . $qi_count); ?>" />

								<label for="ucpt_qi_end_<?php echo $qi_count; ?>"><h3>Estimated Completion Date</h3></label>
									<input id="ucpt_qi_end_<?php echo $qi_count; ?>" type="date" name="ucpt_qi_end_<?php echo $qi_count; ?>" value="<?php echo ucpt_custom_field_meta('ucpt_qi_end' . $qi_count); ?>" />	
									
								<label for="ucpt_qi_results_<?php echo $qi_count; ?>"><h3>Results</h3></label>
									<?php wp_editor( ucpt_custom_field_meta('ucpt_qi_results_' . $qi_count), 'ucpt_qi_results_' . $qi_count, UCPT_EDITOR_SETTINGS ); ?> 		
						</div>
					<?php
						$qi_count++;
						}
				}    
				function settings_screen_save( $group_id = NULL ) {
					$qi_reps = sanitize_text_field($_POST["qi_reps"]);  
					$qi_count = 1;
					while ($qi_count <= $qi_reps) {
						$plain_fields = array(
							'ucpt_qi_problem_' . $qi_count,
							'ucpt_qi_desired_' . $qi_count,
							'ucpt_qi_related_' . $qi_count,
							'ucpt_qi_start_' . $qi_count,
							'ucpt_qi_end_' . $qi_count,
							'ucpt_qi_results_' . $qi_count
						);
						foreach( $plain_fields as $field ) {
							$key = $field;
							if ( isset( $_POST[$key] ) ) {
								$value = wp_filter_post_kses($_POST[$key]);
								groups_update_groupmeta( $group_id, $field, $value );
							}
						}
					$qi_count++;	
					}
					
					$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );
					$activity_update = "Quality Improvement settings were updated by " . $editor_record . ".";
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
					}
				}
				function display( $group_id = null ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;
					?>
					<?php
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
					?>
					<p>
						<form method="get" action="<?php $ucpt_perma = bp_get_group_permalink( $bp->groups->current_group ); echo $ucpt_perma; ?>admin/qi"><button type="submit" align="right">Edit Quality Improvement Settings</button></form>
					</p>
					<?php
					}
					if (ucpt_custom_field_meta('ucpt_qi') == "No" or ucpt_custom_field_meta('ucpt_qi') =="") {
						echo "<div class='division'><p>This group does not yet qualify for Qi. To turn on Quality Improvement, visit the Performance Management settings for your group under the Manage tab (only visible to signed-in group administrators).</p></div>";
					}
					if (ucpt_custom_field_meta('ucpt_qi') == "Yes") {	
						$qi_reps = ucpt_custom_field_meta('ucpt_qi_number'); 
						$qi_count = 1;
						if ($qi_reps == "") {
							$qi_reps = 1;
						}					
						while ($qi_count <= $qi_reps) {
					?>		
						<div class="division">
							<h3>Quality Improvement Project #<?php echo $qi_count; ?></h3>
								<h3>Problem Statement</h3>
									<p><?php echo ucpt_custom_field_meta('ucpt_qi_problem_' . $qi_count); ?></p>
								
								<h3>Desired Result</h3>
									<p><?php echo ucpt_custom_field_meta('ucpt_qi_desired_' . $qi_count); ?></p>
									
								<h3>Related Measures</h3>
									<p><?php echo ucpt_custom_field_meta('ucpt_qi_related_' . $qi_count); ?></p>
									
								<h3>Timeline</h3>
									<p>From <?php echo ucpt_custom_field_meta('ucpt_qi_start_' . $qi_count); ?> to <?php echo ucpt_custom_field_meta('ucpt_qi_end_' . $qi_count); ?></p>

								<h3>Results</h3>
									<p><?php echo ucpt_custom_field_meta('ucpt_qi_results_' . $qi_count); ?></p>
						</div>
					
					<?php
						$qi_count++;
						}
					}
				}
				
			}

			bp_register_group_extension( 'UCPT_QI_Pages' );
			 
		endif;
	}
	add_action( 'bp_include', 'add_page_to_group_qi' );
	
/*
Hyper Local Data Shortcode
*/

	function pullHLD( $group_id = NULL ) {

			$hld_params['meta_query'] = array(
				array(
				 'key'     => 'ucpt_measure_1',
				 'value'   => '',
				 'compare' => '!=',
				 'per_page' => 1000
				)
			);
			if ( function_exists('bp_is_active') && bp_is_active('groups') ) {
			
				if ( bp_has_groups($hld_params) ) {
				
					while ( bp_groups() ) : 
					
						bp_the_group(); 
						$ucpt_measures_options_hld = get_option( 'ucpt_manage_settings' );
						$max_measures_hld = $ucpt_measures_options_hld['ucpt_manage_measure_number'];
						$measure_count_hld = 1;
						$ucpt_group_name = bp_get_group_name();
						$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
						while ($measure_count_hld <= $max_measures_hld) {
							if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count_hld) != "") {
								echo "<a href='" . $ucpt_perma . "raw-data'>" . $ucpt_group_name . " " . ucpt_custom_field_meta('ucpt_measure_' . $measure_count_hld) . "</a>";
								echo "<br />";
								$total_fetched++;
							}
							$measure_count_hld++;
						}
					endwhile;	
					echo $total_fetched . 'Total hyper local measures matching this query.';
				} 
						
			}
		}

	add_shortcode('pullHLD', 'pullHLD');	
	
/*
Hyper Local Data Shortcode (Public)
*/

	function pullHLDP( $group_id = NULL ) {

			$hld_params['meta_query'] = array(
				array(
				 'key'     => 'ucpt_measure_1',
				 'value'   => '',
				 'compare' => '!=',
				 'per_page' => 1000
				)
			);
			if ( function_exists('bp_is_active') && bp_is_active('groups') ) {
			
				if ( bp_has_groups($hld_params) ) {
				
					while ( bp_groups() ) : 
							bp_the_group(); 
							$ucpt_measures_options_hld = get_option( 'ucpt_manage_settings' );
							$max_measures_hld = $ucpt_measures_options_hld['ucpt_manage_measure_number'];
							$measure_count_hld = 1;
							$ucpt_group_name = bp_get_group_name();
							$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
							while ($measure_count_hld <= $max_measures_hld) {
								if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count_hld) != "" && bp_get_group_type()== "Public Group") {
									echo "<a href='" . $ucpt_perma . "raw-data'>" . $ucpt_group_name . " " . ucpt_custom_field_meta('ucpt_measure_' . $measure_count_hld) . "</a>";
									echo "<br />";
									$total_fetched++;
								}
								$measure_count_hld++;
							}
					endwhile;
					echo $total_fetched . ' Total hyper local measures matching this query.';
				} 
						
			}
		}

	add_shortcode('pullHLDP', 'pullHLDP');	

/*
Embed
*/

	function custom_field_embed($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	function ucpt_embed( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_Embed extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'linked',
						'name' => 'Linked',
						'nav_item_position' => 45
					);
					parent::init( $args );
				}
				function settings_screen( $group_id = null ) {
				?>
				<div style="background-color:#10b98f; color: #fff; padding: 20px; margin-top: 10px; margin-bottom: 20px;">
					<b>Linked</b>
					<p>This is an advanced feature that allows groups to embed external resources such as dashboards and websites if they have been specified as allowed by site administrators.</p>
						<?php
						$ucpt_embed_options = get_option( 'ucpt_manage_settings' );
						?>
						<label for="ucpt_embed_host">Hosting Site:</label>
							<select name="ucpt_embed_host">
								<option value="<?php echo custom_field_embed('ucpt_embed_host'); ?>"><?php echo custom_field_embed('ucpt_embed_host'); ?></option>
								<?php
								if ($ucpt_embed_options['ucpt_manage_custom_embed_1'] != "") {
								?>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_1']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_1']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_2']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_2']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_3']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_3']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_4']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_4']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_5']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_5']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_6']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_6']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_7']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_7']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_8']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_8']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_9']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_9']; ?></option>
								<option value="<?php echo $ucpt_embed_options['ucpt_manage_custom_embed_10']; ?>"><?php echo $ucpt_embed_options['ucpt_manage_custom_embed_10']; ?></option>
								<?php
								}
								else {
								?>
								<option value="https://mygarrettcounty.com/">https://mygarrettcounty.com/</option><?php
								}
								?>
							</select>
						<label for="ucpt_embed_linked">URL Extension (i.e.; if the URL is google.com/hello/bar, enter "hello/bar")</label>
							<input id="ucpt_embed_linked" type="text" name="ucpt_embed_linked" value="<?php echo custom_field_embed('ucpt_embed_linked'); ?>" />
				</div>
				<?php
				}
				function settings_screen_save( $group_id = NULL ) {
					$plain_fields = array(
						'ucpt_embed_host',
						'ucpt_embed_linked'
					);
					foreach( $plain_fields as $field ) {
						$key = $field;
						if ( isset( $_POST[$key] ) ) {
							$value = wp_filter_post_kses($_POST[$key]);
							groups_update_groupmeta( $group_id, $field, $value );
						}
					}
				}  
				function display( $group_id = NULL ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;	
					if (custom_field_embed('ucpt_embed_linked') != "") {
					?>
					<iframe src="<?php echo custom_field_embed('ucpt_embed_host'); ?><?php echo custom_field_embed('ucpt_embed_linked'); ?>" alt="Embedded Content" width="95%" height="600px"></iframe>
					<?php
					}
					else {
						echo "This group is not currently linked to any external resources.";
					}
				}
			}

			bp_register_group_extension( 'UCPT_Embed' );
			 
		endif;
	}
		
	add_action( 'bp_include', 'ucpt_embed' );

/*
Location
*/

	function custom_field_location($meta_key='') {
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	function ucpt_location( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_Location extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'location',
						'name' => 'Location',
						'nav_item_position' => 45
					);
					parent::init( $args );
				}
				function settings_screen( $group_id = null ) {
				?>
				<div class="division">
					<h3>Location</h3>
					<p>Select the location that best applies to this group.</p>
						<label for="ucpt_location_host">Location:</label>
							<select name="ucpt_location">
								<option value="<?php echo custom_field_location('ucpt_location'); ?>"><?php echo custom_field_location('ucpt_location'); ?></option>
								<?php
								if (UCPT_OPTIONS['ucpt_manage_custom_location_1'] != "") {
								?>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_1']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_1']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_2']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_2']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_3']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_3']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_4']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_4']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_5']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_5']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_6']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_6']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_7']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_7']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_8']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_8']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_9']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_9']; ?></option>
								<option value="<?php echo UCPT_OPTIONS['ucpt_manage_custom_location_10']; ?>"><?php echo UCPT_OPTIONS['ucpt_manage_custom_location_10']; ?></option>
								<?php
								}
								?>
							</select>
				</div>
				<?php
				}
				function settings_screen_save( $group_id = NULL ) {
					$plain_fields = array(
						'ucpt_location'
					);
					foreach( $plain_fields as $field ) {
						$key = $field;
						if ( isset( $_POST[$key] ) ) {
							$value = wp_filter_post_kses($_POST[$key]);
							groups_update_groupmeta( $group_id, $field, $value );
						}
					}
				}  
				function display( $group_id = NULL ) {
					/* Use this function to display the actual content of your group extension when the nav item is selected */
					global $bp;	
					?>
					<?php
					$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
					?>
					<p>
						<form method="get" action="<?php echo $ucpt_perma; ?>admin/location"><button type="submit" align="right">Edit Location</button></form>
					</p>
					<?php
					}
					if (custom_field_location('ucpt_location') != "") {
					?>
					<div class="division">
						<h3>Location</h3>
						<p><?php echo custom_field_location('ucpt_location'); ?></p>
					</div>
					<?php
					}
					else {
						echo "<div class='division'><p>This group is not currently linked to any locations.</p></div>";
					}
				}
			}

			bp_register_group_extension( 'UCPT_Location' );
			 
		endif;
	}
		
	add_action( 'bp_include', 'ucpt_location' );
	
/*
DS - Data Scanner
*/

	function pullDSfunction( $group_id = NULL ) {
		
		$DS_params['meta_query'] = array(
			array(
			 'key'     => 'ucpt_measure_1',
			 'value'   => '',
			 'compare' => '!=',
			 'per_page' => 1000
			)
		);
		
		$DS_key = sanitize_text_field($_POST['ucpt_m']);
		
		?>
		<div style="background-color:#f1f1f1; padding: 20px;">
		<p>
			<form method="post" action="">
				<h2>Data Scanner - Browse Hyper Local Measures</h2>
					<select name="ucpt_m" style="max-width:90%;">
						<option value="#">Select a hyper local measure to open dataset...</option>
						<?php
							if ( function_exists('bp_is_active') && bp_is_active('groups') ) {
								if ( bp_has_groups($DS_params) ) {
									while ( bp_groups() ) : 
											bp_the_group(); 
											$ucpt_measures_options_ds = get_option( 'ucpt_manage_settings' );
											$max_measures_ds = $ucpt_measures_options_ds['ucpt_manage_measure_number'];
											$measure_count_ds = 1;
											$ucpt_group_name = bp_get_group_name();
											$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
											while ($measure_count_ds <= $max_measures_ds) {
												if (ucpt_custom_field_meta('ucpt_measure_' . $measure_count_ds) != "" && bp_get_group_type()== "Public Group") {
													?>
													<option value="<?php echo $ucpt_perma . '/raw-data'; ?>"><?php echo $ucpt_group_name . " " . ucpt_custom_field_meta('ucpt_measure_' . $measure_count_ds); ?></option>
													<?php
													$total_fetched++;
												}
												$measure_count_ds++;
											}
									endwhile;
								}		
							}
						?>
					</select>
					 <input type="hidden" id="ucpt_l" name="ucpt_l" value="<?php echo $ucpt_perma; ?>">
				<br /><br />
				<button type="submit">Load Dataset</button>
			</form>
		</p>
		<p>
			<small>
				<?php
				echo $total_fetched . ' Total hyper local measures matching this query in Public groups.';
				?>
			</small>
		</p>
		</div>
		<br />
		
		<?php
		if ( $DS_key != "" ) {
		?>
		<meta http-equiv="Refresh" content="0; url=<?php echo $DS_key; ?>" />
		
		<p>We're pulling up this record in our database. Please wait a moment, or click this link to go there now: <a href="<?php echo $DS_key; ?>"><?php echo $DS_key; ?></a>.</p>
		
		<?php
		}
	}

	add_shortcode('pullDS', 'pullDSfunction');		

/*
Funding Details
*/

	function ucpt_funding_page( $group_id = NULL ) {
		if ( class_exists( 'BP_Group_Extension' ) ) :
			class UCPT_Funding extends BP_Group_Extension {
				var $enable_create_step = false;
				var $enable_nav_item = true;
				var $enable_edit_item = true;
				function __construct() {
					$args = array(
						'slug' => 'funding',
						'name' => 'Funding',
						'nav_item_position' => 100
					);
					parent::init( $args );
				}
				function admin_screen( $group_id = null ) {
				echo "<p>These settings are configured via the front-end of your planning tool.</p>";
				}
				function admin_screen_save( $group_id = null ) {
				}			
				function settings_screen( $group_id = null ) {
				?>
				<div class="division">
					<h3>Funding Worksheet</h3>
					<p>Coordinate, track, and report funding progress.</p>

						<label for="ucpt_grant"><h3>Current Grant Name</h3></label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_grant'), 'ucpt_grant', UCPT_EDITOR_SETTINGS ); ?>

						<label for="ucpt_grant_amount"><h3>Current Grant Amount/Terms</h3></label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_grant_amount'), 'ucpt_grant_amount', UCPT_EDITOR_SETTINGS ); ?> 

						<label for="ucpt_grant_start"><h3>Current Grant Start Date</h3></label>
							<input id="ucpt_grant_start" type="date" name="ucpt_grant_start" value="<?php echo ucpt_custom_field_meta('ucpt_grant_start'); ?>" />

						<label for="ucpt_grant_end"><h3>Current Grant End Date</h3></label>
							<input id="ucpt_grant_end" type="date" name="ucpt_grant_end" value="<?php echo ucpt_custom_field_meta('ucpt_grant_end'); ?>" />

						<label for="ucpt_grant_status"><h3>Current Funding Status (Primary/Largest Source)</h3></label>
							<select name="ucpt_grant_status">
								<option value="<?php echo ucpt_custom_field_meta('ucpt_grant_status'); ?>"><?php echo ucpt_custom_field_meta('ucpt_grant_status'); ?></option>
								<option value="Not Funded">Not Funded</option>
								<option value="Funded by County">Funded by County</option>
								<option value="Funded by State Grant">Funded by State Grant</option>
								<option value="Funded by Federal Grant">Funded by Federal Grant</option>
								<option value="Funded by Other Grant">Funded by Other Grant</option>
								<option value="Fee for Service">Fee for Service</option>
								<option value="Other">Other</option>
							</select>

						<label for="ucpt_grant_history"><h3>Additional Funding Notes and Previous Funding</h3></label>
							<?php wp_editor( ucpt_custom_field_meta('ucpt_grant_history'), 'ucpt_grant_history', UCPT_EDITOR_SETTINGS ); ?> 
					<br />
				</div>
				<?php
				}    
				function settings_screen_save( $group_id = NULL ) {
					$plain_fields = array(
						'ucpt_grant',
						'ucpt_grant_amount',
						'ucpt_grant_start',
						'ucpt_grant_end',
						'ucpt_grant_status',
						'ucpt_grant_history'
					);
					foreach( $plain_fields as $field ) {
						$key = $field;
						if ( isset( $_POST[$key] ) ) {
							$value = wp_filter_post_kses($_POST[$key]);
							groups_update_groupmeta( $group_id, $field, $value );
						}

					}
					$editor_record = bp_core_get_user_displayname( bp_loggedin_user_id() );
					$activity_update = "Funding settings were updated by " . $editor_record . ".";
					if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						groups_post_update(array('content' => $activity_update, 'group_id' => $group_id));
					}
				}  
				function display( $group_id = null ) {
					global $bp;
					$group_cover_image_url = bp_attachments_get_attachment('url', array(
						  'object_dir' => 'groups',
						  'item_id' => bp_get_group_id(),
						));
						$ucpt_cover = $group_cover_image_url;
						$ucpt_group_name = bp_get_group_name();
						$ucpt_perma = bp_get_group_permalink( $bp->groups->current_group );
						if (groups_is_user_admin( get_current_user_id(), bp_get_group_id())) {
						?>
						<p><form method="get" action="<?php echo $ucpt_perma; ?>admin/funding"><button type="submit" align="right">Edit Funding Details</button></form></p>
						<?php
						}
						?>
						<div style="background: linear-gradient(rgba(10, 118, 211, 0.85), rgba(8, 94, 168, 0.85)), url('<?php echo $ucpt_cover; ?>'); background-size:100%; width=100%; min-height: 150px; padding: 30px;"><div style="font-size: 32px; color: #fff;">Health Improvement Funding</div><br /><div style="font-size: 18px; color: #efefef;">"<?php echo $ucpt_group_name; ?>"</div><br/><div style="font-size: 10px; color: #efefef;">"<?php echo $ucpt_perma; ?>"</div></div>
						<div class="division">
						<p><h3>Current Grant Name:</h3> <?php echo ucpt_custom_field_meta("ucpt_grant"); ?></p>
						</div>
						<div class="division">
						<p><h3>Current Grant Amount/Terms:</h3> <?php echo ucpt_custom_field_meta("ucpt_grant_amount"); ?></p>
						</div>
						<div class="division">
						<p><h3>Current Grant Start Date:</h3> <?php echo ucpt_custom_field_meta("ucpt_grant_start"); ?></p>
						</div>
						<div class="division">
						<p><h3>Current Grant End Date:</h3> <?php echo ucpt_custom_field_meta("ucpt_grant_end"); ?></p>
						</div>
						<div class="division">
						<p><h3>Current Funding Status (Primary/Largest Source):</h3> <?php echo ucpt_custom_field_meta("ucpt_grant_status"); ?></p>
						</div>
						<div class="division">
						<p><h3>Additional Funding Notes and Previous Funding:</h3> <?php echo ucpt_custom_field_meta("ucpt_grant_history"); ?></p>
						</div>
						<?php
						ucpt_credits();
				} 
			}
			bp_register_group_extension( 'UCPT_Funding' );
			 
			endif;
		}
			
	add_action( 'bp_include', 'ucpt_funding_page' );

// End Modules

/**
  *Compatability Helpers
  */

// Thanks to https://www.wpbeginner.com

function ucpt_gd( $editors ) {
$gd_editor = ‘WP_Image_Editor_GD’;
$editors = array_diff( $editors, array( $gd_editor ) );
array_unshift( $editors, $gd_editor );
return $editors;
}
add_filter( ‘wp_image_editors’, ‘ucpt_gd’ );