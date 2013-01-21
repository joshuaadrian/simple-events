	/************************************************************************/
	/* CREATE CUSTOM POST TYPE
	/************************************************************************/

	function show_extra_profile_fields($user) { ?>
		<h3>Show Permissions</h3>
		<table class="form-table">
			<tr>
				<th scope="row">Production Company</th>
		    	<td>
		    		<select name="user_prod_permissions" id="user_prod_permissions">
			    		<option value="no-prod-co">&mdash; Select Production Company &mdash;</option>
				    	<?php
				    	$user_prod_permissions = esc_attr(get_the_author_meta('user_prod_permissions', $user->ID));
						$prod_args = array(
							'showposts' => -1,
							'post_type' => 'production-companies',
							'sort'      => 'post_title',
							'order'     => 'ASC'
						);
						query_posts($prod_args);
						if (have_posts()) : while (have_posts()) : the_post();
							$prod_slug = basename(get_permalink());
							$prod_title = get_the_title(); ?>
							<option value="<?php echo $prod_slug; ?>" <?php if ($user_prod_permissions == $prod_slug) { echo ' selected="selected"'; } ?>><?php echo $prod_title; ?></option>
						<?php endwhile; endif; wp_reset_query();
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">User Show Permissions</th>
		    	<td>
			    	<input type="hidden" value="<?php echo $user->ID; ?>" id="cur_user_id" />
			    	<select name="user_show_permissions[]" id="user_show_permissions" multiple="multiple">
			    	<?php
						$user_show_permissions = esc_attr(get_the_author_meta('user_show_permissions', $user->ID));
						$user_show_permissions = substr($user_show_permissions, 0, -1);
						$show_permission       = explode(",", $user_show_permissions);
						$prod_access           = get_user_meta($user->ID, 'user_prod_permissions', true);

						$show_args = array(
							'showposts' => -1,
							'post_type' => 'shows',
							'sort'      => 'post_title',
							'order'     => 'ASC',
							'meta_query' => array(
				        array(
			            'key' => 'my_meta_box_production_company',
			            'value' => $prod_access,
			            'compare' => 'EQUALS'
				        )
					    )
						);
						query_posts($show_args);
						if (have_posts()) : while (have_posts()) : the_post();
							$show_slug = basename(get_permalink());
							$show_title = get_the_title(); ?>
							<option value="<?php echo $show_slug; ?>" <?php if (in_array($show_slug, $show_permission)) { echo ' selected="selected"'; } ?>><?php echo $show_title; ?></option>
						<?php endwhile; endif; wp_reset_query();
						?>
					</select>
					<br />
					<span class="description">
						For Windows: Hold down the control (ctrl) button to select multiple options<br />
						For Mac: Hold down the command button to select multiple options
					</span>
				</td>
			</tr>
		</table>

		<h3>Soundminer Credentials</h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e("Soundminer Username"); ?></th>
		    <td>
		    	<input type="text" name="sm_username" id="sm_username" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('sm_username', $user->ID)); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e("Soundminer Password"); ?></th>
		    <td>
		    	<input type="password" name="sm_password" id="sm_password" class="regular-text" value="<?php echo esc_attr(get_the_author_meta('sm_password', $user->ID)); ?>" />
				</td>
			</tr>
		</table>
	<?php }
	
	add_action('show_user_profile', 'show_extra_profile_fields', 100);
	add_action('edit_user_profile', 'show_extra_profile_fields', 100);
	 
	function save_extra_profile_fields($user_id) {
		if (!current_user_can('edit_user', $user_id))
			return false;
		update_user_meta($user_id, 'user_prod_permissions', $_POST['user_prod_permissions'] );
		$sp = $_POST['user_show_permissions'];
		$sp_new;
		foreach($sp as $value) {
			$sp_new .= $value . ',';
		}
		update_user_meta($user_id, 'user_show_permissions', $sp_new);
		update_user_meta($user_id, 'sm_username', $_POST['sm_username'] );
		update_user_meta($user_id, 'sm_password', $_POST['sm_password'] );
	}

	add_action('personal_options_update', 'save_extra_profile_fields');
	add_action('edit_user_profile_update', 'save_extra_profile_fields');

	/************************************************************************/
	/* CUSTOM META DATA
	/************************************************************************/

	add_action('add_meta_boxes', 'cd_meta_box_add');

	function cd_meta_box_add() {
	  $template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
	  add_meta_box('meta-box-production-company', 'Production Company', 'cd_meta_box_production_company', 'shows', 'side', 'core');
	}

	function cd_meta_box_production_company($post) {
	  $values = get_post_custom($post->ID);
	  $prod_co = isset($values['my_meta_box_production_company'] ) ? esc_attr( $values['my_meta_box_production_company'][0] ) : '';
	  wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	  ?>
	  <p>
	  	<select name="my_meta_box_production_company" id="my_meta_box_production_company">
    		<option value="select-production-company">&mdash; Select Production Company &mdash;</option>
	    	<?php
	    	$user_show_permissions = esc_attr(get_the_author_meta('user_show_permissions', $user->ID));
			$args=array(
				'showposts' => -1,
				'post_type' => 'production-companies',
				'sort'      => 'post_title',
				'order'     => 'ASC',
			);
			query_posts($args);
			if (have_posts()) : while (have_posts()) : the_post();
				$slug = basename(get_permalink());
				$title = get_the_title(); ?>
				<option value="<?php echo $slug; ?>" <?php if ($prod_co == $slug) { echo ' selected="selected"'; } ?>><?php echo $title; ?></option>';
			<?php endwhile; endif; wp_reset_query();
			?>
		</select>
	  </p>
	  <?php 
	}

	add_action('save_post', 'cd_meta_box_save');

	function cd_meta_box_save($post_id) {
	  // Bail if we're doing an auto save
	  if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;
	  
	  // if our nonce isn't there, or we can't verify it, bail
	  if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' )) return;
	  
	  // if our current user can't edit this post, bail
	  if(!current_user_can('edit_post')) return;
	  
	  // now we can actually save the data
	  $allowed = array( 
	    'a' => array( // on allow a tags
	      'href' => array() // and those anchords can only have href attribute
	    ),
	    'strong' => array()
	  );
	  
	  // Probably a good idea to make sure your data is set
	  if( isset( $_POST['my_meta_box_production_company'] ) )
	    update_post_meta( $post_id, 'my_meta_box_production_company', wp_kses( $_POST['my_meta_box_production_company'], $allowed ) );
	}



