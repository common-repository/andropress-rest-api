<?php
/**
*@package andropress-rest-api
*Plugin Name: AndroPress REST API
*Plugin URI:  https://bsubash.com.np
*Description: Get REST API response as JSON to android app, using token authenticated request .
*Version:     1.4
*Author:      Subash Bhattarai
*Author URI:  http://bsubash.com.np
*Text Domain: andropress-rest-api
*License:     GPL-2.0+
*License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

defined('ABSPATH') or die('you can not access this file !');

class AndroPressRestAPI{

	function __construct(){
		$this->register_app_menu_page();
	}
	function activate(){
		//flush rewrite rules
		flush_rewrite_rules();

		$this->create_table();
		$this->app_menu_page();
	}

	function deactivate(){
		//flush rewrite rules
		flush_rewrite_rules();
	}

	function create_table(){
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$table_name = $wpdb->prefix.'andropress_rest_api';
		if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$query = "CREATE TABLE ".$table_name."(`id` INT NOT NULL AUTO_INCREMENT, `app_name` VARCHAR(100) NOT NULL, `key` VARCHAR(100) NOT NULL, PRIMARY KEY(`id`));";
				dbDelta($query);
		}
	}


	function app_menu_page(){
		add_menu_page('AndroPress REST API', 'AndroPress REST', 'manage_options', 'andropress-rest-api', array($this, 'app_menu_page_callback'), 'dashicons-admin-site');
	}

	function app_menu_page_callback(){
		require_once sanitize_file_name('menu-page.php');
	}

	function register_app_menu_page(){
		add_action('admin_menu', array($this, 'app_menu_page'));
	}

	function enqueue_assets(){
		//boostrap css
		wp_enqueue_style('andropress-rest-api-bootstrap-css', plugins_url('/assets/css/bootstrap-3.3.7.min.css', __FILE__));

		//plugin css
		wp_enqueue_style('andropress-rest-api-style', plugins_url('/assets/css/style.css', __FILE__));

		//bootstrap js
		wp_enqueue_script('andropress-rest-api-bootstrap-script', plugins_url('/assets/js/bootstrap-3.3.7.min.js', __FILE__));

		//jquery-ui
		wp_enqueue_script('andropress-rest-api-jquery-ui', '//code.jquery.com/ui/1.12.1/jquery-ui.js');

		//plugin script
		wp_enqueue_script('andropress-rest-api-script', plugins_url('/assets/js/script.js', __FILE__));
	}

	function register_assets(){
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
	}

	function generate_key(){
		echo sanitize_key(bin2hex(random_bytes(25)));
	}

	function register_generate_key_request(){
		add_action('admin_post_generate_key', array($this, 'generate_key'));
	}

	function insert_app(){
		$app_name = isset($_POST['name']) && sanitize_text_field($_POST['name'])?sanitize_text_field($_POST['name']):null;
		$app_key = isset($_POST['key']) && sanitize_key($_POST['key'])?sanitize_key($_POST['key']):null;
		if($app_name != null && $app_key != null){
			global $wpdb;
			$wpdb->insert($wpdb->prefix.'andropress_rest_api',array(
					'app_name'=>$app_name,
				'key'=>$app_key
				)
			);
			echo 'success';
		}
	}

	function register_insert_app_request(){
		add_action('admin_post_insert_app', array($this, 'insert_app'));
	}

	function delete_app(){
		$id = isset($_POST['id']) && intval($_POST['id'])?intval($_POST['id']):null;
		if($id != null){
			global $wpdb;
			$wpdb->delete($wpdb->prefix.'andropress_rest_api',array(
				'id'=>$id)
			);
			echo 'success';
		}
	}

	function register_delete_app_request(){
		add_action('admin_post_delete_app', array($this, 'delete_app'));
	}

	function andropress_rest_api_response(){
		$key = isset($_GET['key']) && sanitize_key($_GET['key'])?sanitize_key($_GET['key']):null;
		$title = isset($_GET['title']) && sanitize_text_field($_GET['title'])?sanitize_text_field($_GET['title']):null;
		if($key != null && $title != null){
			global $wpdb, $post;
			$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."andropress_rest_api WHERE `key`='$key';");
			if($results){
				switch ($title) {
					case 'post':
						$category_name = isset($_GET['category_name']) && sanitize_text_field($_GET['category_name']) ? sanitize_text_field($_GET['category_name']):null;
						$posts_per_page = isset($_GET['posts_per_page']) && sanitize_text_field($_GET['posts_per_page']) ? sanitize_text_field($_GET['posts_per_page']):null;
						$paged = isset($_GET['paged']) && intval($_GET['paged']) ? intval($_GET['paged']):null;
						$offset = isset($_GET['offset']) && intval($_GET['offset']) ? intval($_GET['offset']):null;
						$s = isset($_GET['s']) && sanitize_text_field($_GET['s']) ? sanitize_text_field($_GET['s']):null;

						$args = array(
							'post_type' => 'post',
							'post_status' => 'publish'
						);
						if($category_name != null){
							$args['category_name'] = $category_name;
						}
						if($posts_per_page != null){
							$args['posts_per_page'] = $posts_per_page;
						}
						if($paged != null){
							$args['paged'] = $paged;
						}
						if($offset != null){
							$args['offset'] = $offset;
						}
						if($s != null){
							$args['s'] = $s;
						}

						//query post with provided arguments
						$query = new WP_Query($args);
						$arr = $this->get_posts_from_query($query);
						wp_reset_postdata();
						wp_reset_query();
						header('Content-Type:application/json');
						echo json_encode($arr);
						break;

					case 'category':
						$category_count = isset($_GET['count']) && intval($_GET['count']) ? intval($_GET['count']):null;
						$posts_per_page = isset($_GET['posts_per_page']) && sanitize_text_field($_GET['posts_per_page']) ? sanitize_text_field($_GET['posts_per_page']):null;
						$show_posts = isset($_GET['posts']) && sanitize_text_field($_GET['posts']) ? sanitize_text_field($_GET['posts']):null;
						$categories = get_categories(array(
							'taxonomy' => 'category',
							'orderby' => 'count',
							'order' => 'DESC',
							'hide_empty' => true,
							'parent' => 0
						));
						$arr = array();
					 	$count = 0;
						foreach($categories as $category){
							if($category_count != null && $count == $category_count){
								break;
							}
							$category_id = $category->term_id;
							$category_name = $category->name;
							$category_slug = $category->slug;
							$posts = ($show_posts != null && $show_posts == 'true') ? $this->get_category_posts($category_slug, $posts_per_page!=null?$posts_per_page:4):null;
							$arr[] = array(
								'category_id' => $category_id,
								'category_name' => $category_name,
								'category_slug' => $category_slug,
								'posts' => $posts
							);
							$count++;
						}
						header('Content-Type:application/json');
						echo json_encode($arr);
						break;

					case 'product':
						$category_name = isset($_GET['category_name']) && sanitize_text_field($_GET['category_name']) ? sanitize_text_field($_GET['category_name']):null;
						$posts_per_page = isset($_GET['posts_per_page']) && sanitize_text_field($_GET['posts_per_page']) ? sanitize_text_field($_GET['posts_per_page']):null;
						$paged = isset($_GET['paged']) && intval($_GET['paged']) ? intval($_GET['paged']):null;
						$offset = isset($_GET['offset']) && intval($_GET['offset']) ? intval($_GET['offset']):null;
						$s = isset($_GET['s']) && sanitize_text_field($_GET['s']) ? sanitize_text_field($_GET['s']):null;

						$args = array(
							'post_type' => 'product',
							'post_status' => 'publish'
						);
						if($category_name != null){
							$args['tax_query'] = array(
								array(
						        'taxonomy' => 'product_cat',
						        'terms' => $category_name,
						        'field' => 'slug',
						        'operator' => 'IN'
						    ));
						}
						if($posts_per_page != null){
							$args['posts_per_page'] = $posts_per_page;
						}
						if($paged != null){
							$args['paged'] = $paged;
						}
						if($offset != null){
							$args['offset'] = $offset;
						}
						if($s != null){
							$args['s'] = $s;
						}

						//query post with provided arguments
						$query = new WP_Query($args);
						$arr = $this->get_products_from_query($query);
						wp_reset_postdata();
						header('Content-Type:application/json');
						echo json_encode($arr);
						break;

					default:
						echo 'invalid request !';
						//wp_redirect(home_url());
						break;
				}
			}else{
				echo 'invalid request !';
				//wp_redirect(home_url());
			}
		}else{
			echo 'invalid request !';
			//wp_redirect(home_url());
		}
	}

	function register_post_api(){
		add_action('admin_post_nopriv_andropress_rest_api', array($this, 'andropress_rest_api_response'));
	}

	/**
	*@return array of posts
	*@param $query WP_Query
	*/
	function get_posts_from_query($query){
		$posts = $query->get_posts();
		$arr = array();
		foreach ($posts as $post) {
			$post_id = $post->ID;
			$post_title = $post->post_title;
			$post_content = $post->post_content;
			$post_date = $post->post_date;
			$post_modified = $post->post_modified;
			$permalink = get_permalink($post->ID);
			$post_excerpt = $post->post_content;

			$categories = get_the_category($post->ID);

			$category_arr = array();
			foreach($categories as $category){
				$category_arr[] = array('id'=>$category->term_id,'name'=>$category->name, 'slug'=>$category->slug);
			}

			$post_author = get_the_author_meta('display_name', $post->post_author);

			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
			$thumbnail_url = $thumb[0];

			$arr[] = array(
				'post_id' => $post_id,
				'post_title' => $post_title,
				'post_content' => $post_content,
				'post_date' => $post_date,
				'post_modified' => $post_modified,
				'permalink' => $permalink,
				'post_excerpt' => $post_excerpt,
				'categories' => $category_arr,
				'post_author' => $post_author,
				'thumbnail_url' => $thumbnail_url,
			);
		}
		return $arr;
	}

	/**
	*@return array of products
	*@param $query WP_Query
	*/
	function get_products_from_query($query){
		$products = $query->get_posts();
		$products_arr = array();
		foreach ($products as $product) {
			$p=wc_get_product($product);
			$thumbs_small=wp_get_attachment_image_src(get_post_thumbnail_id($product->ID),'app-small');
			$thumbs_medium=wp_get_attachment_image_src(get_post_thumbnail_id($product->ID),'app-medium');
			$thumbs_big=wp_get_attachment_image_src(get_post_thumbnail_id($product->ID),'app-big');

			$attachment_ids = $p->get_gallery_attachment_ids();
		    $gallery = array();
		    foreach ($attachment_ids as $attachment_id) {
		    	$image_link = wp_get_attachment_url( $attachment_id );
		    	$gallery[] = array('url'=>$image_link);
		    }

			$terms = get_the_terms($product->ID, 'product_cat');
			$product_category = $terms[0]->slug;

			$products_arr[] = array ('product_id' => $product->ID ,'product_url'=>get_permalink($product->ID), 'product_title' => $product->post_title ,
				'regular_price' => $p->regular_price , 'sale_price' => $p->sale_price , 'short_description' => $p->short_description ,
				'description' => $p->description , 'product_image_small' => $thumbs_small[0] , 'product_image_medium' => $thumbs_medium[0] ,
				'product_image_big' => $thumbs_big[0],'gallery' => empty($gallery)?null:$gallery,'product_category' => $product_category,
			);
		}
		return $products_arr;
	}

	/**
	*@return array of post
	*@param $category category category slug
	*@param $posts_per_page number of posts to query
	*/
	function get_category_posts($category_name, $posts_per_page){
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'category_name' => $category_name,
			'posts_per_page' => $posts_per_page
		);

		$query = new WP_Query($args);
		$posts = $this->get_posts_from_query($query);
		wp_reset_postdata();
		return $posts;
	}

}

if(class_exists('AndroPressRestAPI')){
	$blogPressRestAPI = new AndroPressRestAPI();
	$blogPressRestAPI->register_assets();
	$blogPressRestAPI->register_generate_key_request();
	$blogPressRestAPI->register_insert_app_request();
	$blogPressRestAPI->register_delete_app_request();
	$blogPressRestAPI->register_post_api();
}

//activation
register_activation_hook( __FILE__, array($blogPressRestAPI, 'activate'));

//deactivation
register_deactivation_hook(__FILE__, array($blogPressRestAPI, 'deactivate'));
