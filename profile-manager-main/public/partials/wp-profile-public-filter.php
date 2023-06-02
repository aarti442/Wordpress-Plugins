<?php
$skill_data = $data['skill_data'];
$education_data = $data['edu_data'];
?>
<div class ="profile_section mw-100">
    <div class="container">
        <div class="filter-main">
            <div calss="profile_filter">
                <form id="filter-list-form" class="module-form module-case" >
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                                <label class="form-check-label" for="search"><?php echo __( 'Keyword', 'wp-profile' ); ?></label>
                                <input class="form-control" name="search" type="text" id="search" value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                                <label class="form-check-label" for="search"><?php echo __( 'Skills', 'wp-profile' ); ?></label>
                                <select name="skills[]" id="skills" class="select js-example-basic-multiple form-control" multiple="multiple" >
                                    <option value="">select</options>
                                    <?php
                                        foreach($skill_data as $key => $val){ ?>
                                            <option value="<?php echo $val->term_id;?>"><?php echo $val->name;?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                    </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                        <label class="form-check-label" for="search"> <?php echo __( 'Education', 'wp-profile' ); ?> </label>
                        <select name="education[]" id="education" class="select form-control" multiple data-mdb-filter="true">
                            <option value="">select</options>
                            <?php
                                foreach($education_data as $key => $val){ ?>
                                    <option value="<?php echo $val->term_id;?>"><?php echo $val->name;?></option>
                            <?php }
                            ?>
                        </select>
                                
                        </div>
                    </div>
                 </div>
                 <div class="row">

                    <div class="col-lg-6">
                        <div class="form-group">
                                <label class="form-check-label" for="search"> <?php echo __( 'Age', 'wp-profile' ); ?> </label>
                                <input type="range" id="age" class="form-control" name="age" min="0" max="80" value="0">
                                <span class="ageText"></span>
                        </div>
                    </div>
                                

                    <div class="col-lg-6"> 
                        <div class="form-group">        
                        <label class="form-check-label rating-label" for="search"> <?php echo __( 'Rating', 'wp-profile' ); ?>  </label>                
                        <div class="rate">
                            <input type="radio" id="star5" name="rate_filter" value="5" />
                            <label for="star5" title="text">5 stars</label>
                            <input type="radio" id="star4" name="rate_filter" value="4" />
                            <label for="star4" title="text">4 stars</label>
                            <input type="radio" id="star3" name="rate_filter" value="3" />
                            <label for="star3" title="text">3 stars</label>
                            <input type="radio" id="star2" name="rate_filter" value="2" />
                            <label for="star2" title="text">2 stars</label>
                            <input type="radio" id="star1" name="rate_filter" value="1" />
                            <label for="star1" title="text">1 star</label>
                        </div>
                        </div>
                    </div>

                    <div class="col-lg-6"> 
                        <div class="form-group">        
                        <label class="form-check-label" for="search"> <?php echo __( 'Year of Experience', 'wp-profile' ); ?>  </label>                
                        <input name="year_of_exp" class="form-control" type="number">
                        </div>     
                    </div>

                    <div class="col-lg-6"> 
                        <div class="form-group">        
                        <label class="form-check-label" for="search"> <?php echo __( 'No of jobs', 'wp-profile' ); ?>  </label>                
                        <input name="no_of_jobs" class="form-control" type="number">
                        </div>
                    </div>
                </div>
                        
                    <div class="text-center">  
                     <button type="submit" class="btn btn-primary"> <?php echo __( 'Submit', 'wp-profile' ); ?> </button>
                     <button type="button" class="btn btn-danger" id="reset_form"> <?php echo __( 'Clear', 'wp-profile' ); ?> </button>
                 </div>
                </form>
            </div>
        </div>

