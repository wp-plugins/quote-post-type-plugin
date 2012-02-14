<?php
/* 
  Plugin Name: quote-posttype-plugin
  Plugin URI: http://gnetos.de
  Description: Plugin zum speichern von Zitaten mit der Hilfe von CustomPostTypes
  Version: 1.2.0
  Author: Tobias Gafner
  Author URI: http://gnetos.de
  License: GPL3
  UPDATE Server: ---
  Min Version: 3.0.0
 
  Copyright 2012  Tobias Gafner  (email : support@gnetos.de)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* Translation start here*/
define("QS_QUOTE",     "Quote");
define("QS_QUOTECATEGORY",     "Tags");
define("QS_QUOTECAUTHOR",     "Author");
$labelsquote = array(
    'name' => _x( 'Quotes Plugin', 'taxonomy general name' ),
    /*'singular_name' => _x( 'Quote', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Quotes' ),
    'popular_items' => __( 'Popular Quotes' ),
    'all_items' => __( 'All Quotes' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Quote' ), 
    'update_item' => __( 'Update Quote' ),
    'add_new_item' => __( 'Add New Quote' ),
    'new_item_name' => __( 'New Quote' ),
    'separate_items_with_commas' => __( 'Separate quotes with commas' ),
    'add_or_remove_items' => __( 'Add or remove quotes' ),
    'choose_from_most_used' => __( 'Choose from the most used quotes' ),
    'menu_name' => __( 'Quotes' ),*/
);
/* Translation end here*/

/**
 * 
 * Enter description here ...
 */
