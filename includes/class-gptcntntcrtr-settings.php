<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Gptcntntcrtr_Settings_Tabs' ) ) {
	class Gptcntntcrtr_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $gptcntntcrtr_options, $gptcntntcrtr_plugin_info;

			$tabs = array(
				'settings'    => array( 'label' => __( 'Settings', 'gpt-ai-content-creator' ) ),
				'misc'        => array( 'label' => __( 'Misc', 'gpt-ai-content-creator' ) ),
				'custom_code' => array( 'label' => __( 'Custom Code', 'gpt-ai-content-creator' ) ),
				/*
				 for "go pro" tab */
				'license'     => array( 'label' => __( 'License Key', 'gpt-ai-content-creator' ) ),
			);

			parent::__construct(
				array(
					'plugin_basename'    => $plugin_basename,
					'plugins_info'       => $gptcntntcrtr_plugin_info,
					'prefix'             => 'gptcntntcrtr',
					'default_options'    => gptcntntcrtr_get_options_default(),
					'options'            => $gptcntntcrtr_options,
					'tabs'               => $tabs,
					'wp_slug'            => 'gpt-ai-content-creator',
					/* for "go pro" tab */
					'pro_page'           => 'admin.php?page=gpt-ai-content-creator-pro.php',
					'bws_license_plugin' => 'gpt-ai-content-creator-pro/gpt-ai-content-creator-pro.php',
					'link_key'           => '61793eecd9fc77083240e03cd1b81e89',
					'link_pn'            => '1061',
				)
			);
		}

		/**
		 * Save plugin options to the database
		 *
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$message = $notice = $error = '';

			if ( isset( $_POST['gptcntntcrtr_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gptcntntcrtr_nonce_field'] ) ), 'gptcntntcrtr_action' ) ) {

				/* Takes all the changed settings on the plugin's admin page and saves them in array 'gptcntntcrtr_options'. */
				$this->options['secret_key']        = isset( $_POST['gptcntntcrtr_secret_key'] ) ? sanitize_text_field( wp_unslash( $_POST['gptcntntcrtr_secret_key'] ) ) : '';
				$this->options['max_tokens']        = isset( $_POST['gptcntntcrtr_max_tokens'] ) && 0 < intval( $_POST['gptcntntcrtr_max_tokens'] ) ? intval( $_POST['gptcntntcrtr_max_tokens'] ) : 16;
				$this->options['temperature']       = isset( $_POST['gptcntntcrtr_temperature'] ) ? floatval( $_POST['gptcntntcrtr_temperature'] ) : 1;
				$this->options['presence_penalty']  = isset( $_POST['gptcntntcrtr_presence_penalty'] ) ? floatval( $_POST['gptcntntcrtr_presence_penalty'] ) : 0;
				$this->options['frequency_penalty'] = isset( $_POST['gptcntntcrtr_frequency_penalty'] ) ? floatval( $_POST['gptcntntcrtr_frequency_penalty'] ) : 0;
				$this->options['best_of']           = isset( $_POST['gptcntntcrtr_best_of'] ) ? floatval( $_POST['gptcntntcrtr_best_of'] ) : 1;
				$this->options['models']            = isset( $_POST['gptcntntcrtr_models'] ) && in_array( sanitize_text_field( wp_unslash( $_POST['gptcntntcrtr_models'] ) ), array( 'text-davinci-003', 'text-davinci-002', 'text-curie-001', 'text-babbage-001', 'text-ada-001' ) ) ? sanitize_text_field( wp_unslash( $_POST['gptcntntcrtr_models'] ) ) : 'text-davinci-003';

				$message = __( 'Settings saved.', 'gpt-ai-content-creator' );

				update_option( 'gptcntntcrtr_options', $this->options );
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() { 
			global $gptcntntcrtr_plugin_info, $wp_version;
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'GPT AI Content Creator Settings', 'gpt-ai-content-creator' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table">
				<tr>
					<th><?php esc_html_e( 'API keys', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<input class="regular-text" type="text" name="gptcntntcrtr_secret_key" value="<?php echo esc_attr( $this->options['secret_key'] ); ?>" maxlength="200" />
						<div class="bws_info gptcntntcrtr_settings_form">
							<?php
							printf(
								esc_html__( 'Go to OpenAI and generate your API key. %1$s Get the API Keys %2$s', 'gpt-ai-content-creator' ),
								'<a target="_blank" href="https://platform.openai.com/account/api-keys">',
								'</a>'
							);
							?>
						</div>
					</td>
				</tr>
				<tr>
					<th colspan="2" class="th_bws_tab_sub">
						<div class="bws_tab_sub_label"><?php esc_html_e( 'Settings for Text', 'gpt-ai-content-creator' ); ?></div>
					</th>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Max tokens', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<input class="small-text" type="number" name="gptcntntcrtr_max_tokens" value="<?php echo esc_attr( $this->options['max_tokens'] ); ?>" min="1" max="4000" step="1" />
						<div class="bws_help_box dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The max tokens parameter sets the maximum number of tokens (words and punctuation marks) that the model is allowed to generate in its response. This can be used to control the length of the generated text.', 'gpt-ai-content-creator' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Temperature', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<input class="small-text" type="number" name="gptcntntcrtr_temperature" value="<?php echo esc_attr( $this->options['temperature'] ); ?>" min="0" max="2" step="0.1" />
						<div class="bws_help_box dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The temperature parameter controls the randomness and creativity of the model\'s responses. A higher temperature will result in more unpredictable and diverse responses, while a lower temperature will produce more conservative and predictable responses.', 'gpt-ai-content-creator' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Presence penalty', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<input class="small-text" type="number" name="gptcntntcrtr_presence_penalty" value="<?php echo esc_attr( $this->options['presence_penalty'] ); ?>" min="-2" max="2" step="0.1" />
						<div class="bws_help_box dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The presence penalty parameter penalizes the model for using certain words or phrases in its response. This can be used to encourage the model to generate more diverse and original responses.', 'gpt-ai-content-creator' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Frequency penalty', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<input class="small-text" type="number" name="gptcntntcrtr_frequency_penalty" value="<?php echo esc_attr( $this->options['frequency_penalty'] ); ?>" min="-2" max="2" step="0.1" />
						<div class="bws_help_box dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The frequency penalty parameter penalizes the model for repeating certain words or phrases in its response. This can be used to encourage the model to generate more varied and natural-sounding responses.', 'gpt-ai-content-creator' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Best Of', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<input class="small-text" type="number" name="gptcntntcrtr_best_of" value="<?php echo esc_attr( $this->options['best_of'] ); ?>" min="1" max="20" step="1" />
						<div class="bws_help_box dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The best of parameter controls how many of the model\'s generated responses are returned to the user. For example, setting best of to 3 will result in the model generating 3 different responses, and returning the one that it deems to be the best. This can be used to increase the likelihood of getting a high-quality response from the model.', 'gpt-ai-content-creator' ); ?></div>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Models', 'gpt-ai-content-creator' ); ?></th>
					<td>
						<select name="gptcntntcrtr_models">
							<option value="text-davinci-003" <?php selected( $this->options['models'], 'text-davinci-003' ); ?>>text-davinci-003</option>
							<option value="text-davinci-002" <?php selected( $this->options['models'], 'text-davinci-002' ); ?>>text-davinci-002</option>
							<option value="text-curie-001" <?php selected( $this->options['models'], 'text-curie-001' ); ?>>text-curie-001</option>
							<option value="text-babbage-001" <?php selected( $this->options['models'], 'text-babbage-001' ); ?>>text-babbage-001</option>
							<option value="text-ada-001" <?php selected( $this->options['models'], 'text-ada-001' ); ?>>text-ada-001</option>
						</select>
						<div class="bws_help_box dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The model parameter refers to the specific version of the GPT-3 architecture that is being used. There are several different versions of the GPT-3 model available, each with different levels of complexity and computational power.', 'gpt-ai-content-creator' ); ?></div>
						</div>
					</td>
				</tr>
			</table>
			<div class="bws_pro_version_bloc pdfprnt-pro-feature">
				<div class="bws_pro_version_table_bloc">
					<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="Close"></button>
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
						<table class="form-table">
							<th colspan="2" class="th_bws_tab_sub">
								<div class="bws_tab_sub_label"><?php esc_html_e( 'Settings for Image', 'gpt-ai-content-creator' ); ?></div>
							</th>
							<tr>
								<th><?php esc_html_e( 'Nubmer', 'gpt-ai-content-creator' ); ?></th>
								<td>
									<input class="small-text" type="number" value="<?php echo esc_attr( $this->options['image_number'] ); ?>" min="1" max="10" step="1" />
									<div class="bws_help_box dashicons dashicons-editor-help">
										<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The number of images to generate', 'gpt-ai-content-creator' ); ?></div>
									</div>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Size', 'gpt-ai-content-creator' ); ?></th>
								<td>
									<select>
										<option value="256x256" <?php selected( $this->options['image_size'], '256x256' ); ?>>256x256</option>
										<option value="512x512" <?php selected( $this->options['image_size'], '512x512' ); ?>>512x512</option>
										<option value="1024x1024" <?php selected( $this->options['image_size'], '1024x1024' ); ?>>1024x1024</option>
									</select>
									<div class="bws_help_box dashicons dashicons-editor-help">
										<div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( 'The size of the generated images', 'gpt-ai-content-creator' ); ?></div>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/gpt-ai-content-creator/?k=61793eecd9fc77083240e03cd1b81e89&pn=1061&v=<?php echo esc_attr( $gptcntntcrtr_plugin_info['Version'] ); ?>&wp_v=<?php echo esc_attr( $wp_version ); ?>" target="_blank" title="GPT AI Content Creator"><?php esc_html_e( 'Upgrade to Pro', 'gpt-ai-content-creator' ); ?></a>
					<div class="clear"></div>
				</div>
			</div>
			<?php wp_nonce_field( 'gptcntntcrtr_action', 'gptcntntcrtr_nonce_field' ); ?>
			<?php
		}
	}
}
