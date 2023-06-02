<?php
/***Template : Single Profile  */

if( wp_is_block_theme() ) {
  block_template_part('header');
 
}
get_header();

$profile_id = get_the_ID();
 
$profile_dob =  get_post_meta($profile_id,'profile_DOB',true);
$profile_Hobbies = get_post_meta($profile_id,'profile_Hobbies',true);
$profile_Interest = get_post_meta($profile_id,'profile_Interest',true);
$profile_expericence = get_post_meta($profile_id,'profile_expericence',true);
$profile_rating = get_post_meta($profile_id,'profile_rating',true);
$profile_jobs = get_post_meta($profile_id,'profile_jobs',true);
$skills = implode(', ',wp_get_post_terms( $profile_id, 'skill',array( 'fields' => 'names' )));
$education = implode('<br>',wp_get_post_terms( $profile_id, 'education',array( 'fields' => 'names' )));
?>
<div class="container">
  <div class="row">
    <div calss="back-button">
      <a class="btn btn-primary" href="<?php echo wp_get_referer(); ?>" role="button"><< <?php echo __( 'Back', 'wp-profile' ); ?></a>
    </div>
    <div class="col-lg-12 text-center">
     <h1><?php echo get_the_title(); ?></h1>
    </div>
    <div class="col-lg-12 text-center">
      <table border="1"  class="table table-striped table-bordered single-profile-table" cellpadding="2"  cellspacing="2"  >
        <tbody>
          <tr>
            <td>  <strong><?php echo __( 'Date of birth', 'wp-profile' ); ?> </strong></td>
            <td>  <?php echo $profile_dob ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'Hobbies', 'wp-profile' ); ?></td>
            <td> <?php echo $profile_Hobbies ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'Interest', 'wp-profile' ); ?></td>
            <td>  <?php echo $profile_Interest ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'Skill', 'wp-profile' ); ?></td>
            <td>  <?php echo $skills; ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'Education', 'wp-profile' ); ?></td>
            <td>  <?php echo $education; ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'No of expericence', 'wp-profile' ); ?></td>
            <td>  <?php echo $profile_expericence; ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'No of jobs', 'wp-profile' ); ?></td>
            <td>  <?php echo $profile_jobs; ?></td>
          </tr>

          <tr>
            <td> <?php echo __( 'Rating', 'wp-profile' ); ?></td>
            <td>  
              <div class="rate">
              <?php 
                  for($i = 5; $i >= 1;$i--){ ?>
                      <?php
                      if($i <= $profile_rating){
                          $class = "clr-yellow";
                      }else{
                          $class = "";
                      } ?>
                      <input type="radio" id="star<?php echo $i;?>" name="rate_filter" value="<?php echo $i;?>" />
                      <label for="star<?php echo $i;?>"  class="<?php echo $class;?>" ><?php echo $i;?> stars</label>
                  <?php } ?>
              
                </div>    
            </td>
          </tr>
        </tbody>
      </table>  
    </div>
    
   
   
  </div>
</div>
<?php

if( wp_is_block_theme() ) {
  block_template_part('footer');
}

get_footer();

?>