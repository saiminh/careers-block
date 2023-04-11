<?php
/**
 * Plugin Name:       Careers Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       careers-block
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

 function render_career_meta() {
  $id = get_the_ID();
  $meta = get_metadata( 'post', $id );
  $title = get_the_title( $id );
  $urltitle = str_replace('%26%23038%3B', '%26', urlencode($title));
  $linkparams = '?candidature='.$urltitle;
  if ( isset($meta['duree'][0]) ) {
    $duration = $meta['duree'][0];
    $content = '<div class="role"><span class="label">Role:</span> '.sprintf($duration).'</div>';
    if ($duration == 'Full time') {
      $linkparams = $linkparams.'&contrat=CDI';
    } elseif (strpos($duration, 'Internship') !== false) {
      $linkparams = $linkparams.'&contrat=Apprentissage';
    }
  }
  if ( isset($meta['location'][0]) ) {
    $location = $meta['location'][0];
    $content = $content.'<div class="location"><span class="label">Location:</span> '.sprintf($location).'</div>';
  }
  if ( isset($meta['date_de_debut'][0]) ) {
    $startdate = $meta['date_de_debut'][0];
    $content = $content.'<div class="startdate"><span class="label">Start date:</span> '.sprintf($startdate).'</div>';
  }
  if ( isset($meta['type_d_ejob'][0]) ) {
    $department = $meta['type_d_ejob'][0];
    $linkparams = $linkparams.'&service='.$department;
  }
  // if ( isset($meta['destinataire'][0]) ) {
  //   $destination = $meta['destinataire'][0];
  //   $content = $content.'<div class="destination">Destination: '.sprintf($destination).'</div>';
  // }
  if ( isset($meta['lien_different'][0]) ) {
    $pdflink = $meta['lien_different'][0];

    $buttons = '
      <div class="wp-block-buttons is-layout-flex career-buttons">
        <div class="wp-block-button is-style-outline">
          <a 
            class="wp-block-button__link wp-element-button pdf-link" 
            style="
              padding-top:10px;
              padding-right:20px;
              padding-bottom:10px;
              padding-left:20px;
              background-color: transparent;
              color: currentColor;
              border-width: 1px;"
            target="_blank"
            href="'.sprintf($pdflink).'">
            Learn More
          </a>
        </div>
      <div class="wp-block-button is-style-fill">
        <a 
          class="wp-block-button__link wp-element-button apply-link" 
          style="
            padding-top:10px;
            padding-right:20px;
            padding-bottom:10px;
            padding-left:20px;"
          href="/apply/'.$linkparams.'">
          Apply
        </a>
      </div>
    </div>';
    $content = $content.$buttons;

    $content = "<div class='career-data'><h3 class='wp-block-post-title career-title'>".get_the_title( $id )."</h3>".$content."</div>";
  }
  return $content;
}
function create_block_careers_block_block_init() {
  register_block_type( __DIR__ . '/build', array(
    'render_callback' => 'render_career_meta',
  ) );
}
add_action( 'init', 'create_block_careers_block_block_init' );


// register custom meta tag field
function register_post_meta_careers() {
  register_post_meta( 'careers', 'type_d_ejob', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
  register_post_meta( 'careers', 'duree', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
  register_post_meta( 'careers', 'date_de_debut', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
  register_post_meta( 'careers', 'location', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
  register_post_meta( 'careers', 'destinataire', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
  register_post_meta( 'careers', 'lien_different', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
}
add_action( 'init', 'register_post_meta_careers' );
