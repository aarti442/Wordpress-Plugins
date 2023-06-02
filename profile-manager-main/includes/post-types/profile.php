<?php
/**
 * Registers the `profile` post type.
 */
function profile_init() {
	
	register_post_type(
		PROFILE,
		[
			'labels'                => [
				'name'                  => __( 'Profiles', 'wp-profile' ),
				'singular_name'         => __( 'Profile', 'wp-profile' ),
				'all_items'             => __( 'All Profiles', 'wp-profile' ),
				'archives'              => __( 'Profile Archives', 'wp-profile' ),
				'attributes'            => __( 'Profile Attributes', 'wp-profile' ),
				'insert_into_item'      => __( 'Insert into Profile', 'wp-profile' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Profile', 'wp-profile' ),
				'featured_image'        => _x( 'Featured Image', 'profile', 'wp-profile' ),
				'set_featured_image'    => _x( 'Set featured image', 'profile', 'wp-profile' ),
				'remove_featured_image' => _x( 'Remove featured image', 'profile', 'wp-profile' ),
				'use_featured_image'    => _x( 'Use as featured image', 'profile', 'wp-profile' ),
				'filter_items_list'     => __( 'Filter Profiles list', 'wp-profile' ),
				'items_list_navigation' => __( 'Profiles list navigation', 'wp-profile' ),
				'items_list'            => __( 'Profiles list', 'wp-profile' ),
				'new_item'              => __( 'New Profile', 'wp-profile' ),
				'add_new'               => __( 'Add New', 'wp-profile' ),
				'add_new_item'          => __( 'Add New Profile', 'wp-profile' ),
				'edit_item'             => __( 'Edit Profile', 'wp-profile' ),
				'view_item'             => __( 'View Profile', 'wp-profile' ),
				'view_items'            => __( 'View Profiles', 'wp-profile' ),
				'search_items'          => __( 'Search Profiles', 'wp-profile' ),
				'not_found'             => __( 'No Profiles found', 'wp-profile' ),
				'not_found_in_trash'    => __( 'No Profiles found in trash', 'wp-profile' ),
				'parent_item_colon'     => __( 'Parent Profile:', 'wp-profile' ),
				'menu_name'             => __( 'Profiles', 'wp-profile' ),
			],
			'public'                => true,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => [ 'title', 'editor','custom-fields' ],
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-groups',
			'show_in_rest'          => true,
			'rest_base'             => 'profile',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		]
	);

}

add_action( 'init', 'profile_init' );

/**
 * Sets the post updated messages for the `profile` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `profile` post type.
 */
function profile_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[PROFILE] = [
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Profile updated. <a target="_blank" href="%s">View Profile</a>', 'wp-profile' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'wp-profile' ),
		3  => __( 'Custom field deleted.', 'wp-profile' ),
		4  => __( 'Profile updated.', 'wp-profile' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Profile restored to revision from %s', 'wp-profile' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Profile published. <a href="%s">View Profile</a>', 'wp-profile' ), esc_url( $permalink ) ),
		7  => __( 'Profile saved.', 'wp-profile' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Profile submitted. <a target="_blank" href="%s">Preview Profile</a>', 'wp-profile' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Profile scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Profile</a>', 'wp-profile' ), date_i18n( __( 'M j, Y @ G:i', 'wp-profile' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Profile draft updated. <a target="_blank" href="%s">Preview Profile</a>', 'wp-profile' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	];

	return $messages;
}

add_filter( 'post_updated_messages', 'profile_updated_messages' );

/**
 * Sets the bulk post updated messages for the `profile` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `profile` post type.
 */
function profile_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[PROFILE] = [
		/* translators: %s: Number of Profiles. */
		'updated'   => _n( '%s Profile updated.', '%s Profiles updated.', $bulk_counts['updated'], 'wp-profile' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Profile not updated, somebody is editing it.', 'wp-profile' ) :
						/* translators: %s: Number of Profiles. */
						_n( '%s Profile not updated, somebody is editing it.', '%s Profiles not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-profile' ),
		/* translators: %s: Number of Profiles. */
		'deleted'   => _n( '%s Profile permanently deleted.', '%s Profiles permanently deleted.', $bulk_counts['deleted'], 'wp-profile' ),
		/* translators: %s: Number of Profiles. */
		'trashed'   => _n( '%s Profile moved to the Trash.', '%s Profiles moved to the Trash.', $bulk_counts['trashed'], 'wp-profile' ),
		/* translators: %s: Number of Profiles. */
		'untrashed' => _n( '%s Profile restored from the Trash.', '%s Profiles restored from the Trash.', $bulk_counts['untrashed'], 'wp-profile' ),
	];

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', 'profile_bulk_updated_messages', 10, 2 );


/**
 * Registers the `skill` taxonomy,
 * for use with 'profile'.
 */
function skill_init() {
	register_taxonomy( SKILL, [ PROFILE ], [
		'hierarchical'          => false,
		'public'                => true,
		'show_in_nav_menus'     => true,
		'show_ui'               => true,
		'show_admin_column'     => false,
		'query_var'             => true,
		'rewrite'               => true,
		'capabilities'          => [
			'manage_terms' => 'edit_posts',
			'edit_terms'   => 'edit_posts',
			'delete_terms' => 'edit_posts',
			'assign_terms' => 'edit_posts',
		],
		'labels'                => [
			'name'                       => __( 'Skills', 'wp-profile' ),
			'singular_name'              => _x( 'Skill', 'taxonomy general name', 'wp-profile' ),
			'search_items'               => __( 'Search Skills', 'wp-profile' ),
			'popular_items'              => __( 'Popular Skills', 'wp-profile' ),
			'all_items'                  => __( 'All Skills', 'wp-profile' ),
			'parent_item'                => __( 'Parent Skill', 'wp-profile' ),
			'parent_item_colon'          => __( 'Parent Skill:', 'wp-profile' ),
			'edit_item'                  => __( 'Edit Skill', 'wp-profile' ),
			'update_item'                => __( 'Update Skill', 'wp-profile' ),
			'view_item'                  => __( 'View Skill', 'wp-profile' ),
			'add_new_item'               => __( 'Add New Skill', 'wp-profile' ),
			'new_item_name'              => __( 'New Skill', 'wp-profile' ),
			'separate_items_with_commas' => __( 'Separate skills with commas', 'wp-profile' ),
			'add_or_remove_items'        => __( 'Add or remove skills', 'wp-profile' ),
			'choose_from_most_used'      => __( 'Choose from the most used skills', 'wp-profile' ),
			'not_found'                  => __( 'No skills found.', 'wp-profile' ),
			'no_terms'                   => __( 'No skills', 'wp-profile' ),
			'menu_name'                  => __( 'Skills', 'wp-profile' ),
			'items_list_navigation'      => __( 'Skills list navigation', 'wp-profile' ),
			'items_list'                 => __( 'Skills list', 'wp-profile' ),
			'most_used'                  => _x( 'Most Used', 'skill', 'wp-profile' ),
			'back_to_items'              => __( '&larr; Back to Skills', 'wp-profile' ),
		],
		'show_in_rest'          => true,
		'rest_base'             => 'skill',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	] );

}

add_action( 'init', 'skill_init' );

/**
 * Sets the post updated messages for the `skill` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `skill` taxonomy.
 */
function skill_updated_messages( $messages ) {

	$messages[SKILL] = [
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Skill added.', 'wp-profile' ),
		2 => __( 'Skill deleted.', 'wp-profile' ),
		3 => __( 'Skill updated.', 'wp-profile' ),
		4 => __( 'Skill not added.', 'wp-profile' ),
		5 => __( 'Skill not updated.', 'wp-profile' ),
		6 => __( 'Skills deleted.', 'wp-profile' ),
	];

	return $messages;
}

add_filter( 'term_updated_messages', 'skill_updated_messages' );


/**
 * Registers the `education` taxonomy,
 * for use with 'profile'.
 */
function education_init() {
	register_taxonomy( EDUCATION, [ PROFILE ], [
		'hierarchical'          => false,
		'public'                => true,
		'show_in_nav_menus'     => true,
		'show_ui'               => true,
		'show_admin_column'     => false,
		'query_var'             => true,
		'rewrite'               => true,
		'capabilities'          => [
			'manage_terms' => 'edit_posts',
			'edit_terms'   => 'edit_posts',
			'delete_terms' => 'edit_posts',
			'assign_terms' => 'edit_posts',
		],
		'labels'                => [
			'name'                       => __( 'Educations', 'wp-profile' ),
			'singular_name'              => _x( 'Education', 'taxonomy general name', 'wp-profile' ),
			'search_items'               => __( 'Search Educations', 'wp-profile' ),
			'popular_items'              => __( 'Popular Educations', 'wp-profile' ),
			'all_items'                  => __( 'All Educations', 'wp-profile' ),
			'parent_item'                => __( 'Parent Education', 'wp-profile' ),
			'parent_item_colon'          => __( 'Parent Education:', 'wp-profile' ),
			'edit_item'                  => __( 'Edit Education', 'wp-profile' ),
			'update_item'                => __( 'Update Education', 'wp-profile' ),
			'view_item'                  => __( 'View Education', 'wp-profile' ),
			'add_new_item'               => __( 'Add New Education', 'wp-profile' ),
			'new_item_name'              => __( 'New Education', 'wp-profile' ),
			'separate_items_with_commas' => __( 'Separate educations with commas', 'wp-profile' ),
			'add_or_remove_items'        => __( 'Add or remove educations', 'wp-profile' ),
			'choose_from_most_used'      => __( 'Choose from the most used educations', 'wp-profile' ),
			'not_found'                  => __( 'No educations found.', 'wp-profile' ),
			'no_terms'                   => __( 'No educations', 'wp-profile' ),
			'menu_name'                  => __( 'Educations', 'wp-profile' ),
			'items_list_navigation'      => __( 'Educations list navigation', 'wp-profile' ),
			'items_list'                 => __( 'Educations list', 'wp-profile' ),
			'most_used'                  => _x( 'Most Used', 'education', 'wp-profile' ),
			'back_to_items'              => __( '&larr; Back to Educations', 'wp-profile' ),
		],
		'show_in_rest'          => true,
		'rest_base'             => 'education',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	] );

}

add_action( 'init', 'education_init' );

/**
 * Sets the post updated messages for the `education` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `education` taxonomy.
 */
function education_updated_messages( $messages ) {

	$messages[EDUCATION] = [
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Education added.', 'wp-profile' ),
		2 => __( 'Education deleted.', 'wp-profile' ),
		3 => __( 'Education updated.', 'wp-profile' ),
		4 => __( 'Education not added.', 'wp-profile' ),
		5 => __( 'Education not updated.', 'wp-profile' ),
		6 => __( 'Educations deleted.', 'wp-profile' ),
	];

	return $messages;
}

add_filter( 'term_updated_messages', 'education_updated_messages' );


// /Add the meta box
function add_custom_meta_box() {
    add_meta_box('custom-meta-box', 'Profile Information', 'render_custom_meta_box', 'profile', 'normal', 'default');
}
add_action('add_meta_boxes', 'add_custom_meta_box');


//Callback function to generate the content of the meta box
function render_custom_meta_box($post) {
	
    // Retrieve any existing meta box values
    $profile_DOB = get_post_meta($post->ID, PROFILE_DOB, true);
    $profile_hobbies = get_post_meta($post->ID, PROFILE_HOBBIES, true);
    $profile_interest = get_post_meta($post->ID, PROFILE_INTEREST, true);
    $profile_exp = get_post_meta($post->ID, PROFILE_EXP, true);
    $profile_rating = get_post_meta($post->ID, PROFILE_RATING, true);
    $profile_jobs = get_post_meta($post->ID, PROFILE_JOBS, true);
    ?>
	
	<table id="custom-the-list" cellspacing="5" cellpadding="10">
		<tbody>
			<tr>
				<td class="custom-left"><label for="custom-meta-dob">DOB:</label></td>
				<td><input type="date" id="custom-meta-dob" name="<?php echo PROFILE_DOB;?>" value="<?php echo esc_attr($profile_DOB); ?>" /> </td>
			</tr>
			<tr>
				<td class="custom-left"><label for="custom-meta-hobbies">Hobbies:</label></td>
				<td><input type="text" id="custom-meta-hobbies" name="<?php echo PROFILE_HOBBIES;?>" value="<?php echo esc_attr($profile_hobbies); ?>" /> </td>
			</tr>
			<tr>
				<td class="custom-left"><label for="custom-meta-hobbies">Interest:</label></td>
				<td><input type="text" id="custom-meta-hobbies" name="<?php echo PROFILE_INTEREST;?>" value="<?php echo esc_attr($profile_interest); ?>" /> </td>
			</tr>
			<tr>
				<td class="custom-left"><label for="custom-meta-expericence">Year of Experience:</label></td>
				<td><input type="number" id="custom-meta-expericence" name="<?php echo PROFILE_EXP;?>" value="<?php echo esc_attr($profile_exp); ?>" min="1" max="30"/> </td>
			</tr>
			<tr>
				<td class="custom-left"><label for="custom-meta-rating">Rating:</label></td>
				<td><input type="number" id="custom-meta-rating"  name="<?php echo PROFILE_RATING;?>" value="<?php echo esc_attr($profile_rating); ?>" min="1" max="5"/> </td>
			</tr>
			<tr>
				<td><label for="custom-meta-jobs">No of Jobs Completed:</label>
				<td><input type="number" id="custom-meta-jobs" name="<?php echo PROFILE_JOBS;?>" value="<?php echo esc_attr($profile_jobs); ?>" min="1" max="30"/> </td>
			</tr>
		
		
		
		
		</tbody>
	</table>
    <?php
}

// Step 4: Save the meta box data
function save_custom_meta_box($post_id) {
	
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;


	if(isset($_POST[PROFILE_DOB]) && $_POST[PROFILE_DOB] != ""){
		update_post_meta($post_id, PROFILE_DOB, sanitize_text_field($_POST[PROFILE_DOB]));
	}

	if(isset($_POST[PROFILE_HOBBIES]) && $_POST[PROFILE_HOBBIES] != "")
	update_post_meta($post_id, PROFILE_HOBBIES, sanitize_text_field($_POST[PROFILE_HOBBIES]));

	if(isset($_POST[PROFILE_INTEREST]) && $_POST[PROFILE_INTEREST] != "")
	update_post_meta($post_id, PROFILE_INTEREST, sanitize_text_field($_POST[PROFILE_INTEREST]));

	if(isset($_POST[PROFILE_EXP]) && $_POST[PROFILE_EXP] != "")
	update_post_meta($post_id, PROFILE_EXP, sanitize_text_field($_POST[PROFILE_EXP]));

	if(isset($_POST[PROFILE_RATING]) && $_POST[PROFILE_RATING] != "")
	update_post_meta($post_id, PROFILE_RATING, sanitize_text_field($_POST[PROFILE_RATING]));

	if(isset($_POST[PROFILE_JOBS]) && $_POST[PROFILE_JOBS] != "")
	update_post_meta($post_id, PROFILE_JOBS, sanitize_text_field($_POST[PROFILE_JOBS]));

}
add_action('save_post', 'save_custom_meta_box',);