<?php

	defined('SSWBF') or die();

	class Falbar_SSWBF extends Falbar_SSWBF_Core{

		public function __construct(){

			$this->main_file_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$this->main_file_name;

			if(!class_exists('Falbar_SSBF')){

				add_action(
					'admin_init',
					array(
						$this,
						'plugin_off'
					)
				);
			}else{

				parent::ssbf_init();

				if(!version_compare($this->ssbf_version, '1.0.3', '>=')){

					add_action(
						'admin_init',
						array(
							$this,
							'plugin_off'
						)
					);
				}
			}

			return false;
		}

		public function plugin_off(){

			deactivate_plugins($this->main_file_path);

			add_action(
				'admin_notices',
				array(
					$this,
					'notices_plugin_off'
				)
			);

			return false;
		}

			public function notices_plugin_off(){

				echo('<div class="notice notice-warning">');

					echo('<p>');
						echo(__('To work', $this->plugin_domain).' <strong>'.$this->plugin_name.'</strong> '.__('needed plugin: Simple SEO by falbar since v 1.0.3 !', $this->plugin_domain));
					echo('</p>');

				echo('</div>');

				if(!empty($_GET['activate'])){

					unset($_GET['activate']);
				}

				return false;
			}

		public function run(){

			// Activation of the plugin
			register_activation_hook(
				$this->main_file_path,
				array(
					$this,
					'activate'
				)
			);

			// Deactivating the plugin
			register_deactivation_hook(
				$this->main_file_path,
				array(
					$this,
					'deactivate'
				)
			);

			// Adding a box on the page product
			add_action(
				'add_meta_boxes',
				array(
					$this,
					'add_meta_boxes'
				)
			);

				// Save the SEO data
				add_action(
					'save_post',
					array(
						$this,
						'save_post'
					)
				);

			// Add fields for conditions (category, tag)
			add_action(
				'edit_category_form_fields',
				array(
					$this,
					'term_view'
				)
			);
			add_action(
				'edit_tag_form_fields',
				array(
					$this,
					'term_view'
				)
			);

				// Save the SEO data
				add_action(
					'edit_product_cat',
					array(
						$this,
						'save_term'
					)
				);
				add_action(
					'edit_product_tag',
					array(
						$this,
						'save_term'
					)
				);

			// Add SEO tags to the template
			add_action(
				'wp_head',
				array(
					$this,
					'add_meta_tags'
				),
				0
			);

			// Localization plugin
			add_action(
				'plugins_loaded',
				array(
					$this,
					'plugin_textdomain'
				)
			);

			return false;
		}

		public function activate(){

			return false;
		}

		public function deactivate(){

			return false;
		}

		public function add_meta_boxes(){

			add_meta_box(
				$this->ssbf_plugin_id,
				$this->ssbf_plugin_name,
				array(
					$this,
					'boxes_view'
				),
				'product'
			);

			return false;
		}

			public function boxes_view($post){

				$data = $this->get_product_seo_data($post->ID);

				echo('<table class="form-table">');

					echo('<tr class="form-field">');
						echo('<th>');
							echo('<label for="'.$this->ssbf_prefix.'-title">Title</label>');
						echo('</th>');
						echo('<td>');
							echo('<input id="'.$this->ssbf_prefix.'-title" type="text" name="'.$this->ssbf_prefix.'_title" value="'.esc_attr($data['title']).'" />');
						echo('</td>');
					echo('</tr>');

					echo('<tr class="form-field">');
						echo('<th>');
							echo('<label for="'.$this->ssbf_prefix.'-description">Description</label>');
						echo('</th>');
						echo('<td>');
							echo('<textarea id="'.$this->ssbf_prefix.'-description" class="large-text" cols="50" rows="5" name="'.$this->ssbf_prefix.'_description">'.esc_attr($data['description']).'</textarea>');
						echo('</td>');
					echo('</tr>');

					echo('<tr class="form-field">');
						echo('<th>');
							echo('<label for="'.$this->ssbf_prefix.'-keywords">Keywords</label>');
						echo('</th>');
						echo('<td>');
							echo('<textarea id="'.$this->ssbf_prefix.'-keywords" class="large-text" cols="50" rows="2" name="'.$this->ssbf_prefix.'_keywords">'.esc_attr($data['keywords']).'</textarea>');
						echo('</td>');
					echo('</tr>');

				echo('</table>');

				return false;
			}

		public function save_post($id){

			if(!empty($_POST[$this->ssbf_prefix.'_title']) ||
			   !empty($_POST[$this->ssbf_prefix.'_description']) ||
			   !empty($_POST[$this->ssbf_prefix.'_keywords'])){

				$title 		 = sanitize_text_field($_POST[$this->ssbf_prefix.'_title']);
				$description = sanitize_text_field($_POST[$this->ssbf_prefix.'_description']);
				$keywords 	 = sanitize_text_field($_POST[$this->ssbf_prefix.'_keywords']);

				update_post_meta($id, $this->prefix_db.'_title', $title);
				update_post_meta($id, $this->prefix_db.'_description', $description);
				update_post_meta($id, $this->prefix_db.'_keywords', $keywords);

				return true;
			}

			return false;
		}

		public function term_view($term){

			if($term->taxonomy == 'product_cat' || $term->taxonomy == 'product_tag'){

				$data = $this->get_term_seo_data($term->term_id);

				echo('<tr class="form-field">');
					echo('<th>');
						echo('<label for="'.$this->ssbf_prefix.'-title">Title</label>');
					echo('</th>');
					echo('<td>');
						echo('<input id="'.$this->ssbf_prefix.'-title" type="text" name="'.$this->ssbf_prefix.'_title" value="'.esc_attr($data['title']).'" />');
						echo('<p class="description">'.$this->ssbf_plugin_name.'</p>');
					echo('</td>');
				echo('</tr>');

				echo('<tr class="form-field">');
					echo('<th>');
						echo('<label for="'.$this->ssbf_prefix.'-description">Description</label>');
					echo('</th>');
					echo('<td>');
						echo('<textarea id="'.$this->ssbf_prefix.'-description" class="large-text" cols="50" rows="5" name="'.$this->ssbf_prefix.'_description">'.esc_attr($data['description']).'</textarea>');
						echo('<p class="description">'.$this->ssbf_plugin_name.'</p>');
					echo('</td>');
				echo('</tr>');

				echo('<tr class="form-field">');
					echo('<th>');
						echo('<label for="'.$this->ssbf_prefix.'-keywords">Keywords</label>');
					echo('</th>');
					echo('<td>');
						echo('<textarea id="'.$this->ssbf_prefix.'-keywords" class="large-text" cols="50" rows="2" name="'.$this->ssbf_prefix.'_keywords">'.esc_attr($data['keywords']).'</textarea>');
						echo('<p class="description">'.$this->ssbf_plugin_name.'</p>');
					echo('</td>');
				echo('</tr>');
			}

			return false;
		}

		public function save_term($id){

			if(!empty($_POST[$this->ssbf_prefix.'_title']) ||
			   !empty($_POST[$this->ssbf_prefix.'_description']) ||
			   !empty($_POST[$this->ssbf_prefix.'_keywords'])){

				$title 		 = sanitize_text_field($_POST[$this->ssbf_prefix.'_title']);
				$description = sanitize_text_field($_POST[$this->ssbf_prefix.'_description']);
				$keywords 	 = sanitize_text_field($_POST[$this->ssbf_prefix.'_keywords']);

				update_term_meta($id, $this->prefix_db.'_title', $title);
				update_term_meta($id, $this->prefix_db.'_description', $description);
				update_term_meta($id, $this->prefix_db.'_keywords', $keywords);

				return true;
			}

			return false;
		}

		public function add_meta_tags(){

			$page_obj  = get_queried_object();

			$post_type = $page_obj->post_type;
			$taxonomy  = $page_obj->taxonomy;

			if(is_shop() ||
		       (is_product_category() && $taxonomy == 'product_cat') ||
		       (is_product_tag() && $taxonomy == 'product_tag') ||
		       (is_product() && $post_type == 'product')){

				remove_theme_support('title-tag');
				remove_action('wp_head', '_wp_render_title_tag', 1);
			}

			$html = '';

			// shop
			if(is_shop()){

				$shop_id = get_option('woocommerce_shop_page_id');

				$title 		 = get_post_meta($shop_id, $this->prefix_db.'_title', true);
				$description = get_post_meta($shop_id, $this->prefix_db.'_description', true);
				$keywords 	 = get_post_meta($shop_id, $this->prefix_db.'_keywords', true);

				if(!$title){

					$shop = get_post($shop_id);

					$title = $shop->post_title;
				}

				$html = $this->ssbf_obj->get_falbar_ssbf_data(array(
					'method' => array(
						'name'   => 'get_meta_template',
						'values' => array(
							'title' 	  => $title,
							'description' => $description,
							'keywords' 	  => $keywords
						)
					)
				));
			}
			// category
			elseif(is_product_category() && $taxonomy == 'product_cat'){

				$cat_id = $page_obj->term_id;

				$data = $this->get_term_seo_data($cat_id);

				if(!$data['title']){

					$cat = get_category($cat_id);

					$data['title'] = $cat->name;
				}

				$html = $this->ssbf_obj->get_falbar_ssbf_data(array(
					'method' => array(
						'name'   => 'get_meta_template',
						'values' => array(
							'title' 	  => $data['title'],
							'description' => $data['description'],
							'keywords' 	  => $data['keywords']
						)
					)
				));
			}
			// tag
			elseif(is_product_tag() && $taxonomy == 'product_tag'){

				$tag_id = $page_obj->term_id;

				$data = $this->get_term_seo_data($tag_id);

				if(!$data['title']){

					$tag = get_tag($tag_id);

					$data['title'] = $tag->name;
				}

				$html = $this->ssbf_obj->get_falbar_ssbf_data(array(
					'method' => array(
						'name'   => 'get_meta_template',
						'values' => array(
							'title' 	  => $data['title'],
							'description' => $data['description'],
							'keywords' 	  => $data['keywords']
						)
					)
				));
			}
			// product
			elseif(is_product() && $post_type == 'product'){

				global $post;

				$title 		 = get_post_meta($post->ID, $this->prefix_db.'_title', true);
				$description = get_post_meta($post->ID, $this->prefix_db.'_description', true);
				$keywords 	 = get_post_meta($post->ID, $this->prefix_db.'_keywords', true);

				if(!$title){

					$title = $post->post_title;
				}

				$html = $this->ssbf_obj->get_falbar_ssbf_data(array(
					'method' => array(
						'name'   => 'get_meta_template',
						'values' => array(
							'title' 	  => $title,
							'description' => $description,
							'keywords' 	  => $keywords
						)
					)
				));
			}

			echo($html);

			return false;
		}

		public function plugin_textdomain(){

			load_plugin_textdomain(
				$this->plugin_domain,
				false,
				dirname(dirname(plugin_basename(__FILE__ ))).'/languages/'
			);

			return false;
		}
	}