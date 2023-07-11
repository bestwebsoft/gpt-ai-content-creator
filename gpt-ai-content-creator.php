<?php
/**
Plugin Name: GPT AI Content Creator by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/gpt-ai-content-creator/
Description: Generate content with GPT AI Content Creator
Author: BestWebSoft
Text Domain: gpt-ai-content-creator
Domain Path: /languages
Version: 1.1.0
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
 */

/*
	© Copyright 2020  BestWebSoft  ( https://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add BWS menu */
if ( ! function_exists( 'gptcntntcrtr_add_pages' ) ) {
	function gptcntntcrtr_add_pages() {
		global $submenu, $gptcntntcrtr_plugin_info, $wp_version;
		$settings = add_menu_page( __( 'GPT AI Content Creator Settings', 'gpt-ai-content-creator' ), 'GPT AI Content Creator', 'manage_options', 'gpt-ai-content-creator.php', 'gptcntntcrtr_settings_page', 'none' );
		add_submenu_page( 'gpt-ai-content-creator.php', __( 'GPT AI Content Creator Settings', 'gpt-ai-content-creator' ), __( 'Settings', 'gpt-ai-content-creator' ), 'manage_options', 'gpt-ai-content-creator.php', 'gptcntntcrtr_settings_page' );

		add_submenu_page( 'gpt-ai-content-creator.php', 'BWS Panel', 'BWS Panel', 'manage_options', 'gptcntntcrtr-bws-panel', 'bws_add_menu_render' );

		$submenu['gpt-ai-content-creator.php'][] = array(
			'<span style="color:#d86463"> ' . esc_html__( 'Upgrade to Pro', 'gpt-ai-content-creator' ) . '</span>',
			'manage_options',
			'https://bestwebsoft.com/products/wordpress/plugins/gpt-ai-content-creator/?k=61793eecd9fc77083240e03cd1b81e89&pn=1061&v=' . $gptcntntcrtr_plugin_info["Version"] . '&wp_v=' . $wp_version
		);

		add_action( 'load-' . $settings, 'gptcntntcrtr_add_tabs' );
	}
}

