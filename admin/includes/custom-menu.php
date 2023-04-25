<?php
/**
 * Portal custom menu settings
 *
 * @since 3.3.1
 * @author Sunilsinh Gohil [BizTech]
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Add menu item language name meta field
 *
 * This class demonstrate the usage of menu item custom meta fields
 *
 * @since 0.1.0
 */
class BCP_Menu_Item_Custom_Meta_Fields {

    private $plugin_name;
    private $version;
    private $bcp_helper;
    private $bcp_languages = array();
    protected static $fields = array();


    /**
     * __construct
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->bcp_helper = new BCPCustomerPortalHelper($this->plugin_name, $this->version);
    }

    /**
     * Initialize
     */
    public function bcp_manage_multilang_menu_item_meta_fields() {

        $this->bcp_languages = array();
        $bcp_enable_multilang = fetch_data_option('bcp_enable_multilang');
        $enabled_languages = fetch_data_option('bcp_portal_language');
        $enabled_languages_data = $this->bcp_helper->get_language_information($enabled_languages);
        if (isset($enabled_languages_data) && is_array($enabled_languages_data) && count($enabled_languages_data) > 0 && $bcp_enable_multilang) {
            foreach ($enabled_languages_data as $lanuage_info) {
                $this->bcp_languages[$lanuage_info['language']] = $lanuage_info['native_name'];
            }
        }
        
        if (count($this->bcp_languages)) {

            add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, 'bcp_add_multilang_menu_item_meta_fields' ), 10, 4 );
            add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'bcp_save_multilang_menu_item_meta_fields' ), 10, 3 );
            add_filter( 'manage_nav-menus_columns', array( __CLASS__, 'bcp_manage_multilang_menu_item_meta_fields_columns' ), 99 );

            //Dynamic Fields Count
            foreach ($this->bcp_languages as $key => $value) {
                self::$fields["label-$key"] = __('Navigation Label')." - $value";
            }
        }
    }

    /**
     * Add custom fields
     * @wp_hook action wp_nav_menu_item_custom_fields
     */
    public static function bcp_add_multilang_menu_item_meta_fields( $id, $item, $depth, $args ) {
        foreach ( self::$fields as $_key => $label ) :
            $key   = sprintf( 'menu-item-%s', $_key );
            $id    = sprintf( 'edit-%s-%s', $key, $item->ID );
            $name  = sprintf( '%s[%s]', $key, $item->ID );
            $value = get_post_meta( $item->ID, $key, true );
            $class = sprintf( 'field-%s', $_key );
            ?>
                <p class="description description-wide <?php echo esc_attr( $class ) ?>">
                    <?php printf(
                        '<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
                        esc_attr( $id ),
                        esc_html( $label ),
                        esc_attr( $name ),
                        esc_attr( $value )
                    ) ?>
                </p>
            <?php
        endforeach;
    }

    /**
     * Save custom field value
     * @wp_hook action wp_update_nav_menu_item
     */
    public static function bcp_save_multilang_menu_item_meta_fields( $menu_id, $menu_item_db_id, $menu_item_args ) {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

        foreach ( self::$fields as $_key => $label ) {
            $key = sprintf( 'menu-item-%s', $_key );

            // Sanitize
            if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
                // Do some checks here...
                $value = $_POST[ $key ][ $menu_item_db_id ];
            } else {
                $value = null;
            }

            // Update
            if ( ! is_null( $value ) ) {
                update_post_meta( $menu_item_db_id, $key, $value );
            } else {
                delete_post_meta( $menu_item_db_id, $key );
            }
        }
    }

    /**
     * Add our fields to the screen options toggle
     * @wp_hook filter manage_nav-menus_columnadd_menu_item_metas
     */
    public static function bcp_manage_multilang_menu_item_meta_fields_columns( $columns ) {
        $columns = array_merge( $columns, self::$fields );

        return $columns;
    }

    public function bcp_multilang_menu_item_change_title( $atts, $item, $args ) {
        global $post;
        if ((isset($post) && is_object($post)) && (isset($post->ID) && $post->ID != '')) {
            if (in_array($post->ID, $this->bcp_helper->bcp_pages_list())) {
                $bcp_current_language = 'en_US';
                $bcp_default_language = fetch_data_option('bcp_default_language');

                if ($bcp_default_language) {
                    $bcp_current_language = $bcp_default_language;
                }

                if (isset($_SESSION['bcp_language']) && !empty($_SESSION['bcp_language'])) {
                    $bcp_current_language = $_SESSION['bcp_language'];
                }

                if (isset($bcp_current_language) && !empty($bcp_current_language)) {

                    $meta_key = 'menu-item-label-' . $bcp_current_language;

                    $lang_title = get_post_meta($item->ID, $meta_key, true);
                    if (!empty($lang_title)) {
                        $item->title = $lang_title;
                    }
                }
            }
        }

        return $atts;
    }
}



