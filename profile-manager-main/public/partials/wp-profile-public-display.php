<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Wp_Profile
 * @subpackage Wp_Profile/public/partials
 */
?>

            <div calss="profile_table table-responsive text-nowrap">
                <table border="1" class="table table-striped table-bordered" cellpadding="2"  cellspacing="2" id="sortTable">
                <thead>
                    <tr>
                        <?php                      
                       if ($order_by && $order_by == "title") {
                            $orders = 'ASC';
                            $icon = '<span class="dashicons dashicons-arrow-down"></span>';
                            if ($order == 'ASC') {
                                $orders = 'DESC';
                                $icon = '<span class="dashicons dashicons-arrow-up"></span>';
                            }
                        } else {
                            $orders = 'ASC';
                            $icon = '<span class="dashicons dashicons-arrow-down"></span>';
                        }
                       
                        ?>
                        <th scope="col"><?php echo __( 'No', 'wp-profile' ); ?></th>
                        <th scope="col" class="profile-action" page="<?php echo $page;?>" order_by="title" order="<?php echo $orders;?>"><?php echo __( 'Profile Name', 'wp-profile' ); ?><?php echo $icon;?></th>
                        <th scope="col" ><?php echo __( 'Age', 'wp-profile' ); ?></th>
                        <th scope="col" ><?php echo __( 'Years Of Experience', 'wp-profile' ); ?></th>
                        <th scope="col" ><?php echo __( 'No of jobs complated ', 'wp-profile' ); ?></th>
                        <th scope="col" class=" px-5"><?php echo __( 'Ratings', 'wp-profile' ); ?></th>
                        <th scope="col" ><?php echo __( 'Skill', 'wp-profile' ); ?></th>
                        <th scope="col" ><?php echo __( 'Education', 'wp-profile' ); ?></th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $no = 1;
                $noRow = ($page - 1) * $limit + 1;
                $totalPages = ceil($totalPosts / $limit);
                $totalPosts = $total_records;
                $startRow = ($page - 1) * $limit + 1;
                if($totalPosts > 0){
                    foreach ($data['profile_data'] as $key => $profile) {                   
                        $profile_title = $profile->post_title;
                        $profile_id = $profile->ID;
                        $meta_data =  get_post_meta($profile_id);  
                        $profile_dob =  get_post_meta($profile_id,'profile_DOB',true);
                        $profile_Hobbies = get_post_meta($profile_id,'profile_Hobbies',true);
                        $profile_Interest = get_post_meta($profile_id,'profile_Interest',true);
                        $profile_expericence = get_post_meta($profile_id,'profile_expericence',true);
                        $profile_rating = get_post_meta($profile_id,'profile_rating',true);
                        $profile_jobs = get_post_meta($profile_id,'profile_jobs',true);
                        $profile_link = get_permalink($profile_id);
                        $dateOfBirth = $profile_dob;
                        $dob = new DateTime($dateOfBirth);
                        $now = new DateTime();
                        $diff = $now->diff($dob);
                        $age = $diff->y;
                        $skills = implode('<br>',wp_get_post_terms( $profile_id, 'skill',array( 'fields' => 'names' )));
                        $education = implode('<br>',wp_get_post_terms( $profile_id, 'education',array( 'fields' => 'names' )));
                        ?>
                        <tr>
                            <td class="fit" scope="row"><?php echo __( $startRow, 'wp-profile' ); ?></td>
                            <td class="fit"><a href="<?php echo $profile_link;?>"><?php echo __( $profile_title, 'wp-profile' ); ?></a></td>
                            <td class="fit text-center"><?php echo __( $age, 'wp-profile' ); ?></td>
                            <td class="fit text-center" ><?php echo __( $profile_expericence, 'wp-profile' ); ?></td>
                            <td class="fit text-center"><?php echo __( $profile_jobs, 'wp-profile' ); ?></td>
                            <td class="fit ">
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
                            <td class="fit"><?php echo __( $skills, 'wp-profile' );?></td>
                            <td class="fit"><?php echo __( $education, 'wp-profile' );?></td>
                        </tr>

                    <?php $startRow++; } 
                }else{?>
                    <tr>
                        <td colspan="8"><?php echo __( 'No Records Found.', 'wp-profile' ); ?></td>
                    </tr>
                <?php }?>
                </tbody>
             </table>

            <?php
              $pagination =  paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, $page ),
                'total' => $total_pages,
                'next_text' => 'Next',
                'prev_text' => __( 'Prev', 'twentyfifteen' ),

            ));

            echo '<ul class="pagination" order_by="title" order="'.$order.'">';
            echo $pagination;
            echo '</ul>';
            ?>
        </div>
    </div>
</div>