if ( ! function_exists( 'gptcntntcrtr_plugins_loaded' ) ) {
	function gptcntntcrtr_plugins_loaded() {
		load_plugin_textdomain( 'gpt-ai-content-creator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Initialization */
if ( ! function_exists( 'gptcntntcrtr_init' ) ) {
	function gptcntntcrtr_init() {
		global $gptcntntcrtr_plugin_info, $gptcntntcrtr_options;

		if ( empty( $gptcntntcrtr_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$gptcntntcrtr_plugin_info = get_plugin_data( __FILE__ );
		}

		/* add general functions */
		require_once dirname( __FILE__ ) . '/bws_menu/bws_include.php';
		bws_include_init( plugin_basename( __FILE__ ) );

		/* check compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $gptcntntcrtr_plugin_info, '5.6' );

		/* Get/Register and check settings for plugin */
		if ( is_admin() ) {
			gptcntntcrtr_settings();
		}
	}
}

/* Function for admin_init */
if ( ! function_exists( 'gptcntntcrtr_admin_init' ) ) {
	function gptcntntcrtr_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $gptcntntcrtr_plugin_info, $bws_shortcode_list;

		/* Function for bws menu */
		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array(
				'id'      => '1061',
				'version' => $gptcntntcrtr_plugin_info['Version'],
			);
		}

		/* add Plugin to global $bws_shortcode_list */
		$bws_shortcode_list['plgnnm'] = array( 'name' => 'GPT AI Content Creator' );
	}
}

if ( ! function_exists( 'gptcntntcrtr_settings' ) ) {
	function gptcntntcrtr_settings() {
		global $gptcntntcrtr_options, $gptcntntcrtr_plugin_info;

		/* Install the option defaults */
		if ( ! get_option( 'gptcntntcrtr_options' ) ) {
			$options_default = gptcntntcrtr_get_options_default();
			add_option( 'gptcntntcrtr_options', $options_default );
		}

		/* Get options from the database */
		$gptcntntcrtr_options = get_option( 'gptcntntcrtr_options' );

		if ( ! isset( $gptcntntcrtr_options['plugin_option_version'] ) || $gptcntntcrtr_options['plugin_option_version'] !== $gptcntntcrtr_plugin_info['Version'] ) {
			$options_default                               = gptcntntcrtr_get_options_default();
			$gptcntntcrtr_options                          = array_merge( $options_default, $gptcntntcrtr_options );
			$gptcntntcrtr_options['plugin_option_version'] = $gptcntntcrtr_plugin_info['Version'];
			$update_option                                 = true;
		}

		if ( isset( $update_option ) ) {
			update_option( 'gptcntntcrtr_options', $gptcntntcrtr_options );
		}
	}
}

if ( ! function_exists( 'gptcntntcrtr_get_options_default' ) ) {
	function gptcntntcrtr_get_options_default() {
		global $gptcntntcrtr_plugin_info;

		$default_options = array(
			'plugin_option_version'   => $gptcntntcrtr_plugin_info['Version'],
			'display_settings_notice' => 1,
			'suggest_feature_banner'  => 1,
			/* end deneral options */
			'models'                  => 'text-davinci-003',
			'max_tokens'              => 1500,
			'temperature'             => 1,
			'number'                  => 1,
			'presence_penalty'        => 0,
			'frequency_penalty'       => 0,
			'best_of'                 => 1,
			'image_number'            => 1,
			'image_size'              => '256x256',
			'secret_key'              => '',
		);

		return $default_options;
	}
}

/* Function formed content of the plugin's admin page. */
if ( ! function_exists( 'gptcntntcrtr_settings_page' ) ) {
	function gptcntntcrtr_settings_page() {
		global $title;

		if ( ! class_exists( 'Bws_Settings_Tabs' ) ) {
			require_once dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php';
		}
		require_once dirname( __FILE__ ) . '/includes/class-gptcntntcrtr-settings.php';
		$page = new gptcntntcrtr_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
		<div class = "wrap">
			<h1><?php echo $title; ?></h1>
			<?php $page->display_content(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'gptcntntcrtr_admin_head' ) ) {
	function gptcntntcrtr_admin_head() {
		global $gptcntntcrtr_plugin_info, $gptcntntcrtr_options;
		wp_enqueue_style( 'gptcntntcrtr_general', plugins_url( 'css/style-general.css', __FILE__ ), false, $gptcntntcrtr_plugin_info['Version'] );

		if ( isset( $_GET['page'] ) && 'gpt-ai-content-creator.php' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		} elseif ( is_admin() ) {
			wp_enqueue_style( 'gptcntntcrtr_stylesheet', plugins_url( 'css/admin-style.css', __FILE__ ), false, $gptcntntcrtr_plugin_info['Version'] );
			wp_enqueue_script( 'gptcntntcrtr_gutenberg_script', plugins_url( 'js/gutenberg-script.js', __FILE__ ), array( 'jquery', 'code-editor', 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-util', 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-api' ), true, $gptcntntcrtr_plugin_info['Version'] );
			wp_localize_script(
				'gptcntntcrtr_gutenberg_script',
				'gptcntntcrtr_vars',
				array(
					'ajax_nonce'         => wp_create_nonce( 'gptcntntcrtr-ajax-nonce' ),
					'add_content_text'   => esc_html__( 'Add Content with GPT', 'gpt-ai-content-creator' ),
					'empty_title_error'  => esc_html__( 'Please set a post title before generating content.', 'gpt-ai-content-creator' ),
					'empty_key_error'    => esc_html__( 'Please provide your API key before generating content on the plugin Settings page.', 'gpt-ai-content-creator' ),
					'prompt_placeholder' => esc_html__( 'Type your prompt here...', 'gpt-ai-content-creator' ),
					'key_exist'          => isset( $gptcntntcrtr_options['secret_key'] ) && ! empty( $gptcntntcrtr_options['secret_key'] ) ? 1 : 0,
				)
			);
		}
	}
}

/* Functions creates other links on plugins page. */
if ( ! function_exists( 'gptcntntcrtr_action_links' ) ) {
	function gptcntntcrtr_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename( __FILE__ );
			}
			if ( $file === $this_plugin ) {
				$settings_link = '<a href="admin.php?page=gpt-ai-content-creator.php">' . __( 'Settings', 'gpt-ai-content-creator' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}
/* End function gptcntntcrtr_action_links */

if ( ! function_exists( 'gptcntntcrtr_links' ) ) {
	function gptcntntcrtr_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file === $base ) {
			if ( ! is_network_admin() ) {
				$links[] = '<a href="admin.php?page=gpt-ai-content-creator.php">' . __( 'Settings', 'gpt-ai-content-creator' ) . '</a>';
			}
			$links[] = '<a href="https://wordpress.org/plugins/gpt-ai-content-creator/faq/" target="_blank">' . __( 'FAQ', 'gpt-ai-content-creator' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'gpt-ai-content-creator' ) . '</a>';
		}
		return $links;
	}
}
/* End function gptcntntcrtr_links */

/* Add help tab  */
if ( ! function_exists( 'gptcntntcrtr_add_tabs' ) ) {
	function gptcntntcrtr_add_tabs() {
		$screen = get_current_screen();
		$args   = array(
			'id'      => 'plgnnm',
			'section' => '',
		);
		bws_help_tab( $screen, $args );
	}
}

/* GPT Query  */
if ( ! function_exists( 'gptcntntcrtr_request' ) ) {
	function gptcntntcrtr_request( $prompt ) {
		global $gptcntntcrtr_options;
		$error_content    = '';
		$response_content = '';

		if ( empty( $gptcntntcrtr_options['secret_key'] ) ) {
			$error_content = esc_html__( 'Please provide your API key before generating content on the plugin Settings page.', 'gpt-ai-content-creator' );
		} else {
			$request_body = array(
				'prompt'            => $prompt,
				'max_tokens'        => $gptcntntcrtr_options['max_tokens'],
				'temperature'       => $gptcntntcrtr_options['temperature'],
				'n'                 => $gptcntntcrtr_options['number'],
				'presence_penalty'  => $gptcntntcrtr_options['presence_penalty'],
				'frequency_penalty' => $gptcntntcrtr_options['frequency_penalty'],
				'best_of'           => $gptcntntcrtr_options['best_of'],
				'stream'            => false,
				'stop'              => null,
			);

			$postfields = wp_json_encode( $request_body );

			try {
				$response = wp_remote_post(
					'https://api.openai.com/v1/engines/' . $gptcntntcrtr_options['models'] . '/completions',
					array(
						'method'      => 'POST',
						'timeout'     => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'headers'     => array(
							'Content-Type'  => 'application/json',
							'Authorization' => 'Bearer ' . $gptcntntcrtr_options['secret_key'],
						),
						'body'        => wp_json_encode( $request_body ),
					)
				);
				if ( ( ! is_wp_error( $response ) ) && ( 200  === wp_remote_retrieve_response_code( $response ) ) ) {
					$response_content = $response['body'];
				} elseif ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_message();
						$error_content = esc_html__( 'Something went wrong 2:', 'gpt-ai-content-creator' ) . $error_message;
					} else {
						$error_content = json_decode( $response['body'] );
					}
				}
			} catch ( Exception $ex ) {
				$error_content = esc_html__( 'Something went wrong. Error #:', 'gpt-ai-content-creator' ) . $ex->getMessage();
			}
		}
		return array(
			'error'    => $error_content,
			'response' => $response_content,
		);
	}
}

