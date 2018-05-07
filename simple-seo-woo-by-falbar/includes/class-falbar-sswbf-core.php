<?php

	defined('SSWBF') or die();

	class Falbar_SSWBF_Core{

		protected $ssbf_obj;
		protected $ssbf_plugin_name;
		protected $ssbf_prefix;
		protected $ssbf_plugin_id;
		protected $ssbf_version;

		protected $main_file_path;
		protected $main_file_name = 'simple-seo-woo-by-falbar.php';
		protected $plugin_name    = 'Simple SEO Woo by falbar';
		protected $prefix_db 	  = '_falbar_';
		protected $plugin_domain  = 'simple-seo-woo-by-falbar';

		protected function ssbf_init(){

			$this->ssbf_obj = new Falbar_SSBF();

			$this->ssbf_plugin_name = $this->ssbf_obj->get_falbar_ssbf_data(array(
				'property' => 'plugin_name'
			));

			$this->ssbf_prefix = $this->ssbf_obj->get_falbar_ssbf_data(array(
				'property' => 'prefix'
			));

			$this->ssbf_plugin_id = $this->ssbf_obj->get_falbar_ssbf_data(array(
				'property' => 'plugin_id'
			));

			$this->ssbf_version = $this->ssbf_obj->get_falbar_ssbf_data(array(
				'property' => 'version'
			));

			$this->prefix_db = $this->prefix_db.$this->ssbf_prefix;

			return false;
		}

		protected function get_product_seo_data($id){

			return array(
				'title' 	  => get_post_meta($id, $this->prefix_db.'_title', true),
				'description' => get_post_meta($id, $this->prefix_db.'_description', true),
				'keywords' 	  => get_post_meta($id, $this->prefix_db.'_keywords', true)
			);
		}

		protected function get_term_seo_data($id){

			return array(
				'title' 	  => get_term_meta($id, $this->prefix_db.'_title', true),
				'description' => get_term_meta($id, $this->prefix_db.'_description', true),
				'keywords' 	  => get_term_meta($id, $this->prefix_db.'_keywords', true)
			);
		}
	}