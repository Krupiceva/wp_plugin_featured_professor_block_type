<?php

/*
  Plugin Name: Featured Professor Block Type
  Description: Show proffesor in blog post
  Version: 1.0
  Author: Krupiceva
  Text Domain: featured-professor
  Domain Path: /languages
  
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'inc/generateProfessorHTML.php';
require_once plugin_dir_path(__FILE__) . 'inc/relatedPostsHTML.php';

class FeaturedProfessor {
  function __construct() {
    add_action('init', [$this, 'onInit']);
    add_action('rest_api_init', [$this, 'profHTML']);
    add_filter('the_content', [$this, 'addRelatedPosts']);
  }

  function onInit() {
    wp_register_script('featuredProfessorScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-i18n', 'wp-editor'));
    wp_register_style('featuredProfessorStyle', plugin_dir_url(__FILE__) . 'build/index.css');

    wp_set_script_translations('featuredProfessorScript', 'featured-professor', plugin_dir_path(__FILE__) . '/languages');

    register_block_type('ourplugin/featured-professor', array(
      'render_callback' => [$this, 'renderCallback'],
      'editor_script' => 'featuredProfessorScript',
      'editor_style' => 'featuredProfessorStyle'
    ));

    register_meta('post', 'featuredprofessor', array(
      'show_in_rest' => true,
      'type' => 'number',
      'single' => false
    ));

    load_plugin_textdomain('featured-professor', false, dirname(plugin_basename(__FILE__)) . '/languages');
  }

  /* Frontend */
  //HTML for fronted part of block type of website
  function renderCallback($attributes) {
    if($attributes['profId']){
      wp_enqueue_style('featuredProfessorStyle');
      return generateProfessorHTML($attributes['profId']);
    } else {
      return NULL;
    }
  }

  //Add related blog posts to professor post type
  function addRelatedPosts($content){
    if(is_singular('teacher') && in_the_loop() && is_main_query()){
      return $content . relatedPostsHTML(get_the_id());
    }
    return $content;
  }

  /* Backend */
  //Custom rest api for professor html to be rendered in admin edit screen
  function profHTML(){
    register_rest_route('featuredProfessor/v1', 'getHTML', array(
      'methods' => WP_REST_SERVER::READABLE, // GET
      'callback' => [$this, 'getProfHTML']
    ));
  }

  //HTML to be rendered in admin edit screen
  function getProfHTML($data){
    return generateProfessorHTML($data['profId']);
  }

}

$featuredProfessor = new FeaturedProfessor();