// 	// Add thumbnails to edit screens
// 	if ( !function_exists('fb_AddThumbColumn') && function_exists('add_theme_support') ) {
		
// 		function fb_AddThumbColumn($cols) {
// 			$cols['thumbnail'] = __('Thumbnail');
// 			return $cols;
// 		}
		
// 		function fb_AddThumbValue($column_name, $post_id) {
// 			$width = (int) 35;
// 			$height = (int) 35;
// 			if ( 'thumbnail' == $column_name ) {
				
// 				$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
				
// 				$attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				
// 				if ($thumbnail_id)
// 					$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
// 				elseif ($attachments) {
// 					foreach ( $attachments as $attachment_id => $attachment ) {
// 						$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
// 					}
// 				}
// 				if ( isset($thumb) && $thumb ) {
// 					echo $thumb;
// 				} else {
// 					echo __('None');
// 				}
// 		}

// }

// // Add to Products
// add_filter( 'manage_se_products_posts_columns', 'fb_AddThumbColumn' );
// add_action( 'manage_se_products_posts_custom_column', 'fb_AddThumbValue', 10, 2 );

// // Add to Galleries
// add_filter( 'manage_se_gallery_posts_columns', 'fb_AddThumbColumn' );
// add_action( 'manage_se_gallery_posts_custom_column', 'fb_AddThumbValue', 10, 2 );

// // Add to Products
// add_filter( 'manage_se_bios_posts_columns', 'fb_AddThumbColumn' );
// add_action( 'manage_se_bios_posts_custom_column', 'fb_AddThumbValue', 10, 2 );

// // Add to Products
// add_filter( 'manage_se_programs_posts_columns', 'fb_AddThumbColumn' );
// add_action( 'manage_se_programs_posts_custom_column', 'fb_AddThumbValue', 10, 2 );

// }