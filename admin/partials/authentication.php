
<h2><?php _e('License', 'bcp_portal'); ?></h2>
<!-- form-table Start -->
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label><?php _e('License Key', 'bcp_portal'); ?> <sup>*</sup></label></th>
            <td>
                <?php
                $readonly = "";
                if ($bcp_licence) {
                    $readonly = "readonly=''";
                }
                ?>
                <input class="regular-text" <?php echo $readonly; ?> type="text" name="bcp_licence_key" value="<?php echo $licence_key; ?>" required="required" />
            </td>
        </tr>
    </tbody>
</table>
<!-- form-table End -->
<?php if ($bcp_licence) { ?>
    <h2><?php _e('Authentication Method', 'bcp_portal'); ?></h2>
    <!-- form-table Start -->
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label><?php _e('Select Method', 'bcp_portal'); ?> <sup>*</sup></label></th>
                <td>
                    <?php
                   
                    if($authentication_method == "jwt_bearer_flow"){
                        $jwt_bearer_flow_selected = "selected";
                        $user_pwd_flow_selected = "";
                    }else if($authentication_method == "user_pwd_flow"){
                        $user_pwd_flow_selected = "selected";
                        $jwt_bearer_flow_selected = "";
                    }else{
                        $user_pwd_flow_selected = "selected";
                        $jwt_bearer_flow_selected = "";
                    }
                    if ($bcp_licence) {
                        $readonly = "readonly=''";
                    }
                    ?>
                    <select name="authentication_method" id="authentication_method">
                        <option value="user_pwd_flow" <?php echo $user_pwd_flow_selected;?>>OAuth 2.0 Username-Password Flow</option>
                        <option value="jwt_bearer_flow" <?php echo $jwt_bearer_flow_selected;?>>OAuth 2.0 JWT Bearer Flow</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>

 
    <h2><?php _e('Authentication', 'bcp_portal'); ?></h2>
        <hr />
            <?php

            if($authentication_method == "user_pwd_flow"){
                $style_user_pwd_flow = "display:block";
                $style_jwt_bearer_flow = "display:none";
                $required_user_pwd_flow = "required=''";
                $required_jwt_bearer_flow = "";
            }else if($authentication_method == "jwt_bearer_flow"){
                $style_user_pwd_flow = "display:none";
                $style_jwt_bearer_flow = "display:block";
                $required_user_pwd_flow = "";
                $required_jwt_bearer_flow = "required=''";
            }else{
                $style_user_pwd_flow = "display:block";
                $style_jwt_bearer_flow = "display:none";
                $required_user_pwd_flow = "required=''";
                $required_jwt_bearer_flow = "";
            }
            ?>
            <!-- form-table Start -->
            <table class="form-table" id="user_pwd_flow" style="<?php echo $style_user_pwd_flow;?>">
                <tbody>
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Consumer Key', 'bcp_portal'); ?> <?php
                                if (empty($client_id)) {
                                    echo "<sup>*</sup>";
                                }
                                ?></label>
                        </th>
                        <td>
                            <?php
                            $hidden_client_id = "";
                            if (!empty($client_id)) {
                                $hidden_client_id .= "******";
                            }
                            $hidden_client_id .= substr($client_id, -5);
                            ?>
                            <input type="text" name="bcp_client_id" value="" <?php
                            if (empty($client_id)) {
                                echo $required_user_pwd_flow;
                            }
                            ?> >
                                <?php if (!empty($client_id)) { ?>
                                <p class="description"><?php echo $hidden_client_id; ?></p>
                            <?php } ?>
                            <input type="hidden" class="bcp_client_id_hidden" value="<?php echo $client_id;?>">
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Consumer Secret', 'bcp_portal'); ?> <?php
                                if (empty($client_secret)) {
                                    echo "<sup>*</sup>";
                                }
                                ?></label>
                        </th>
                        <td>
                            <?php
                            $hidden_client_secret = "";
                            if (!empty($client_secret)) {
                                $hidden_client_secret .= "******";
                            }
                            $hidden_client_secret .= substr($client_secret, -5);
                            ?>
                            <input type="text" name="bcp_client_secret" value="" <?php
                            if (empty($client_secret)) {
                                echo $required_user_pwd_flow;
                            }
                            ?> >
                                <?php if (!empty($client_secret)) { ?>
                                <p class="description"><?php echo $hidden_client_secret; ?></p>
                            <?php } ?>
                            <input type="hidden" class="bcp_client_secret_hidden" value="<?php echo $client_secret;?>">
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Username', 'bcp_portal'); ?> <?php
                                if (empty($username)) {
                                    echo "<sup>*</sup>";
                                }
                                ?></label>
                        </th>
                        <td>
                            <?php
                            $hidden_username = "";
                            if (!empty($username)) {
                                $hidden_username = substr($username, 0, 5);
                                $hidden_username .= "******";
                            }
                            ?>
                            <input type="text" name="bcp_username" value="" <?php
                            if (empty($username)) {
                                echo $required_user_pwd_flow;
                            }
                            ?> >
                                <?php if (!empty($username)) { ?>
                                <p class="description"><?php echo $hidden_username; ?></p>
                            <?php } ?>
                            <input type="hidden" class="bcp_username_hidden" value="<?php echo $username;?>">
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr>
                        <th scope="row"><label><?php _e('Password', 'bcp_portal'); ?></label></th>
                        <td>
                            <input type="password" name="bcp_password" value="">
                            <p class="description"><strong><?php _e('Note: ', 'bcp_portal'); ?></strong><?php _e('If password is blank then authentication will not check.', 'bcp_portal'); ?></p>
                            <input type="hidden" class="bcp_password_hidden" value="<?php echo $password;?>">
                            
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Security Token', 'bcp_portal'); ?> <?php
                                if (empty($security_token)) {
                                    echo "<sup>*</sup>";
                                }
                                ?></label>
                        </th>
                        <td>
                            <?php
                            $hidden_token = "";
                            $bcp_token = "";
                            if (!empty($security_token)) {
                                $hidden_token .= "******";
                            }
                            $hidden_token .= substr($security_token, -5);
                            ?>
                            <input type="text" name="bcp_security_token" value="" autocomplete="off" <?php
                            if (empty($security_token)) {
                                echo $required_user_pwd_flow;
                            }
                            ?> >
                                <?php if (!empty($security_token)) { ?>
                                <p class="description"><?php echo $hidden_token; ?></p>
                            <?php } ?>
                            <input type="hidden" class="bcp_security_token_hidden" value="<?php echo $security_token;?>">
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr class="hide_class">
                        <th scope="row"><label for="bcp_operation_mode"><?php _e('Operation mode', 'bcp_portal'); ?></label></th>
                        <td>
                            <?php
                            ( fetch_data_option('bcp_operation_mode') === FALSE ? update_option('bcp_operation_mode', 'live') : '' ); // If operation mode is not set than set it to live
                            $operation_mode = fetch_data_option('bcp_operation_mode');
                            ?>
                            <select name="bcp_operation_mode" id="bcp_operation_mode">
                                <option value="testing" <?php echo $operation_mode == 'testing' ? 'selected' : ''; ?>><?php _e('Sandbox', 'bcp_portal'); ?></option>
                                <option value="live" <?php echo $operation_mode == 'live' ? 'selected' : ''; ?>><?php _e('Live', 'bcp_portal'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <!-- TR End -->
                </tbody>
            </table>
            <!-- form-table End -->
        
                <!-- form-table Start -->
            <table class="form-table" id="jwt_bearer_flow" style="<?php echo $style_jwt_bearer_flow;?>">
                <tbody>
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Consumer Key', 'bcp_portal'); ?> <?php
                                if (empty($client_id_jwt)) {
                                    echo "<sup>*</sup>";
                                }
                                ?></label>
                        </th>
                        <td>
                            <?php
                            $hidden_client_id_jwt = "";
                            if (!empty($client_id_jwt)) {
                                $hidden_client_id_jwt .= "******";
                            }
                            $hidden_client_id_jwt .= substr($client_id_jwt, -5);
                            ?>
                            <input type="text" name="bcp_client_id_jwt" value="" <?php
                            if (empty($client_id_jwt)) {
                                echo $required_jwt_bearer_flow;
                            }
                            ?> >
                                <?php if (!empty($client_id_jwt)) { ?>
                                <p class="description"><?php echo $hidden_client_id_jwt; ?></p>
                            <?php } ?>
                            <input type="hidden" class="bcp_client_id_jwt_hidden" value="<?php echo $client_id_jwt;?>">
                        </td>
                    </tr>
                    <!-- TR End -->
                  
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Username', 'bcp_portal'); ?> <?php
                                if (empty($username_jwt)) {
                                    echo "<sup>*</sup>";
                                }
                                ?></label>
                        </th>
                        <td>
                            <?php
                            $hidden_username_jwt = "";
                            if (!empty($username_jwt)) {
                                $hidden_username_jwt = substr($username_jwt, 0, 5);
                                $hidden_username_jwt .= "******";
                            }
                            ?>
                            <input type="text" name="bcp_username_jwt" value="" <?php
                            if (empty($username_jwt)) {
                                echo $required_jwt_bearer_flow;
                            }
                            ?> >
                                <?php if (!empty($username_jwt)) { ?>
                                <p class="description"><?php echo $hidden_username_jwt; ?></p>
                            <?php } ?>
                            <input type="hidden" class="bcp_username_jwt_hidden" value="<?php echo $username_jwt;?>">
                        </td>
                    </tr>
                    <!-- TR End -->
                  
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Private Key', 'bcp_portal'); ?> <?php
                                if (empty($bcp_private_key)) {
                                    echo "<sup>*</sup>";
                                }
                                ?>
                            </label>
                        </th>
                        <td>
                           
                            <textarea type="text" name="bcp_private_key" value="<?php echo $bcp_private_key;?>" rows="5" cols="30" autocomplete="off" <?php
                            //if (empty($bcp_private_key)) {
                                echo $required_jwt_bearer_flow;
                            //}
                            ?>><?php echo $bcp_private_key;?></textarea>
                              
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr>
                        <th scope="row">
                            <label><?php _e('Public Key', 'bcp_portal'); ?> <?php
                                if (empty($bcp_public_key)) {
                                    echo "<sup>*</sup>";
                                }
                                ?>
                            </label>
                        </th>
                        <td>            
                            <textarea type="text" name="bcp_public_key" value="<?php echo $bcp_public_key;?>" rows="5" cols="30" autocomplete="off" <?php
                           // if (empty($bcp_public_key)) {
                                echo $required_jwt_bearer_flow;
                           // }
                            ?> ><?php echo $bcp_public_key;?></textarea>
                                
                        </td>
                    </tr>
                    <!-- TR End -->
                    <!-- TR Start -->
                    <tr class="hide_class">
                        <th scope="row"><label for="bcp_operation_mode"><?php _e('Operation mode', 'bcp_portal'); ?></label></th>
                        <td>
                            <?php
                            ( fetch_data_option('bcp_operation_mode') === FALSE ? update_option('bcp_operation_mode', 'live') : '' ); // If operation mode is not set than set it to live
                            $operation_mode = fetch_data_option('bcp_operation_mode');
                            ?>
                            <select name="bcp_operation_mode" id="bcp_operation_mode_jwt">
                            <option value="testing" <?php echo $operation_mode == 'testing' ? 'selected' : ''; ?>><?php _e('Sandbox', 'bcp_portal'); ?></option>
                            <option value="live" <?php echo $operation_mode == 'live' ? 'selected' : ''; ?>><?php _e('Live', 'bcp_portal'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <!-- TR End -->
                </tbody>
            </table>
            <!-- form-table End -->
 <?php } ?>
