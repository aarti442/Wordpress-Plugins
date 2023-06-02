<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The file that defines the actions of plugin
 *
 * @since      1.0.0
 * @package    Wp_Profile
 * @subpackage Wp_Profile/includes
 * @author     Aarti 
 */
class Wp_Profile_Actions {
    private $plugin_name;
    private $version;
    private $template;
    public function __construct($plugin_name, $version) {
        
    if ( defined( 'WP_PROFILE_VERSION' ) ) {
			$this->version = WP_PROFILE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-profile';
        $this->template = new TemplateHelper( PLUGIN_DIR . "/public/partials/" );
    }

  /**
	 * Function to display the content of profile listing which is called by the shortcode
	 * @since    1.0.0
   * @param array $atts
   * @param string $content
   * @return string
	 */

    public function profile_list_callback($atts, $content = '') {
        global $wpdb;
        ob_start();
        
        if(isset($atts['per_page']) && $atts['per_page'] != ""){
          $per_page = $atts['per_page'];
        }else{
          $per_page = 5;
        }
        $skill_args = array(
          'taxonomy' => SKILL,
          'orderby' => 'name',
          'order'   => 'ASC'
        );
        $skill_data = get_categories($skill_args);
        $edu_args = array(
          'taxonomy' => EDUCATION,
          'orderby' => 'name',
          'order'   => 'ASC'
        );
        $edu_data = get_categories($edu_args);
        $data = array(
          'edu_data'      => $edu_data,
          'skill_data'    => $skill_data,
        );
        $this->template->render('/wp-profile-public-filter.php', $data);
        ?>
        <input type="hidden" value="<?php echo $per_page;?>" class="paginationpage">
        
        <div id="responsedata" class="mw-100 mt-4"></div>
       
        <?php
        $content .= ob_get_clean();
        return $content;
    } 

  /**
	 * Function to display the content of filtered data called by the ajax
	 * @since    1.0.0
   * @return void
	 */

    public function wp_profile_filter_callback() {
      global  $wpdb;
      parse_str($_REQUEST['formData'],$formdata);
      $order_by = ( isset($_REQUEST['order_by']) ? $_REQUEST['order_by'] : 'title' );
      $order = ( isset($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC' );
      $limit = $_REQUEST['limit'];
      $search_title = $formdata['search'];
      $age = $formdata['age'];
      $profile_expericence = $formdata['year_of_exp'];
      $profile_jobs = $formdata['no_of_jobs'];
      $page = ( isset($_REQUEST['page']) ? $_REQUEST['page'] : 1 );
      $skills = $formdata['skills'];  
      $education = $formdata['education'];
      $rate_filter = sanitize_text_field($formdata['rate_filter']);
      $metaquery = array();
      $query_args = array(
          'post_type' => PROFILE,
          'post_status' => 'publish',
          'posts_per_page' => $limit,
          'paged' => $page,
          'orderby' => $order_by, 
          'order' => $order, 
      );

      if($search_title){
        $query_args["s"] = $search_title;
      }

      if(!empty($rate_filter)){
        $meta_query[] = array(
            'key' => PROFILE_RATING,
            'value' => $rate_filter,
            'compare' => '='
          );
      }

      if(!empty($profile_jobs)){
        $meta_query[] = array(
            'key' => PROFILE_JOBS,
            'value' => $profile_jobs,
            'compare' => '='
          );
      }

      if(!empty($profile_expericence)){
        $meta_query[] = array(
            'key' => PROFILE_EXP,
            'value' => $profile_expericence,
            'compare' => '='
          );
      }

      if($skills){
        $tax_query[] = array(
          'taxonomy' => SKILL,
          'field' => 'term_id',
          'compare' => 'IN',
          'terms' => $skills
        );
      }

      if($education){
        $tax_query[] = array(
          'taxonomy' => EDUCATION,
          'field' => 'term_id',
          'compare' => 'IN',
          'terms' => $education
        );
      }
      $today = new DateTime();  // Get the current date
      if($age){
        $birthDate = $today->sub(new DateInterval('P' . $age . 'Y'))->format('Y-m-d');
        $meta_query['meta_query'] = array(
          array(
            'key' => PROFILE_DOB,
            'value' => $birthDate,
            'compare' => '>=',
            'type' => 'DATE',
          ),
        );
      }

      if(!empty($meta_query)){
          if(count($meta_query) > 1) {
            $meta_query['relation'] = "AND";
          }
          $query_args['meta_query'] = $meta_query;
      }

      if(!empty($tax_query)){
          if(count($tax_query) > 1) {
            $tax_query['relation'] = "AND";
          }
          $query_args['tax_query'] = $tax_query;
      }
  
      $query = new WP_Query($query_args);
      if(!empty($query->posts)){
        $profile_data = $query->posts;
        $total_pages = $query->max_num_pages;
        $total_records = $query->found_posts;
      }
   
      $data = array(
        'profile_data'  => $profile_data,
        'skills'  => $skills,
        'education'  => $education,
        'search_title'  => $search_title,
        'age'  => $age,
        'rate_filter'  => $rate_filter,
        'order_by' => $order_by,
        'order' => $order,
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'page' => $page,
        'limit' => $limit
      );
      $this->template->render('/wp-profile-public-display.php', $data);
      exit;
    }

   /**
	 * Function to set the single template for single profile list
	 * @since    1.0.0
   * @param type $single
   * @return type
	 */

    function profile_single_callback($single) {
      global $post;
      if ( $post->post_type == 'profile' ) {
          if ( file_exists( PLUGIN_DIR . '/public/partials/profile-single.php' ) ) {
              return PLUGIN_DIR . '/public/partials/profile-single.php';
          }
      }
      return $single;
  }
}