if ( ! function_exists( 'gptcntntcrtr_plugin_banner' ) ) {
	function gptcntntcrtr_plugin_banner() {
		global $hook_suffix, $gptcntntcrtr_plugin_info;
		if ( 'plugins.php' === $hook_suffix ) {
			if ( ! is_network_admin() ) {
				bws_plugin_banner_to_settings( $gptcntntcrtr_plugin_info, 'gptcntntcrtr_options', 'gpt-ai-content-creator', 'admin.php?page=gpt-ai-content-creator.php' );
			}
		}
		if ( isset( $_REQUEST['page'] ) && 'gpt-ai-content-creator.php' === $_REQUEST['page'] ) {
			bws_plugin_suggest_feature_banner( $gptcntntcrtr_plugin_info, 'gptcntntcrtr_options', 'gpt-ai-content-creator' );
		}
	}
}

if ( ! function_exists( 'gptcntntcrtr_ajax_callback' ) ) {
	function gptcntntcrtr_ajax_callback() {
		check_ajax_referer( 'gptcntntcrtr-ajax-nonce', 'security' );
		if ( isset( $_POST['is_gutenberg'] ) && 'true' === $_POST['is_gutenberg'] && ! empty( $_POST['post_title'] ) ) {
			$result = gptcntntcrtr_request( sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) );
			if ( empty( $result['error'] ) ) {
				$response = json_decode( $result['response'], true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					if ( isset( $response['error'] ) ) {
						echo wp_json_encode(
							array(
								'error'   => esc_html__( 'Error #:', 'gpt-ai-content-creator' ) . $response['error']['message'],
								'content' => '',
							)
						);
					} elseif ( ! empty( $response['choices'][0]['text'] ) ) {
						echo wp_json_encode(
							array(
								'error'   => '',
								'content' => $response['choices'][0]['text'],
							)
						);
					} else {
						echo wp_json_encode(
							array(
								'error'   => esc_html__( 'The model predicted a completion that begins with a stop sequence, resulting in no output. Consider adjusting your prompt (title)', 'gpt-ai-content-creator' ),
								'content' => '',
							)
						);
					}
				} else {
					echo wp_json_encode(
						array(
							'error'   => esc_html__( 'Something went wrong 5:', 'gpt-ai-content-creator' ) . json_last_error(),
							'content' => '',
						)
					);
				}
			} else {
				echo wp_json_encode(
					array(
						'error' => $result['error']->error->message,
						'content' => '',
					)
				);
			}
		}
		wp_die();
	}
}

/* Function for delete options */
if ( ! function_exists( 'gptcntntcrtr_delete_options' ) ) {
	function gptcntntcrtr_delete_options() {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'gptcntntcrtr_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'gptcntntcrtr_options' );
		}

		require_once dirname( __FILE__ ) . '/bws_menu/bws_include.php';
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

/* Calling a function add administrative menu. */
add_action( 'admin_menu', 'gptcntntcrtr_add_pages' );
add_action( 'plugins_loaded', 'gptcntntcrtr_plugins_loaded' );
add_action( 'init', 'gptcntntcrtr_init' );
add_action( 'admin_init', 'gptcntntcrtr_admin_init' );
/* Adding stylesheets */
add_action( 'wp_enqueue_scripts', 'gptcntntcrtr_admin_head' );
add_action( 'admin_enqueue_scripts', 'gptcntntcrtr_admin_head' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'gptcntntcrtr_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gptcntntcrtr_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'gptcntntcrtr_plugin_banner' );
/* Add AJAX function */
add_action( 'wp_ajax_gptcntntcrtr_ajax_callback', 'gptcntntcrtr_ajax_callback' );
/* Plugin uninstall function */
register_uninstall_hook( __FILE__, 'gptcntntcrtr_delete_options' );