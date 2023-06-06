<?php
/**
 * Plugin Name:       Careers Block
 * Description:       Block to display and change career meta data.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Simon Flöter
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
   
  if (function_exists('pll__')) {
    $transl_role = pll__('Role');
    $transl_location = pll__('Location');
    $transl_startdate = pll__('Start date');
    $transl_apply = pll__('Apply');
    $transl_learnmore = pll__('Learn more');
    $transl_no_role = pll__('No role');
    $transl_no_location = pll__('No location');
    $transl_no_startdate = pll__('No start date given');
   } else {
    $transl_role = 'Role';
    $transl_location = 'Location';
    $transl_startdate = 'Start date';
    $transl_apply = 'Apply';
    $transl_learnmore = 'Learn more';
    $transl_no_role = 'No role';
    $transl_no_location = 'No location';
    $transl_no_startdate = 'No start date given';
   }

  $id = get_the_ID();
  $meta = get_metadata( 'post', $id );
  $title = get_the_title( $id );
  $urltitle = str_replace('%26%23038%3B', '%26', urlencode($title));
  $linkparams = '?candidature='.$urltitle;
  if(ICL_LANGUAGE_CODE=='en') {
    $language = 'en';
    $apply_page_name = 'apply';
  } elseif(ICL_LANGUAGE_CODE=='fr') {
    $language = 'fr';
    $apply_page_name = 'postuler';
  } else {
    $language = 'en';
  };
  
  if ( isset($meta['duree'][0]) ) {
    $duration = $meta['duree'][0];
    $content = '<div class="role"><span class="label">'.$transl_role.':</span> '.sprintf($duration).'</div>';
  } else {
    $content = '<div class="role"><span class="label">'.$transl_role.':</span> '.$transl_no_role.'</div>';
  }

  if ( isset($meta['location'][0]) ) {
    $location = $meta['location'][0];
    $content = $content.'<div class="location"><span class="label">'.$transl_location.':</span> '.sprintf($location).'</div>';
  }
  else {
    $content = $content.'<div class="location"><span class="label">'.$transl_location.':</span> '.$transl_no_location.'</div>';
  }

  if ( isset($meta['date_de_debut'][0]) ) {
    $startdate = $meta['date_de_debut'][0];
    $content = $content.'<div class="startdate"><span class="label">'.$transl_startdate.':</span> '.sprintf($startdate).'</div>';
  }
  else {
    $content = $content.'<div class="startdate"><span class="label">'.$transl_startdate.':</span> '.$transl_no_startdate.'</div>';
  }

  if ( isset($meta['type_d_ejob'][0]) ) {
    $department = $meta['type_d_ejob'][0];
    $departmentEncoded = base64_encode($department);
    $linkparams = $linkparams.'&service='.urlencode($departmentEncoded);
  }
  else {
    $linkparams = $linkparams.'&service='.urlencode(base64_encode('jobs@woodoo.com'));
  }

  if ( isset($meta['type_de_contrat'][0]) ) {
    if ( 
      $meta['type_de_contrat'][0] == 'Apprentissage' OR 
      $meta['type_de_contrat'][0] == 'Stage') {
      $linkparams = $linkparams.'&contrat=Internship';
    }
    else {
      $linkparams = $linkparams.'&contrat='.$meta['type_de_contrat'][0];    
    }
  }
  else {
    $linkparams = $linkparams.'&contrat=CDI';
  }

  // if ( isset($meta['destinataire'][0]) ) {
  //   $destination = $meta['destinataire'][0];
  //   $content = $content.'<div class="destination">Destination: '.sprintf($destination).'</div>';
  // }

  if ( isset($meta['lien_different'][0]) ) {
    $pdflink = $meta['lien_different'][0];
  } 
  else {
    $pdflink = '';
  }
  
  $pdf_button_markup = '
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
        href="'.sprintf($pdflink).'">'.
        $transl_learnmore
      .'</a>
    </div>
  ';
  if ($pdflink == '') {
    $pdf_button_markup = '';
  }
  $buttons = '
    <div class="wp-block-buttons is-layout-flex career-buttons">'
      .$pdf_button_markup.
      '<div class="wp-block-button is-style-fill">
        <a 
          class="wp-block-button__link wp-element-button apply-link" 
          style="
            padding-top:10px;
            padding-right:20px;
            padding-bottom:10px;
            padding-left:20px;"
          href="/'.$apply_page_name.'/'.$linkparams.'">'.
            $transl_apply
        .'</a>
      </div>
    </div>';
  $content = $content.$buttons;
  $content = "<div class='career-data'><h3 class='wp-block-post-title career-title'>".get_the_title( $id )."</h3>".$content."</div>";
  return $content;
}
function create_block_careers_block_block_init() {
  if (function_exists('pll_register_string')) {
    pll_register_string( 'careers-apply', 'Apply', 'polylang', false );
    pll_register_string( 'careers-learnmore', 'Learn more', 'polylang', false );
    pll_register_string( 'careers-role', 'Role', 'polylang', false );
    pll_register_string( 'careers-location', 'Location', 'polylang', false );
    pll_register_string( 'careers-start-date', 'Start date', 'polylang', false );
  }

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
  register_post_meta( 'careers', 'type_de_contrat', array(
      'show_in_rest' => true,
      'single' => true,
      'type' => 'string'
  ) );
}
add_action( 'init', 'register_post_meta_careers' );
