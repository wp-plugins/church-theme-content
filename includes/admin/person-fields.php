<?php
/**
 * Person Fields
 *
 * Meta boxes and admin columns.
 *
 * @package    Church_Theme_Content
 * @subpackage Admin
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-content
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************
 * TITLE FIELD
 **********************************/

/**
 * Change "Enter title here"
 *
 * @since 0.9
 * @param string $title Default title placeholder
 * @return string Modified placeholder
 */
function ctc_person_title_text( $title ) {

	$screen = get_current_screen();

	if  ( 'ctc_person' == $screen->post_type ) {
		$title = __( 'Enter name here', 'church-theme-content' );
	}

	return $title;

}

add_filter( 'enter_title_here', 'ctc_person_title_text' );

/**********************************
 * META BOXES
 **********************************/

/**
 * Person details
 *
 * @since 0.9
 */
function ctc_add_meta_box_person_details() {

	// Configure Meta Box
	$meta_box = array(

		// Meta Box
		'id' 		=> 'ctc_person_details', // unique ID
		'title' 	=> _x( 'Person Details', 'meta box', 'church-theme-content' ),
		'post_type'	=> 'ctc_person',
		'context'	=> 'normal', // where the meta box appear: normal (left above standard meta boxes), advanced (left below standard boxes), side
		'priority'	=> 'high', // high, core, default or low (see this: http://www.wproots.com/ultimate-guide-to-meta-boxes-in-wordpress/)

		// Fields
		'fields' => array(

			// Example
			/*
			'option_key' => array(
				'name'				=> __( 'Field Name', 'church-theme-content' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> __( 'This is the description below the field.', 'church-theme-content' ),
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, upload, upload_textarea, url
				'checkbox_label'	=> '', //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // text for button that opens media frame
				'upload_title'		=> '', // title appearing at top of media frame
				'upload_type'		=> '', // optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attr => value array (e.g. set min/max for number type)
				'class'				=> '', // class(es) to add to input (try try ctmb-medium, ctmb-small, ctmb-tiny)
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization
				'custom_field'=> '', // function for custom display of field input
			*/

			// Position
			'_ctc_person_position' => array(
				'name'				=> __( 'Position', 'church-theme-content' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> __( "Enter the person's position or title (e.g. Senior Pastor, Deacon, etc.)", 'church-theme-content' ),
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, upload, upload_textarea, url
				'checkbox_label'	=> '', //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // text for button that opens media frame
				'upload_title'		=> '', // title appearing at top of media frame
				'upload_type'		=> '', // optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attr => value array (e.g. set min/max for number type)
				'class'				=> 'ctmb-medium', // class(es) to add to input (try try ctmb-medium, ctmb-small, ctmb-tiny)
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization
				'custom_field'		=> '', // function for custom display of field input
			),

			// Phone
			'_ctc_person_phone' => array(
				'name'				=> _x( 'Phone', 'location meta box', 'church-theme-content' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, upload, upload_textarea, url
				'checkbox_label'	=> '', //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // text for button that opens media frame
				'upload_title'		=> '', // title appearing at top of media frame
				'upload_type'		=> '', // optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attr => value array (e.g. set min/max for number type)
				'class'				=> 'ctmb-medium', // class(es) to add to input (try try ctmb-medium, ctmb-small, ctmb-tiny)
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization
				'custom_field'		=> '', // function for custom display of field input
			),

			// Email
			'_ctc_person_email' => array(
				'name'				=> _x( 'Email', 'location meta box', 'church-theme-content' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, upload, upload_textarea, url
				'checkbox_label'	=> '', //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // text for button that opens media frame
				'upload_title'		=> '', // title appearing at top of media frame
				'upload_type'		=> '', // optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attr => value array (e.g. set min/max for number type)
				'class'				=> 'ctmb-medium', // class(es) to add to input (try try ctmb-medium, ctmb-small, ctmb-tiny)
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> 'sanitize_email', // function to do additional sanitization
				'custom_field'		=> '', // function for custom display of field input
			),

			// URLs
			'_ctc_person_urls' => array(
				'name'				=> __( 'URLs', 'church-theme-content' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'textarea', // text, textarea, checkbox, radio, select, number, upload, upload_textarea, url
				'checkbox_label'	=> '', //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // text for button that opens media frame
				'upload_title'		=> '', // title appearing at top of media frame
				'upload_type'		=> '', // optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attr => value array (e.g. set min/max for number type)
				'class'				=> '', // class(es) to add to input (try try ctmb-medium, ctmb-small, ctmb-tiny)
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization
				'custom_field'		=> '', // function for custom display of field input
			),

		),

	);

	// Add Meta Box
	new CT_Meta_Box( $meta_box );

}

add_action( 'admin_init', 'ctc_add_meta_box_person_details' );

/**********************************
 * ADMIN COLUMNS
 **********************************/

/**
 * Add/remove list columns
 *
 * @since 0.9
 * @param array $columns Columns to manipulate
 * @return array Modified columns
 */
function ctc_person_columns( $columns ) {

	// insert thumbnail after checkbox (before title)
	$insert_array = array();
	$insert_array['ctc_person_thumbnail'] = __( 'Thumbnail', 'church-theme-content' );
	$columns = ctc_array_merge_after_key( $columns, $insert_array, 'cb' );

	// insert columns after title
	$insert_array = array();
	if ( ctc_field_supported( 'people', '_ctc_person_position' ) ) $insert_array['ctc_person_position'] = __( 'Position', 'church-theme-content' );
	if ( ctc_taxonomy_supported( 'people', 'ctc_person_group' ) ) $insert_array['ctc_person_group'] = _x( 'Groups', 'people column', 'church-theme-content' );
	$insert_array['ctc_person_order'] = _x( 'Order', 'sorting', 'church-theme-content' );
	$columns = ctc_array_merge_after_key( $columns, $insert_array, 'title' );

	//change "title" to "name"
	$columns['title'] = _x( 'Name', 'person', 'church-theme-content' );

	return $columns;

}

add_filter( 'manage_ctc_person_posts_columns' , 'ctc_person_columns' ); // add columns

/**
 * Change person list column content
 *
 * @since 0.9
 * @param string $column Column being worked on
 */
function ctc_person_columns_content( $column ) {

	global $post;

	switch ( $column ) {

		// Thumbnail
		case 'ctc_person_thumbnail' :

			if ( has_post_thumbnail() ) {
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . get_the_post_thumbnail( $post->ID, array( 80, 80 ) ) . '</a>';
			}

			break;

		// Position
		case 'ctc_person_position' :

			echo get_post_meta( $post->ID , '_ctc_person_position' , true );

			break;

		// Group
		case 'ctc_person_group' :

			echo ctc_admin_term_list( $post->ID, 'ctc_person_group' );

			break;

		// Order
		case 'ctc_person_order' :

			echo isset( $post->menu_order ) ? $post->menu_order : '';

			break;

	}

}

add_action( 'manage_posts_custom_column' , 'ctc_person_columns_content' ); // add content for columns

/**
 * Enable sorting for new columns
 *
 * @since 0.9
 * @param array $columns Columns being worked on
 * @return array Modified columns
 */
function ctc_person_columns_sorting( $columns ) {

	$columns['ctc_person_position'] = '_ctc_person_position';
	$columns['ctc_person_order'] = 'menu_order';

	return $columns;

}

add_filter( 'manage_edit-ctc_person_sortable_columns', 'ctc_person_columns_sorting' ); // make columns sortable

/**
 * Set how to sort columns (default sorting, custom fields)
 *
 * @since 0.9
 * @param array $args Sorting arguments
 * @return array Modified arguments
 */
function ctc_person_columns_sorting_request( $args ) {

	// admin area only
	if ( is_admin() ) {

		$screen = get_current_screen();

		// only on this post type's list
		if ( 'ctc_person' == $screen->post_type && 'edit' == $screen->base ) {

			// orderby has been set, tell how to order
			if ( isset( $args['orderby'] ) ) {

				switch ( $args['orderby'] ) {

					// Under Name
					case '_ctc_person_position' :

						$args['meta_key'] = '_ctc_person_position';
						$args['orderby'] = 'meta_value'; // alphabetically (meta_value_num for numeric)

						break;

				}

			}

			// orderby not set, tell which column to sort by default
			else {
				$args['orderby'] = 'menu_order'; // sort by Order column by default
				$args['order'] = 'ASC';
			}

		}

	}

	return $args;

}

add_filter( 'request', 'ctc_person_columns_sorting_request' ); // set how to sort columns