/**
 * Adds the meta box to wordpress nav-menus.php page 
 */
function bcp_register_custom_menu_metabox() {
    $custom_param = array( 0 => 'This param will be passed to bcp_render_menu_meta_box_content' );
    
    add_meta_box('sfcp-menu-items-metabox', __('BCP Modules', 'bcp_portal'), 'bcp_render_menu_meta_box_content', 'nav-menus', 'side', 'default', $custom_param);
}
add_action( 'admin_head-nav-menus.php', 'bcp_register_custom_menu_metabox' );

/**
 * Displays a menu metabox 
 *
 * @param string $object Not used.
 * @param array $args Parameters and arguments. If you passed custom params to add_meta_box(), 
 * they will be in $args['args']
 */
function bcp_render_menu_meta_box_content( $object, $args ) {
    global $objCp, $nav_menu_selected_id;

    // Create an array of objects that imitate Post objects
    $bcp_modules = $objCp->PortalModuleName();
    if (isset($bcp_modules->contact)) {
        unset($bcp_modules->contact);
    }
    $i = 0001;

    $bcp_listmodules = array();

    foreach( $bcp_modules as $key => $values ) {

        $i++;
        if ( $key == 'status' ){
            continue;
        }
        
        $item = json_decode($values);
       
        $module_item = (object) array(
            'ID' => $i,
            'db_id' => 0,
            'menu_item_parent' => 0,
            'object_id' => 3,
            'post_parent' => 0,
            'type' => 'custom',
            'object' => 'sfcp-'.$key,
            'type_label' => 'BCP Plugin',
            'title' => $item->PluralName,
            'url' => home_url( 'customer-portal/'.$key.'/list/' ),
            'target' => '',
            'attr_title' => $key,
            'description' => '',
            'classes' => array($key),
            'xfn' => '',
        );
        array_push($bcp_listmodules, $module_item);
    }
    
    $logout = 
        (object) array(
            'ID' => $i,
            'db_id' => 0,
            'menu_item_parent' => 0,
            'object_id' => 1,
            'post_parent' => 0,
            'type' => 'custom',
            'object' => 'bcp-logout',
            'type_label' => 'BCP Plugin',
            'title' => __('Logout' , 'bcp_portal'),
            'url' => home_url( '?bcp-logout=1' ),
            'target' => '',
            'attr_title' => 'logout',
            'description' => '',
            'classes' => array(),
            'xfn' => '',
        
    );
    array_push($bcp_listmodules, $logout);


    $db_fields = false;

    // If your links will be hieararchical, adjust the $db_fields array bellow
    
    $walker = new Walker_Nav_Menu_Checklist( $db_fields );

    $removed_args = array(
        'action',
        'customlink-tab',
        'edit-menu-item',
        'menu-item',
        'page-tab',
        '_wpnonce',
    ); ?>
    <div id="sfcp-modules-menu-items">
        <div id="tabs-panel-sfcp-modules-all" class="tabs-panel tabs-panel-active">
            <ul id="sfcp-menu-items-checklist-pop" class="categorychecklist form-no-clear" >
                <?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $bcp_listmodules ), 0, (object) array( 'walker' => $walker ) ); ?>
            </ul>

            <p class="button-controls">
                
                <span class="list-controls hide-if-no-js">
                    <input type="checkbox" id="sfcp-menu-select-all" class="select-all">
                    <label for="sfcp-menu-select-all"><?php _e( 'Select All'); ?></label>
                </span>
                <span class="add-to-menu">
                    <input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu'); ?>" name="add-sfcp-modules-menu-item" id="submit-sfcp-modules-menu-items" />
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
    </div>
    <?php
}