function create_qs_quote_type() {
	global $labelsquote;
  register_post_type( 'qs_quote_type',
    array(
      'labels' => $labelsquote,
      'public' => true,
       'capability_type' => 'post',
      'show_in_nav_menus' =>  false, 
      'exclude_from_search' => false,
  	  'supports' => array( ''),
     // 'taxonomies' => array( 'quote','quotecategory'),
      'rewrite' => false
    )
  );
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $postId
 * @param unknown_type $taxonomy
 */
function qs_quote_type_get_taxos($postId,$taxonomy) {
	$terms = wp_get_object_terms($postId,$taxonomy);
	if(!empty($terms)){
		if(!is_wp_error( $terms )){
			return $terms[0]->name;
		}
	}
}

/**
 * 
 * Enter description here ...
 * @return multitype:string
 */
function create_qs_quote_type_taxonomy() {
	function change_columns( $cols ) {
		$cols = array(
		'cb'       			 => '<input type="checkbox" />',
		'quote'     		 => __( QS_QUOTE ),
	    'quotecategory'      => __( QS_QUOTECATEGORY ),
	    'quoteauthor'		 => __( QS_QUOTECAUTHOR )
		);
		return $cols;
	}
	// Make these columns sortable
	/*function sortable_columns() {
		return array(
		'quote'     => 'quote',
	    'quotecategory' => 'quotecategory',
	    'quoteauthor' => 'quoteauthor'
		);
	} */
	register_taxonomy('quote',
		array (
		0 => 'qs_quote_type',
		),
		array(
		    'hierarchical' => false,
		    'public' => true,
		    'show_ui' => false,
		    'show_tagcloud' => false,
		  	'label' => __( QS_QUOTE ),
		    'query_var' => true,
		    'rewrite' => false
		)
	);
	register_taxonomy('quoteauthor',
		array (
		0 => 'qs_quote_type',
		), array(
      	'hierarchical' => false,
      	'public' => false,
  		'label' => __( QS_QUOTECAUTHOR ),
      	'show_ui' => false,
      	'query_var' => true,
      	'rewrite' => false
	));
	register_taxonomy('quotecategory',
		array (
		0 => 'qs_quote_type',
		),
		array( 
			'hierarchical' => false,
			'label' => __( QS_QUOTECATEGORY ),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => false,
		//	'singular_label' => 'Kategorie'
		) 
	);
	add_filter( "manage_qs_quote_type_posts_columns", "change_columns" );
	//add_filter( "manage_edit-qs_quote_type_sortable_columns", "sortable_columns" );
}


/**
 * 
 * Enter description here ...
 * @param unknown_type $column
 */
function custom_columns2( $column ) {
	global $post;
	$wpg_row_actions  = '<div class="row-actions"><span class="edit"><a title="'.__('Edit this item', 'quotable').'" href="'.get_admin_url().'post.php?post='.$post->ID.'&amp;action=edit">Edit</a> | </span>';
	$wpg_row_actions .= '<span class="inline hide-if-no-js"><a title="'.__('Edit this item inline', 'quotable').'" class="editinline" href="#">Quick&nbsp;Edit</a> | </span>';
	$wpg_row_actions .= '<span class="trash"><a href="'.wp_nonce_url(get_admin_url().'post.php?post='.$post->ID.'&amp;action=trash', 'delete-post_'.$post->ID).'" title="'.__('Move this item to the Trash', 'quotable').'" class="submitdelete">Trash</a></span>';
	switch ( $column ) {
		case "quotecategory":
			echo qs_quote_type_get_taxos( $post->ID, 'quotecategory');
			break;
		case "quote":
			echo qs_quote_type_get_taxos( $post->ID,'quote').$wpg_row_actions;
			break;
		case "quoteauthor":
			echo qs_quote_type_get_taxos( $post->ID,'quoteauthor');
			break;
	}
}


/**
 * 
 * Enter description here ...
 */	
function add_qs_quote_type_box_quote() {
	add_meta_box('quote_box_ID', __('Quote'), 'qs_quote_type_styling_function', 'qs_quote_type', 'side', 'core');
}	
function add_qs_quote_type_box_quoteauthor() {
	add_meta_box('quoteauthor_box_ID', __('Author'), 'qs_quoteauthor_type_styling_function', 'qs_quote_type', 'side', 'core');
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $post
 */
function qs_quote_type_styling_function($post) {
	echo '<input type="hidden" name="taxonomy_y" id="taxonomy_noncename" value="' .
	wp_create_nonce( 'taxonomy_quote' ) . '" />';
	// Get all theme taxonomy terms
	$quote = qs_quote_type_get_taxos($post->ID, 'quote'); 
	?>
	<p><textarea cols="28" rows="4" name="quote"><?php echo $quote; ?></textarea>
	</p>
	<?php
}

/**
*
* Enter description here ...
* @param unknown_type $post
*/
function qs_quoteauthor_type_styling_function($post) {

	echo '<input type="hidden" name="taxonomy_x" id="taxonomy_noncename" value="' .
	wp_create_nonce( 'taxonomy_quoteauthor' ) . '" />';
	// Get all theme taxonomy terms
	$quoteauthor = qs_quote_type_get_taxos($post->ID, 'quoteauthor');
	?>
	<p><input type="text" value="<?php echo $quoteauthor; ?>" autocomplete="on" size="30" class="form-input-tip" name="quoteauthor" id="new-tag-quoteauthor">
	</p>
	<?php
}
	
/**
 * 
 * Enter description here ...
 * @param unknown_type $post_id
 */
function save_qs_quote_type_taxonomy_quote($post_id) {
	return save_qs_quote_type_taxonomy_data($post_id,'quote' );
}
function save_qs_quote_type_taxonomy_quoteauthor($post_id) {
	return save_qs_quote_type_taxonomy_data($post_id,'quoteauthor' );
}

function save_qs_quote_type_taxonomy_data($post_id,$fieldname) {
	// verify this came from our screen and with proper authorization.

	if ( !wp_verify_nonce( $_POST['taxonomy_y'], 'taxonomy_'.$fieldname ) && !wp_verify_nonce( $_POST['taxonomy_x'], 'taxonomy_'.$fieldname )) {
		return $post_id;
	}
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	return $post_id;

	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	// OK, we're authenticated: we need to find and save the data
	$post = get_post($post_id);
	if (($post->post_type == 'post') || ($post->post_type == 'qs_quote_type')) {
		// OR $post->post_type != 'revision'
		$theme = $_POST[$fieldname];
		wp_set_object_terms( $post_id, $theme, $fieldname );
	}
	return $theme;
}

/**
 * Widget
 * 
 */  
class QSQuoteSidebarRandom_Widget extends WP_Widget {
	function QSQuoteSidebarRandom_Widget() {
		$widget_ops = array( 'classname' => 'widget_qs_quote_random_posts','description' => 'Use this widget to add random quotes to the sidebar.' );
		$this->WP_Widget('sidebar_quote_random_wg', 'Quotes Random Widget', $widget_ops);
	}
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		echo $args['before_widget'];
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$qsCategory = empty($instance['qstag']) ? '' : apply_filters('widget_title', $instance['qstag']);
		$showauthor = empty($instance['showauthor']) ? '' : apply_filters('widget_title', $instance['showauthor']);
		if ( !empty( $title ) ) {
			echo $before_title . $title . $after_title;
		};
		$queryString = 'orderby=rand&numberposts=1&post_type=qs_quote_type&post_status=publish';
    if(!empty($qsCategory) && $qsCategory != '') {
      $queryString .=  "&quotecategory=".$qsCategory;
    }
		$posts = get_posts($queryString);
    foreach($posts as $post) { 
      echo '<p class="qsquote-widget">'.qs_quote_type_get_taxos($post->ID, 'quote')."</p>";
      if(!empty($showauthor) && $showauthor > 0) {
        echo '<p class="qsquote-author-widget" style="float:right">'.qs_quote_type_get_taxos($post->ID, 'quoteauthor')."</p>";
      }
    }
		echo $args['after_widget'];
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['qstag'] = strip_tags($new_instance['qstag']);
		$instance['showauthor'] = strip_tags($new_instance['showauthor']);
		return $instance;
	}
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'entry_title' => '', 'comments_title' => '' ) );
		$title = strip_tags($instance['title']);
		$qsTag = strip_tags($instance['qstag']);
		$showauthor = strip_tags($instance['showauthor']);
		?>
    <p>
    	<label for="<?php echo $this->get_field_id('title'); ?>">Title: <input
    		class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
    		name="<?php echo $this->get_field_name('title'); ?>" type="text"
    		value="<?php echo attribute_escape($title); ?>" />
    	</label>
    </p>
    <p>
    	<label for="<?php echo $this->get_field_id('showauthor'); ?>">Show Author:
      <select name="<?php echo $this->get_field_name('showauthor'); ?>"' id="<?php echo $this->get_field_id('showauthor'); ?>">
        <option value="0" <?php if(empty($showauthor) || $showauthor == '') echo "selected"; ?>>No</option>
        <option value="1" <?php if(!empty($showauthor) || $showauthor > 0) echo "selected"; ?>>Yes</option>
      </select>
    	</label>
    </p>
    <p>
    <?php 
      $qsCategories = get_terms('quotecategory', 'hide_empty=0'); 
      $qsTag = attribute_escape($qsTag); 
    ?>
    <label for="<?php echo $this->get_field_id('qstag'); ?>">Tags: 
    <select name="<?php echo $this->get_field_name('qstag'); ?>"' id="<?php echo $this->get_field_id('qstag'); ?>">
	  <!-- Display themes as options -->
      <option value="" 
      <?php if (empty($qsTag) || $qsTag == '') echo "selected";?>
      >None</option>
      <?php
      foreach ($qsCategories as $qsCategory) {
        if (!empty($qsTag) && !strcmp($qsCategory->name, $qsTag)){
			     echo "<option value='" . $qsCategory->name . "' selected>" . $qsCategory->name . "</option>"; 
			  } else {
           echo "<option value='" . $qsCategory->name . "'>" . $qsCategory->name . "</option>"; 
        }
	    }
      ?>
    </select>  
  </p>
  <?php
  }
}
function qsquoteplugin_register_widgets() {
	register_widget( 'QSQuoteSidebarRandom_Widget' );
}


add_action( 'manage_posts_custom_column' , 'custom_columns2');
add_action('admin_menu', 'add_qs_quote_type_box_quote');
/* Use the save_post action to save new post data */
add_action('save_post', 'save_qs_quote_type_taxonomy_quote');
add_action('admin_menu', 'add_qs_quote_type_box_quoteauthor');
add_action('save_post', 'save_qs_quote_type_taxonomy_quoteauthor');	
add_action( 'init', 'create_qs_quote_type' );
add_action( 'init', 'create_qs_quote_type_taxonomy' );
function qs_quote_flush() {
  create_qs_quote_type();
  create_qs_quote_type_taxonomy();
}
register_activation_hook(__FILE__, 'qs_quote_flush');

add_action( 'widgets_init', 'qsquoteplugin_register_widgets' );