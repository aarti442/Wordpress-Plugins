<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.crmjetty.com
 * @since      1.0.0
 *
 * @package    Customer_Portal_Salesforce_V2
 * @subpackage Customer_Portal_Salesforce_V2/admin
 */
class BCP_Default_Content {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Login page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateLoginPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Login', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetLoginPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_login');
        update_option('bcp_login_page', $id);
        return $id;
    }

    /**
     * Login page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetLoginPageContent() {
        return '<!-- wp:columns -->
                            <div class="wp-block-columns"><!-- wp:column -->
                            <div class="wp-block-column"><!-- wp:html -->
                            <div style="color: white;">
                            <h2 style="color: white;">Customer Portal</h2>
                            <p>Portal embeds a customer relationship intelligence to your organization.</p>
                            </div>
                            <!-- /wp:html -->

                            <!-- wp:paragraph -->
                            <p></p>
                            <!-- /wp:paragraph --></div>
                            <!-- /wp:column -->

                            <!-- wp:column -->
                            <div class="wp-block-column"><!-- wp:sfcp/portal-login /--></div>
                            <!-- /wp:column --></div>
                            <!-- /wp:columns -->';
    }

    /**
     * Registration page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateRegistrationPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Registration', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetRegistrationPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_register');
        update_option('bcp_registration_page', $id);
        return $id;
    }

    /**
     * Registration page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetRegistrationPageContent() {
        return '<!-- wp:sfcp/portal-registration /-->';
    }

    /**
     * Profile page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateProfilePage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Profile', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetProfilePageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_profile');
        update_option('bcp_profile_page', $id);
        return $id;
    }

    /**
     * Profile page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetProfilePageContent() {
        return '<!-- wp:sfcp/portal-profile-block /-->';
    }

    /**
     * ForgotPassword page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateForgotPasswordPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Forgot Password', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetForgotPasswordPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_forget_password');
        update_option('bcp_forgot_password_page', $id);
        return $id;
    }

    /**
     * ForgotPassword page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetForgotPasswordPageContent() {
        return '<!-- wp:sfcp/portal-forgot-password /-->';
    }

    /**
     * Resetpage page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateResetPageContent() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Reset Password', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetResetPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_reset_password');
        update_option('bcp_reset_password_page', $id);
        return $id;
    }

    /**
     * Resetpage page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetResetPageContent() {
        return '<!-- wp:sfcp/portal-reset-password /-->';
    }

    /**
     * Manage page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateManagePage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Manage', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetManagePageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_option('bcp_manage_page', $id);
    }

    /**
     * Manage page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetManagePageContent() {
        return '<!-- wp:sfcp/portal-content-block /-->';
    }

    /**
     * List page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateListPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal List Page', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetListPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_list');
        update_post_meta($id, '_bcp_modules', 'All');
        update_option('bcp_list_page', $id);
        return $id;
    }

    /**
     * List page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetListPageContent() {
        return '<!-- wp:sfcp/portal-list-block /-->';
    }

    /**
     * Detail page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateDetailPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Detail Page', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => '<!-- wp:sfcp/portal-detail-block /-->',
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_detail');
        update_post_meta($id, '_bcp_modules', 'All');
        update_option('bcp_detail_page', $id);
        return $id;
    }

    /**
     * Detail page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetDetailPageContent() {
        return '<!-- wp:sfcp/portal-detail-block /-->';
    }

    /**
     * Dashboard page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateDashboardPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Dashboard Page', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetDashboardPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_option('bcp_dashboard_page', $id);
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_dashboard');
        update_post_meta($id, 'sfcp_page_counter_data', maybe_unserialize('a:4:{s:20:"counter_block_id_204";a:3:{s:11:"module_name";s:7:"account";s:5:"field";s:3:"All";s:11:"field_value";s:3:"All";}s:20:"counter_block_id_616";a:3:{s:11:"module_name";s:5:"order";s:5:"field";s:6:"Status";s:11:"field_value";s:5:"Draft";}s:20:"counter_block_id_524";a:3:{s:11:"module_name";s:4:"case";s:5:"field";s:6:"Status";s:11:"field_value";s:3:"New";}s:20:"counter_block_id_649";a:3:{s:11:"module_name";s:4:"call";s:5:"field";s:6:"Status";s:11:"field_value";s:9:"Completed";}}'));
        update_post_meta($id, 'sfcp_page_chart_data', maybe_unserialize('a:2:{s:14:"chart_block_24";a:2:{s:11:"module_name";s:4:"case";s:5:"field";s:4:"Type";}s:15:"chart_block_757";a:2:{s:11:"module_name";s:5:"order";s:5:"field";s:6:"Status";}}'));
        update_post_meta($id, 'sfcp_page_activity_data', maybe_unserialize('a:6:{s:18:"activity_block_162";a:4:{s:11:"module_name";s:7:"account";s:22:"field1_selected_option";s:4:"Name";s:22:"field2_selected_option";s:4:"Type";s:22:"field3_selected_option";s:11:"CreatedDate";}s:18:"activity_block_868";a:4:{s:11:"module_name";s:4:"case";s:22:"field1_selected_option";s:7:"Subject";s:22:"field2_selected_option";s:6:"Status";s:22:"field3_selected_option";s:11:"CreatedDate";}s:18:"activity_block_109";a:4:{s:11:"module_name";s:8:"contract";s:22:"field1_selected_option";s:14:"ContractNumber";s:22:"field2_selected_option";s:6:"Status";s:22:"field3_selected_option";s:9:"StartDate";}s:18:"activity_block_358";a:4:{s:11:"module_name";s:5:"order";s:22:"field1_selected_option";s:11:"OrderNumber";s:22:"field2_selected_option";s:6:"Status";s:22:"field3_selected_option";s:13:"EffectiveDate";}s:18:"activity_block_999";a:4:{s:11:"module_name";s:15:"contentdocument";s:22:"field1_selected_option";s:5:"Title";s:22:"field2_selected_option";s:13:"PublishStatus";s:22:"field3_selected_option";s:11:"CreatedDate";}s:17:"activity_block_43";a:4:{s:11:"module_name";s:4:"call";s:22:"field1_selected_option";s:7:"Subject";s:22:"field2_selected_option";s:6:"Status";s:22:"field3_selected_option";s:11:"CreatedDate";}}'));
        return $id;
    }

    /**
     * Dashboard page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetDashboardPageContent() {
        $detail_content = '
            <!-- wp:sfcp/portal-search-block /-->

            <!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-counter-block {"module_fields":[{"label":"All","value":"All"},{"label":"Account Type","value":"Type"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy"},{"label":"Industry","value":"Industry"},{"label":"Ownership","value":"Ownership"},{"label":"Account Rating","value":"Rating"},{"label":"Clean Status","value":"CleanStatus"},{"label":"Account Source","value":"AccountSource"},{"label":"Customer Priority","value":"biztechcs__CustomerPriority__c"},{"label":"SLA","value":"biztechcs__SLA__c"},{"label":"Active","value":"biztechcs__Active__c"},{"label":"Upsell Opportunity","value":"biztechcs__UpsellOpportunity__c"}],"selected_field":"All","module_title":"Numbers of Account","field_options_loaded":true,"module_dropdown_data":{"All":[{"label":"All","value":"All"}],"Type":[{"label":"Prospect","value":"Prospect"},{"label":"Customer - Direct","value":"Customer - Direct"},{"label":"Customer - Channel","value":"Customer - Channel"},{"label":"Channel Partner / Reseller","value":"Channel Partner / Reseller"},{"label":"Installation Partner","value":"Installation Partner"},{"label":"Technology Partner","value":"Technology Partner"},{"label":"Other","value":"Other"}],"BillingGeocodeAccuracy":[{"label":"Address","value":"Address"},{"label":"NearAddress","value":"NearAddress"},{"label":"Block","value":"Block"},{"label":"Street","value":"Street"},{"label":"ExtendedZip","value":"ExtendedZip"},{"label":"Zip","value":"Zip"},{"label":"Neighborhood","value":"Neighborhood"},{"label":"City","value":"City"},{"label":"County","value":"County"},{"label":"State","value":"State"},{"label":"Unknown","value":"Unknown"}],"ShippingGeocodeAccuracy":[{"label":"Address","value":"Address"},{"label":"NearAddress","value":"NearAddress"},{"label":"Block","value":"Block"},{"label":"Street","value":"Street"},{"label":"ExtendedZip","value":"ExtendedZip"},{"label":"Zip","value":"Zip"},{"label":"Neighborhood","value":"Neighborhood"},{"label":"City","value":"City"},{"label":"County","value":"County"},{"label":"State","value":"State"},{"label":"Unknown","value":"Unknown"}],"Industry":[{"label":"Agriculture","value":"Agriculture"},{"label":"Apparel","value":"Apparel"},{"label":"Banking","value":"Banking"},{"label":"Biotechnology","value":"Biotechnology"},{"label":"Chemicals","value":"Chemicals"},{"label":"Communications","value":"Communications"},{"label":"Construction","value":"Construction"},{"label":"Consulting","value":"Consulting"},{"label":"Education","value":"Education"},{"label":"Electronics","value":"Electronics"},{"label":"Energy","value":"Energy"},{"label":"Engineering","value":"Engineering"},{"label":"Entertainment","value":"Entertainment"},{"label":"Environmental","value":"Environmental"},{"label":"Finance","value":"Finance"},{"label":"Food \u0026 Beverage","value":"Food \u0026 Beverage"},{"label":"Government","value":"Government"},{"label":"Healthcare","value":"Healthcare"},{"label":"Hospitality","value":"Hospitality"},{"label":"Insurance","value":"Insurance"},{"label":"Machinery","value":"Machinery"},{"label":"Manufacturing","value":"Manufacturing"},{"label":"Media","value":"Media"},{"label":"Not For Profit","value":"Not For Profit"},{"label":"Recreation","value":"Recreation"},{"label":"Retail","value":"Retail"},{"label":"Shipping","value":"Shipping"},{"label":"Technology","value":"Technology"},{"label":"Telecommunications","value":"Telecommunications"},{"label":"Transportation","value":"Transportation"},{"label":"Utilities","value":"Utilities"},{"label":"Other","value":"Other"}],"Ownership":[{"label":"Public","value":"Public"},{"label":"Private","value":"Private"},{"label":"Subsidiary","value":"Subsidiary"},{"label":"Other","value":"Other"}],"Rating":[{"label":"Hot","value":"Hot"},{"label":"Warm","value":"Warm"},{"label":"Cold","value":"Cold"}],"CleanStatus":[{"label":"In Sync","value":"Matched"},{"label":"Different","value":"Different"},{"label":"Reviewed","value":"Acknowledged"},{"label":"Not Found","value":"NotFound"},{"label":"Inactive","value":"Inactive"},{"label":"Not Compared","value":"Pending"},{"label":"Select Match","value":"SelectMatch"},{"label":"Skipped","value":"Skipped"}],"AccountSource":[{"label":"Web","value":"Web"},{"label":"Phone Inquiry","value":"Phone Inquiry"},{"label":"Partner Referral","value":"Partner Referral"},{"label":"Purchased List","value":"Purchased List"},{"label":"Other","value":"Other"}],"biztechcs__CustomerPriority__c":[{"label":"High","value":"High"},{"label":"Low","value":"Low"},{"label":"Medium","value":"Medium"}],"biztechcs__SLA__c":[{"label":"Gold","value":"Gold"},{"label":"Silver","value":"Silver"},{"label":"Platinum","value":"Platinum"},{"label":"Bronze","value":"Bronze"}],"biztechcs__Active__c":[{"label":"No","value":"No"},{"label":"Yes","value":"Yes"}],"biztechcs__UpsellOpportunity__c":[{"label":"Maybe","value":"Maybe"},{"label":"No","value":"No"},{"label":"Yes","value":"Yes"}]},"selected_field_value":"All","selected_icon":"lnr lnr-user","random_number":"icon_picker_845","counter_block_id":"counter_block_id_204","block_color":"#ffffff","font_color":"#445eff","block_hover_color":"#e5e9ff","font_hover_color":"#445eff"} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-counter-block {"selected_module":"order","module_fields":[{"label":"All","value":"All"},{"label":"Status","value":"Status"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy"},{"label":"Status Category","value":"StatusCode"}],"selected_field":"Status","module_title":"Numbers of Orders","field_options_loaded":true,"module_dropdown_data":{"All":[{"label":"All","value":"All"}],"Status":[{"label":"Draft","value":"Draft"},{"label":"Activated","value":"Activated"}],"BillingGeocodeAccuracy":[{"label":"Address","value":"Address"},{"label":"NearAddress","value":"NearAddress"},{"label":"Block","value":"Block"},{"label":"Street","value":"Street"},{"label":"ExtendedZip","value":"ExtendedZip"},{"label":"Zip","value":"Zip"},{"label":"Neighborhood","value":"Neighborhood"},{"label":"City","value":"City"},{"label":"County","value":"County"},{"label":"State","value":"State"},{"label":"Unknown","value":"Unknown"}],"ShippingGeocodeAccuracy":[{"label":"Address","value":"Address"},{"label":"NearAddress","value":"NearAddress"},{"label":"Block","value":"Block"},{"label":"Street","value":"Street"},{"label":"ExtendedZip","value":"ExtendedZip"},{"label":"Zip","value":"Zip"},{"label":"Neighborhood","value":"Neighborhood"},{"label":"City","value":"City"},{"label":"County","value":"County"},{"label":"State","value":"State"},{"label":"Unknown","value":"Unknown"}],"StatusCode":[{"label":"Draft","value":"Draft"},{"label":"Activated","value":"Activated"},{"label":"Cancelled","value":"Canceled"},{"label":"Expired","value":"Expired"}]},"selected_field_value":"Draft","selected_icon":"lnr lnr-file-empty","random_number":"icon_picker_753","counter_block_id":"counter_block_id_616","block_color":"#ffffff","font_color":"#00a8ff","block_hover_color":"#e5f7ff","font_hover_color":"#00a8ff"} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-counter-block {"selected_module":"case","module_fields":[{"label":"All","value":"All"},{"label":"Case Type","value":"Type"},{"label":"Status","value":"Status"},{"label":"Case Reason","value":"Reason"},{"label":"Case Origin","value":"Origin"},{"label":"Priority","value":"Priority"},{"label":"SLA Violation","value":"biztechcs__SLAViolation__c"},{"label":"Product","value":"biztechcs__Product__c"},{"label":"Potential Liability","value":"biztechcs__PotentialLiability__c"},{"label":"Source From","value":"biztechcs__Source_From__c"},{"label":"Modified From","value":"biztechcs__Modified_From__c"},{"label":"testmultipicklist","value":"biztechcs__testmultipicklist__c"}],"selected_field":"Status","module_title":"Numbers of Cases","field_options_loaded":true,"module_dropdown_data":{"All":[{"label":"All","value":"All"}],"Type":[{"label":"Mechanical","value":"Mechanical"},{"label":"Electrical","value":"Electrical"},{"label":"Electronic","value":"Electronic"},{"label":"Structural","value":"Structural"},{"label":"Other","value":"Other"}],"Status":[{"label":"New","value":"New"},{"label":"Working","value":"Working"},{"label":"Escalated","value":"Escalated"},{"label":"Closed","value":"Closed"}],"Reason":[{"label":"Installation","value":"Installation"},{"label":"Equipment Complexity","value":"Equipment Complexity"},{"label":"Performance","value":"Performance"},{"label":"Breakdown","value":"Breakdown"},{"label":"Equipment Design","value":"Equipment Design"},{"label":"Feedback","value":"Feedback"},{"label":"Other","value":"Other"}],"Origin":[{"label":"Phone","value":"Phone"},{"label":"Email","value":"Email"},{"label":"Web","value":"Web"},{"label":"Portal","value":"Portal"}],"Priority":[{"label":"High","value":"High"},{"label":"Medium","value":"Medium"},{"label":"Low","value":"Low"}],"biztechcs__SLAViolation__c":[{"label":"No","value":"No"},{"label":"Yes","value":"Yes"}],"biztechcs__Product__c":[{"label":"GC1040","value":"GC1040"},{"label":"GC1060","value":"GC1060"},{"label":"GC3020","value":"GC3020"},{"label":"GC3040","value":"GC3040"},{"label":"GC3060","value":"GC3060"},{"label":"GC5020","value":"GC5020"},{"label":"GC5040","value":"GC5040"},{"label":"GC5060","value":"GC5060"},{"label":"GC1020","value":"GC1020"}],"biztechcs__PotentialLiability__c":[{"label":"No","value":"No"},{"label":"Yes","value":"Yes"}],"biztechcs__Source_From__c":[{"label":"Salesforce","value":"Salesforce"},{"label":"Portal","value":"Portal"}],"biztechcs__Modified_From__c":[{"label":"Salesforce","value":"Salesforce"},{"label":"Portal","value":"Portal"}],"biztechcs__testmultipicklist__c":[{"label":"Test1","value":"Test1"},{"label":"Test2","value":"Test2"},{"label":"Test3","value":"Test3"}]},"selected_field_value":"New","selected_icon":"lnr lnr-book","random_number":"icon_picker_419","counter_block_id":"counter_block_id_524","block_color":"#ffffff","font_color":"#e2770f","block_hover_color":"#fff2e5","font_hover_color":"#e2770f"} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-counter-block {"selected_module":"task","module_fields":[{"label":"All","value":"All"},{"label":"Status","value":"Status"},{"label":"Priority","value":"Priority"},{"label":"Call Type","value":"CallType"},{"label":"Recurrence Time Zone","value":"RecurrenceTimeZoneSidKey"},{"label":"Recurrence Type","value":"RecurrenceType"},{"label":"Recurrence Instance","value":"RecurrenceInstance"},{"label":"Recurrence Month of Year","value":"RecurrenceMonthOfYear"},{"label":"Repeat This Task","value":"RecurrenceRegeneratedType"},{"label":"Task Subtype","value":"TaskSubtype"}],"selected_field":"Status","module_title":"Numbers of Calls","field_options_loaded":true,"module_dropdown_data":{"All":[{"label":"All","value":"All"}],"Status":[{"label":"Not Started","value":"Not Started"},{"label":"In Progress","value":"In Progress"},{"label":"Completed","value":"Completed"},{"label":"Waiting on someone else","value":"Waiting on someone else"},{"label":"Deferred","value":"Deferred"}],"Priority":[{"label":"High","value":"High"},{"label":"Normal","value":"Normal"},{"label":"Low","value":"Low"}],"CallType":[{"label":"Internal","value":"Internal"},{"label":"Inbound","value":"Inbound"},{"label":"Outbound","value":"Outbound"}],"RecurrenceTimeZoneSidKey":[{"label":"(GMT+14:00) Line Islands Time (Pacific/Kiritimati)","value":"Pacific/Kiritimati"},{"label":"(GMT+13:00) Phoenix Islands Time (Pacific/Enderbury)","value":"Pacific/Enderbury"},{"label":"(GMT+13:00) Tonga Standard Time (Pacific/Tongatapu)","value":"Pacific/Tongatapu"},{"label":"(GMT+12:45) Chatham Standard Time (Pacific/Chatham)","value":"Pacific/Chatham"},{"label":"(GMT+12:00) Petropavlovsk-Kamchatski Standard Time (Asia/Kamchatka)","value":"Asia/Kamchatka"},{"label":"(GMT+12:00) New Zealand Standard Time (Pacific/Auckland)","value":"Pacific/Auckland"},{"label":"(GMT+12:00) Fiji Standard Time (Pacific/Fiji)","value":"Pacific/Fiji"},{"label":"(GMT+11:00) Solomon Islands Time (Pacific/Guadalcanal)","value":"Pacific/Guadalcanal"},{"label":"(GMT+11:00) Norfolk Island Time (Pacific/Norfolk)","value":"Pacific/Norfolk"},{"label":"(GMT+10:30) Lord Howe Standard Time (Australia/Lord_Howe)","value":"Australia/Lord_Howe"},{"label":"(GMT+10:00) Australian Eastern Standard Time (Australia/Brisbane)","value":"Australia/Brisbane"},{"label":"(GMT+10:00) Australian Eastern Standard Time (Australia/Sydney)","value":"Australia/Sydney"},{"label":"(GMT+09:30) Australian Central Standard Time (Australia/Adelaide)","value":"Australia/Adelaide"},{"label":"(GMT+09:30) Australian Central Standard Time (Australia/Darwin)","value":"Australia/Darwin"},{"label":"(GMT+09:00) Korean Standard Time (Asia/Seoul)","value":"Asia/Seoul"},{"label":"(GMT+09:00) Japan Standard Time (Asia/Tokyo)","value":"Asia/Tokyo"},{"label":"(GMT+08:00) Hong Kong Standard Time (Asia/Hong_Kong)","value":"Asia/Hong_Kong"},{"label":"(GMT+08:00) Malaysia Time (Asia/Kuala_Lumpur)","value":"Asia/Kuala_Lumpur"},{"label":"(GMT+08:00) Philippine Standard Time (Asia/Manila)","value":"Asia/Manila"},{"label":"(GMT+08:00) China Standard Time (Asia/Shanghai)","value":"Asia/Shanghai"},{"label":"(GMT+08:00) Singapore Standard Time (Asia/Singapore)","value":"Asia/Singapore"},{"label":"(GMT+08:00) Taipei Standard Time (Asia/Taipei)","value":"Asia/Taipei"},{"label":"(GMT+08:00) Australian Western Standard Time (Australia/Perth)","value":"Australia/Perth"},{"label":"(GMT+07:00) Indochina Time (Asia/Bangkok)","value":"Asia/Bangkok"},{"label":"(GMT+07:00) Indochina Time (Asia/Ho_Chi_Minh)","value":"Asia/Ho_Chi_Minh"},{"label":"(GMT+07:00) Western Indonesia Time (Asia/Jakarta)","value":"Asia/Jakarta"},{"label":"(GMT+06:30) Myanmar Time (Asia/Rangoon)","value":"Asia/Rangoon"},{"label":"(GMT+06:00) Bangladesh Standard Time (Asia/Dhaka)","value":"Asia/Dhaka"},{"label":"(GMT+05:45) Nepal Time (Asia/Kathmandu)","value":"Asia/Kathmandu"},{"label":"(GMT+05:30) India Standard Time (Asia/Colombo)","value":"Asia/Colombo"},{"label":"(GMT+05:30) India Standard Time (Asia/Kolkata)","value":"Asia/Kolkata"},{"label":"(GMT+05:00) Pakistan Standard Time (Asia/Karachi)","value":"Asia/Karachi"},{"label":"(GMT+05:00) Uzbekistan Standard Time (Asia/Tashkent)","value":"Asia/Tashkent"},{"label":"(GMT+05:00) Yekaterinburg Standard Time (Asia/Yekaterinburg)","value":"Asia/Yekaterinburg"},{"label":"(GMT+04:30) Afghanistan Time (Asia/Kabul)","value":"Asia/Kabul"},{"label":"(GMT+04:30) Iran Daylight Time (Asia/Tehran)","value":"Asia/Tehran"},{"label":"(GMT+04:00) Azerbaijan Standard Time (Asia/Baku)","value":"Asia/Baku"},{"label":"(GMT+04:00) Gulf Standard Time (Asia/Dubai)","value":"Asia/Dubai"},{"label":"(GMT+04:00) Georgia Standard Time (Asia/Tbilisi)","value":"Asia/Tbilisi"},{"label":"(GMT+04:00) Armenia Standard Time (Asia/Yerevan)","value":"Asia/Yerevan"},{"label":"(GMT+03:00) East Africa Time (Africa/Nairobi)","value":"Africa/Nairobi"},{"label":"(GMT+03:00) Arabian Standard Time (Asia/Baghdad)","value":"Asia/Baghdad"},{"label":"(GMT+03:00) Eastern European Summer Time (Asia/Beirut)","value":"Asia/Beirut"},{"label":"(GMT+03:00) Israel Daylight Time (Asia/Jerusalem)","value":"Asia/Jerusalem"},{"label":"(GMT+03:00) Arabian Standard Time (Asia/Kuwait)","value":"Asia/Kuwait"},{"label":"(GMT+03:00) Arabian Standard Time (Asia/Riyadh)","value":"Asia/Riyadh"},{"label":"(GMT+03:00) Eastern European Summer Time (Europe/Athens)","value":"Europe/Athens"},{"label":"(GMT+03:00) Eastern European Summer Time (Europe/Bucharest)","value":"Europe/Bucharest"},{"label":"(GMT+03:00) Eastern European Summer Time (Europe/Helsinki)","value":"Europe/Helsinki"},{"label":"(GMT+03:00) Europe/Istanbul","value":"Europe/Istanbul"},{"label":"(GMT+03:00) Moscow Standard Time (Europe/Minsk)","value":"Europe/Minsk"},{"label":"(GMT+03:00) Moscow Standard Time (Europe/Moscow)","value":"Europe/Moscow"},{"label":"(GMT+02:00) Eastern European Standard Time (Africa/Cairo)","value":"Africa/Cairo"},{"label":"(GMT+02:00) South Africa Standard Time (Africa/Johannesburg)","value":"Africa/Johannesburg"},{"label":"(GMT+02:00) Central European Summer Time (Europe/Amsterdam)","value":"Europe/Amsterdam"},{"label":"(GMT+02:00) Central European Summer Time (Europe/Berlin)","value":"Europe/Berlin"},{"label":"(GMT+02:00) Central European Summer Time (Europe/Brussels)","value":"Europe/Brussels"},{"label":"(GMT+02:00) Central European Summer Time (Europe/Paris)","value":"Europe/Paris"},{"label":"(GMT+02:00) Central European Summer Time (Europe/Prague)","value":"Europe/Prague"},{"label":"(GMT+02:00) Central European Summer Time (Europe/Rome)","value":"Europe/Rome"},{"label":"(GMT+01:00) Central European Standard Time (Africa/Algiers)","value":"Africa/Algiers"},{"label":"(GMT+01:00) Africa/Casablanca","value":"Africa/Casablanca"},{"label":"(GMT+01:00) Irish Standard Time (Europe/Dublin)","value":"Europe/Dublin"},{"label":"(GMT+01:00) Western European Summer Time (Europe/Lisbon)","value":"Europe/Lisbon"},{"label":"(GMT+01:00) British Summer Time (Europe/London)","value":"Europe/London"},{"label":"(GMT+00:00) East Greenland Summer Time (America/Scoresbysund)","value":"America/Scoresbysund"},{"label":"(GMT+00:00) Azores Summer Time (Atlantic/Azores)","value":"Atlantic/Azores"},{"label":"(GMT+00:00) Greenwich Mean Time (GMT)","value":"GMT"},{"label":"(GMT-01:00) Cape Verde Standard Time (Atlantic/Cape_Verde)","value":"Atlantic/Cape_Verde"},{"label":"(GMT-02:00) South Georgia Time (Atlantic/South_Georgia)","value":"Atlantic/South_Georgia"},{"label":"(GMT-02:30) Newfoundland Daylight Time (America/St_Johns)","value":"America/St_Johns"},{"label":"(GMT-03:00) Argentina Standard Time (America/Argentina/Buenos_Aires)","value":"America/Argentina/Buenos_Aires"},{"label":"(GMT-03:00) Atlantic Daylight Time (America/Halifax)","value":"America/Halifax"},{"label":"(GMT-03:00) Brasilia Standard Time (America/Sao_Paulo)","value":"America/Sao_Paulo"},{"label":"(GMT-03:00) Atlantic Daylight Time (Atlantic/Bermuda)","value":"Atlantic/Bermuda"},{"label":"(GMT-04:00) Venezuela Time (America/Caracas)","value":"America/Caracas"},{"label":"(GMT-04:00) Eastern Daylight Time (America/Indiana/Indianapolis)","value":"America/Indiana/Indianapolis"},{"label":"(GMT-04:00) Eastern Daylight Time (America/New_York)","value":"America/New_York"},{"label":"(GMT-04:00) Atlantic Standard Time (America/Puerto_Rico)","value":"America/Puerto_Rico"},{"label":"(GMT-04:00) Chile Standard Time (America/Santiago)","value":"America/Santiago"},{"label":"(GMT-05:00) Colombia Standard Time (America/Bogota)","value":"America/Bogota"},{"label":"(GMT-05:00) Central Daylight Time (America/Chicago)","value":"America/Chicago"},{"label":"(GMT-05:00) Peru Standard Time (America/Lima)","value":"America/Lima"},{"label":"(GMT-05:00) Central Daylight Time (America/Mexico_City)","value":"America/Mexico_City"},{"label":"(GMT-05:00) Eastern Standard Time (America/Panama)","value":"America/Panama"},{"label":"(GMT-06:00) Mountain Daylight Time (America/Denver)","value":"America/Denver"},{"label":"(GMT-06:00) Central Standard Time (America/El_Salvador)","value":"America/El_Salvador"},{"label":"(GMT-06:00) Mexican Pacific Daylight Time (America/Mazatlan)","value":"America/Mazatlan"},{"label":"(GMT-07:00) Pacific Daylight Time (America/Los_Angeles)","value":"America/Los_Angeles"},{"label":"(GMT-07:00) Mountain Standard Time (America/Phoenix)","value":"America/Phoenix"},{"label":"(GMT-07:00) Pacific Daylight Time (America/Tijuana)","value":"America/Tijuana"},{"label":"(GMT-08:00) Alaska Daylight Time (America/Anchorage)","value":"America/Anchorage"},{"label":"(GMT-08:00) Pitcairn Time (Pacific/Pitcairn)","value":"Pacific/Pitcairn"},{"label":"(GMT-09:00) Hawaii-Aleutian Daylight Time (America/Adak)","value":"America/Adak"},{"label":"(GMT-09:00) Gambier Time (Pacific/Gambier)","value":"Pacific/Gambier"},{"label":"(GMT-09:30) Marquesas Time (Pacific/Marquesas)","value":"Pacific/Marquesas"},{"label":"(GMT-10:00) Hawaii-Aleutian Standard Time (Pacific/Honolulu)","value":"Pacific/Honolulu"},{"label":"(GMT-11:00) Niue Time (Pacific/Niue)","value":"Pacific/Niue"},{"label":"(GMT-11:00) Samoa Standard Time (Pacific/Pago_Pago)","value":"Pacific/Pago_Pago"}],"RecurrenceType":[{"label":"Recurs Daily","value":"RecursDaily"},{"label":"Recurs Every Weekday","value":"RecursEveryWeekday"},{"label":"Recurs Monthly","value":"RecursMonthly"},{"label":"Recurs Monthy Nth","value":"RecursMonthlyNth"},{"label":"Recurs Weekly","value":"RecursWeekly"},{"label":"Recurs Yearly","value":"RecursYearly"},{"label":"Recurs Yearly Nth","value":"RecursYearlyNth"}],"RecurrenceInstance":[{"label":"1st","value":"First"},{"label":"2nd","value":"Second"},{"label":"3rd","value":"Third"},{"label":"4th","value":"Fourth"},{"label":"last","value":"Last"}],"RecurrenceMonthOfYear":[{"label":"January","value":"January"},{"label":"February","value":"February"},{"label":"March","value":"March"},{"label":"April","value":"April"},{"label":"May","value":"May"},{"label":"June","value":"June"},{"label":"July","value":"July"},{"label":"August","value":"August"},{"label":"September","value":"September"},{"label":"October","value":"October"},{"label":"November","value":"November"},{"label":"December","value":"December"}],"RecurrenceRegeneratedType":[{"label":"After due date","value":"RecurrenceRegenerateAfterDueDate"},{"label":"After date completed","value":"RecurrenceRegenerateAfterToday"},{"label":"(Task Closed)","value":"RecurrenceRegenerated"}],"TaskSubtype":[{"label":"Task","value":"Task"},{"label":"Email","value":"Email"},{"label":"List Email","value":"ListEmail"},{"label":"Cadence","value":"Cadence"},{"label":"Call","value":"Call"}]},"selected_field_value":"Completed","selected_icon":"lnr lnr-file-add","random_number":"icon_picker_896","counter_block_id":"counter_block_id_649","block_color":"#ffffff","font_color":"#00a63b","block_hover_color":"#e5ffee","font_hover_color":"#00a63b"} /--></div>
            <!-- /wp:column --></div>
            <!-- /wp:columns -->

            <!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/chart-block {"chart_block_id":"chart_block_24","selected_module":"case","view_style":"style_4","module_fields":[{"label":"Case Type","value":"Type"},{"label":"Status","value":"Status"},{"label":"Case Reason","value":"Reason"},{"label":"Case Origin","value":"Origin"},{"label":"Priority","value":"Priority"},{"label":"SLA Violation","value":"biztechcs__SLAViolation__c"},{"label":"Product","value":"biztechcs__Product__c"},{"label":"Potential Liability","value":"biztechcs__PotentialLiability__c"},{"label":"Source From","value":"biztechcs__Source_From__c"},{"label":"Modified From","value":"biztechcs__Modified_From__c"},{"label":"testmultipicklist","value":"biztechcs__testmultipicklist__c"}],"selected_field":"Type","field_options_loaded":true,"legend_enable":true} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/chart-block {"chart_block_id":"chart_block_757","selected_module":"order","view_style":"style_4","module_fields":[{"label":"Status","value":"Status"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy"},{"label":"Status Category","value":"StatusCode"}],"selected_field":"Status","field_options_loaded":true,"legend_enable":true} /--></div>
            <!-- /wp:column --></div>
            <!-- /wp:columns -->

            <!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-recent-activity {"activity_block_id":"activity_block_162","module_title":"Recent Account","field_options_loaded":true,"field1_options":[{"label":"Account ID","value":"Id","type":"id"},{"label":"Deleted","value":"IsDeleted","type":"boolean"},{"label":"Master Record ID","value":"MasterRecordId","type":"reference"},{"label":"Account Name","value":"Name","type":"string"},{"label":"Account Type","value":"Type","type":"picklist"},{"label":"Parent Account ID","value":"ParentId","type":"reference"},{"label":"Billing Street","value":"BillingStreet","type":"textarea"},{"label":"Billing City","value":"BillingCity","type":"string"},{"label":"Billing State/Province","value":"BillingState","type":"string"},{"label":"Billing Zip/Postal Code","value":"BillingPostalCode","type":"string"},{"label":"Billing Country","value":"BillingCountry","type":"string"},{"label":"Billing Latitude","value":"BillingLatitude","type":"double"},{"label":"Billing Longitude","value":"BillingLongitude","type":"double"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy","type":"picklist"},{"label":"Billing Address","value":"BillingAddress","type":"address"},{"label":"Shipping Street","value":"ShippingStreet","type":"textarea"},{"label":"Shipping City","value":"ShippingCity","type":"string"},{"label":"Shipping State/Province","value":"ShippingState","type":"string"},{"label":"Shipping Zip/Postal Code","value":"ShippingPostalCode","type":"string"},{"label":"Shipping Country","value":"ShippingCountry","type":"string"},{"label":"Shipping Latitude","value":"ShippingLatitude","type":"double"},{"label":"Shipping Longitude","value":"ShippingLongitude","type":"double"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy","type":"picklist"},{"label":"Shipping Address","value":"ShippingAddress","type":"address"},{"label":"Account Phone","value":"Phone","type":"phone"},{"label":"Account Fax","value":"Fax","type":"phone"},{"label":"Account Number","value":"AccountNumber","type":"string"},{"label":"Website","value":"Website","type":"url"},{"label":"Photo URL","value":"PhotoUrl","type":"url"},{"label":"SIC Code","value":"Sic","type":"string"},{"label":"Industry","value":"Industry","type":"picklist"},{"label":"Annual Revenue","value":"AnnualRevenue","type":"currency"},{"label":"Employees","value":"NumberOfEmployees","type":"int"},{"label":"Ownership","value":"Ownership","type":"picklist"},{"label":"Ticker Symbol","value":"TickerSymbol","type":"string"},{"label":"Account Description","value":"Description","type":"textarea"},{"label":"Account Rating","value":"Rating","type":"picklist"},{"label":"Account Site","value":"Site","type":"string"},{"label":"Owner ID","value":"OwnerId","type":"reference"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Created By ID","value":"CreatedById","type":"reference"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Last Modified By ID","value":"LastModifiedById","type":"reference"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Activity","value":"LastActivityDate","type":"date"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"Customer Portal Account","value":"IsCustomerPortal","type":"boolean"},{"label":"Data.com Key","value":"Jigsaw","type":"string"},{"label":"Jigsaw Company ID","value":"JigsawCompanyId","type":"string"},{"label":"Clean Status","value":"CleanStatus","type":"picklist"},{"label":"Account Source","value":"AccountSource","type":"picklist"},{"label":"D-U-N-S Number","value":"DunsNumber","type":"string"},{"label":"Tradestyle","value":"Tradestyle","type":"string"},{"label":"NAICS Code","value":"NaicsCode","type":"string"},{"label":"NAICS Description","value":"NaicsDesc","type":"string"},{"label":"Year Started","value":"YearStarted","type":"string"},{"label":"SIC Description","value":"SicDesc","type":"string"},{"label":"D\u0026B Company ID","value":"DandbCompanyId","type":"reference"},{"label":"Operating Hour ID","value":"OperatingHoursId","type":"reference"},{"label":"Customer Priority","value":"biztechcs__CustomerPriority__c","type":"picklist"},{"label":"SLA","value":"biztechcs__SLA__c","type":"picklist"},{"label":"Active","value":"biztechcs__Active__c","type":"picklist"},{"label":"Number of Locations","value":"biztechcs__NumberofLocations__c","type":"double"},{"label":"Upsell Opportunity","value":"biztechcs__UpsellOpportunity__c","type":"picklist"},{"label":"SLA Serial Number","value":"biztechcs__SLASerialNumber__c","type":"string"},{"label":"SLA Expiration Date","value":"biztechcs__SLAExpirationDate__c","type":"date"},{"label":"Primary Contact","value":"biztechcs__primary_contact__c","type":"reference"}],"field2_options":[{"label":"Account Type","value":"Type","type":"picklist"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy","type":"picklist"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy","type":"picklist"},{"label":"Industry","value":"Industry","type":"picklist"},{"label":"Ownership","value":"Ownership","type":"picklist"},{"label":"Account Rating","value":"Rating","type":"picklist"},{"label":"Clean Status","value":"CleanStatus","type":"picklist"},{"label":"Account Source","value":"AccountSource","type":"picklist"},{"label":"Customer Priority","value":"biztechcs__CustomerPriority__c","type":"picklist"},{"label":"SLA","value":"biztechcs__SLA__c","type":"picklist"},{"label":"Active","value":"biztechcs__Active__c","type":"picklist"},{"label":"Upsell Opportunity","value":"biztechcs__UpsellOpportunity__c","type":"picklist"}],"field3_options":[{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Activity","value":"LastActivityDate","type":"date"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"SLA Expiration Date","value":"biztechcs__SLAExpirationDate__c","type":"date"}],"field1_selected_option":"Name","field2_selected_option":"Type","field3_selected_option":"CreatedDate","dropdown_field_color":"#00A8FF"} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-recent-activity {"activity_block_id":"activity_block_868","selected_module":"case","module_title":"Recent Case","view_style":"style_3","field_options_loaded":true,"field1_options":[{"label":"Case ID","value":"Id","type":"id"},{"label":"Deleted","value":"IsDeleted","type":"boolean"},{"label":"Master Record ID","value":"MasterRecordId","type":"reference"},{"label":"Case Number","value":"CaseNumber","type":"string"},{"label":"Contact ID","value":"ContactId","type":"reference"},{"label":"Account ID","value":"AccountId","type":"reference"},{"label":"Asset ID","value":"AssetId","type":"reference"},{"label":"Source ID","value":"SourceId","type":"reference"},{"label":"Business Hours ID","value":"BusinessHoursId","type":"reference"},{"label":"Parent Case ID","value":"ParentId","type":"reference"},{"label":"Name","value":"SuppliedName","type":"string"},{"label":"Email Address","value":"SuppliedEmail","type":"email"},{"label":"Phone","value":"SuppliedPhone","type":"string"},{"label":"Company","value":"SuppliedCompany","type":"string"},{"label":"Case Type","value":"Type","type":"picklist"},{"label":"Status","value":"Status","type":"picklist"},{"label":"Case Reason","value":"Reason","type":"picklist"},{"label":"Case Origin","value":"Origin","type":"picklist"},{"label":"Subject","value":"Subject","type":"string"},{"label":"Priority","value":"Priority","type":"picklist"},{"label":"Description","value":"Description","type":"textarea"},{"label":"Closed","value":"IsClosed","type":"boolean"},{"label":"Closed Date","value":"ClosedDate","type":"datetime"},{"label":"Escalated","value":"IsEscalated","type":"boolean"},{"label":"New Self-Service Comment","value":"HasCommentsUnreadByOwner","type":"boolean"},{"label":"Self-Service Commented","value":"HasSelfServiceComments","type":"boolean"},{"label":"Owner ID","value":"OwnerId","type":"reference"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Created By ID","value":"CreatedById","type":"reference"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Last Modified By ID","value":"LastModifiedById","type":"reference"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Contact Phone","value":"ContactPhone","type":"phone"},{"label":"Contact Mobile","value":"ContactMobile","type":"phone"},{"label":"Contact Email","value":"ContactEmail","type":"email"},{"label":"Contact Fax","value":"ContactFax","type":"phone"},{"label":"Internal Comments","value":"Comments","type":"textarea"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"Engineering Req Number","value":"biztechcs__EngineeringReqNumber__c","type":"string"},{"label":"SLA Violation","value":"biztechcs__SLAViolation__c","type":"picklist"},{"label":"Product","value":"biztechcs__Product__c","type":"picklist"},{"label":"Potential Liability","value":"biztechcs__PotentialLiability__c","type":"picklist"},{"label":"Source From","value":"biztechcs__Source_From__c","type":"picklist"},{"label":"CaseComment","value":"biztechcs__CaseComment__c","type":"textarea"},{"label":"Modified From","value":"biztechcs__Modified_From__c","type":"picklist"},{"label":"testmultipicklist","value":"biztechcs__testmultipicklist__c","type":"multipicklist"}],"field2_options":[{"label":"Case Type","value":"Type","type":"picklist"},{"label":"Status","value":"Status","type":"picklist"},{"label":"Case Reason","value":"Reason","type":"picklist"},{"label":"Case Origin","value":"Origin","type":"picklist"},{"label":"Priority","value":"Priority","type":"picklist"},{"label":"SLA Violation","value":"biztechcs__SLAViolation__c","type":"picklist"},{"label":"Product","value":"biztechcs__Product__c","type":"picklist"},{"label":"Potential Liability","value":"biztechcs__PotentialLiability__c","type":"picklist"},{"label":"Source From","value":"biztechcs__Source_From__c","type":"picklist"},{"label":"Modified From","value":"biztechcs__Modified_From__c","type":"picklist"},{"label":"testmultipicklist","value":"biztechcs__testmultipicklist__c","type":"multipicklist"}],"field3_options":[{"label":"Closed Date","value":"ClosedDate","type":"datetime"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"}],"field1_selected_option":"Subject","field2_selected_option":"Status","field3_selected_option":"CreatedDate","dropdown_field_color":"#00A8FF"} /--></div>
            <!-- /wp:column --></div>
            <!-- /wp:columns -->

            <!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-recent-activity {"activity_block_id":"activity_block_109","selected_module":"contract","view_style":"style_3","field_options_loaded":true,"field1_options":[{"label":"Contract ID","value":"Id","type":"id"},{"label":"Account ID","value":"AccountId","type":"reference"},{"label":"Price Book ID","value":"Pricebook2Id","type":"reference"},{"label":"Owner Expiration Notice","value":"OwnerExpirationNotice","type":"picklist"},{"label":"Contract Start Date","value":"StartDate","type":"date"},{"label":"Contract End Date","value":"EndDate","type":"date"},{"label":"Billing Street","value":"BillingStreet","type":"textarea"},{"label":"Billing City","value":"BillingCity","type":"string"},{"label":"Billing State/Province","value":"BillingState","type":"string"},{"label":"Billing Zip/Postal Code","value":"BillingPostalCode","type":"string"},{"label":"Billing Country","value":"BillingCountry","type":"string"},{"label":"Billing Latitude","value":"BillingLatitude","type":"double"},{"label":"Billing Longitude","value":"BillingLongitude","type":"double"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy","type":"picklist"},{"label":"Billing Address","value":"BillingAddress","type":"address"},{"label":"Contract Term","value":"ContractTerm","type":"int"},{"label":"Owner ID","value":"OwnerId","type":"reference"},{"label":"Status","value":"Status","type":"picklist"},{"label":"Company Signed By ID","value":"CompanySignedId","type":"reference"},{"label":"Company Signed Date","value":"CompanySignedDate","type":"date"},{"label":"Customer Signed By ID","value":"CustomerSignedId","type":"reference"},{"label":"Customer Signed Title","value":"CustomerSignedTitle","type":"string"},{"label":"Customer Signed Date","value":"CustomerSignedDate","type":"date"},{"label":"Special Terms","value":"SpecialTerms","type":"textarea"},{"label":"Activated By ID","value":"ActivatedById","type":"reference"},{"label":"Activated Date","value":"ActivatedDate","type":"datetime"},{"label":"Status Category","value":"StatusCode","type":"picklist"},{"label":"Description","value":"Description","type":"textarea"},{"label":"Contract Name","value":"Name","type":"string"},{"label":"Deleted","value":"IsDeleted","type":"boolean"},{"label":"Contract Number","value":"ContractNumber","type":"string"},{"label":"Last Approved Date","value":"LastApprovedDate","type":"datetime"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Created By ID","value":"CreatedById","type":"reference"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Last Modified By ID","value":"LastModifiedById","type":"reference"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Activity","value":"LastActivityDate","type":"date"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"Related Contact","value":"biztechcs__ContactId__c","type":"reference"}],"field2_options":[{"label":"Owner Expiration Notice","value":"OwnerExpirationNotice","type":"picklist"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy","type":"picklist"},{"label":"Status","value":"Status","type":"picklist"},{"label":"Status Category","value":"StatusCode","type":"picklist"}],"field3_options":[{"label":"Contract Start Date","value":"StartDate","type":"date"},{"label":"Contract End Date","value":"EndDate","type":"date"},{"label":"Company Signed Date","value":"CompanySignedDate","type":"date"},{"label":"Customer Signed Date","value":"CustomerSignedDate","type":"date"},{"label":"Activated Date","value":"ActivatedDate","type":"datetime"},{"label":"Last Approved Date","value":"LastApprovedDate","type":"datetime"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Activity","value":"LastActivityDate","type":"date"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"}],"field1_selected_option":"ContractNumber","field2_selected_option":"Status","field3_selected_option":"StartDate","dropdown_field_color":"#00A8FF"} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-recent-activity {"activity_block_id":"activity_block_358","selected_module":"order","view_style":"style_3","field_options_loaded":true,"field1_options":[{"label":"Order ID","value":"Id","type":"id"},{"label":"Owner ID","value":"OwnerId","type":"reference"},{"label":"Contract ID","value":"ContractId","type":"reference"},{"label":"Account ID","value":"AccountId","type":"reference"},{"label":"Price Book ID","value":"Pricebook2Id","type":"reference"},{"label":"Order ID","value":"OriginalOrderId","type":"reference"},{"label":"Order Start Date","value":"EffectiveDate","type":"date"},{"label":"Order End Date","value":"EndDate","type":"date"},{"label":"Reduction Order","value":"IsReductionOrder","type":"boolean"},{"label":"Status","value":"Status","type":"picklist"},{"label":"Description","value":"Description","type":"textarea"},{"label":"Customer Authorized By ID","value":"CustomerAuthorizedById","type":"reference"},{"label":"Customer Authorized Date","value":"CustomerAuthorizedDate","type":"date"},{"label":"Company Authorized By ID","value":"CompanyAuthorizedById","type":"reference"},{"label":"Company Authorized Date","value":"CompanyAuthorizedDate","type":"date"},{"label":"Order Type","value":"Type","type":"picklist"},{"label":"Billing Street","value":"BillingStreet","type":"textarea"},{"label":"Billing City","value":"BillingCity","type":"string"},{"label":"Billing State/Province","value":"BillingState","type":"string"},{"label":"Billing Zip/Postal Code","value":"BillingPostalCode","type":"string"},{"label":"Billing Country","value":"BillingCountry","type":"string"},{"label":"Billing Latitude","value":"BillingLatitude","type":"double"},{"label":"Billing Longitude","value":"BillingLongitude","type":"double"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy","type":"picklist"},{"label":"Billing Address","value":"BillingAddress","type":"address"},{"label":"Shipping Street","value":"ShippingStreet","type":"textarea"},{"label":"Shipping City","value":"ShippingCity","type":"string"},{"label":"Shipping State/Province","value":"ShippingState","type":"string"},{"label":"Shipping Zip/Postal Code","value":"ShippingPostalCode","type":"string"},{"label":"Shipping Country","value":"ShippingCountry","type":"string"},{"label":"Shipping Latitude","value":"ShippingLatitude","type":"double"},{"label":"Shipping Longitude","value":"ShippingLongitude","type":"double"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy","type":"picklist"},{"label":"Shipping Address","value":"ShippingAddress","type":"address"},{"label":"Order Name","value":"Name","type":"string"},{"label":"PO Date","value":"PoDate","type":"date"},{"label":"PO Number","value":"PoNumber","type":"string"},{"label":"Order Reference Number","value":"OrderReferenceNumber","type":"string"},{"label":"Bill To Contact ID","value":"BillToContactId","type":"reference"},{"label":"Ship To Contact ID","value":"ShipToContactId","type":"reference"},{"label":"Activated Date","value":"ActivatedDate","type":"datetime"},{"label":"Activated By ID","value":"ActivatedById","type":"reference"},{"label":"Status Category","value":"StatusCode","type":"picklist"},{"label":"Order Number","value":"OrderNumber","type":"string"},{"label":"Order Amount","value":"TotalAmount","type":"currency"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Created By ID","value":"CreatedById","type":"reference"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Last Modified By ID","value":"LastModifiedById","type":"reference"},{"label":"Deleted","value":"IsDeleted","type":"boolean"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"Related Contact","value":"biztechcs__ContactId__c","type":"reference"},{"label":"Order","value":"biztechcs__Order__c","type":"reference"}],"field2_options":[{"label":"Status","value":"Status","type":"picklist"},{"label":"Order Type","value":"Type","type":"picklist"},{"label":"Billing Geocode Accuracy","value":"BillingGeocodeAccuracy","type":"picklist"},{"label":"Shipping Geocode Accuracy","value":"ShippingGeocodeAccuracy","type":"picklist"},{"label":"Status Category","value":"StatusCode","type":"picklist"}],"field3_options":[{"label":"Order Start Date","value":"EffectiveDate","type":"date"},{"label":"Order End Date","value":"EndDate","type":"date"},{"label":"Customer Authorized Date","value":"CustomerAuthorizedDate","type":"date"},{"label":"Company Authorized Date","value":"CompanyAuthorizedDate","type":"date"},{"label":"PO Date","value":"PoDate","type":"date"},{"label":"Activated Date","value":"ActivatedDate","type":"datetime"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"}],"field1_selected_option":"OrderNumber","field2_selected_option":"Status","field3_selected_option":"EffectiveDate","dropdown_field_color":"#00A8FF"} /--></div>
            <!-- /wp:column --></div>
            <!-- /wp:columns -->

            <!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-recent-activity {"activity_block_id":"activity_block_999","selected_module":"contentdocument","module_title":"Recent Attachment","view_style":"style_5","field_options_loaded":true,"field1_options":[{"label":"ContentDocument ID","value":"Id","type":"id"},{"label":"Created By ID","value":"CreatedById","type":"reference"},{"label":"Created","value":"CreatedDate","type":"datetime"},{"label":"Last Modified By ID","value":"LastModifiedById","type":"reference"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Is Archived","value":"IsArchived","type":"boolean"},{"label":"User ID","value":"ArchivedById","type":"reference"},{"label":"Archived Date","value":"ArchivedDate","type":"date"},{"label":"Is Deleted","value":"IsDeleted","type":"boolean"},{"label":"Owner ID","value":"OwnerId","type":"reference"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Title","value":"Title","type":"string"},{"label":"Publish Status","value":"PublishStatus","type":"picklist"},{"label":"Latest Published Version ID","value":"LatestPublishedVersionId","type":"reference"},{"label":"Parent ID","value":"ParentId","type":"reference"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"Description","value":"Description","type":"textarea"},{"label":"Size","value":"ContentSize","type":"int"},{"label":"File Type","value":"FileType","type":"string"},{"label":"File Extension","value":"FileExtension","type":"string"},{"label":"Prevent others from sharing and unsharing","value":"SharingOption","type":"picklist"},{"label":"File Privacy on Records","value":"SharingPrivacy","type":"picklist"},{"label":"Content Modified Date","value":"ContentModifiedDate","type":"datetime"},{"label":"Asset File ID","value":"ContentAssetId","type":"reference"}],"field2_options":[{"label":"Publish Status","value":"PublishStatus","type":"picklist"},{"label":"Prevent others from sharing and unsharing","value":"SharingOption","type":"picklist"},{"label":"File Privacy on Records","value":"SharingPrivacy","type":"picklist"}],"field3_options":[{"label":"Created","value":"CreatedDate","type":"datetime"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Archived Date","value":"ArchivedDate","type":"date"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Last Viewed Date","value":"LastViewedDate","type":"datetime"},{"label":"Last Referenced Date","value":"LastReferencedDate","type":"datetime"},{"label":"Content Modified Date","value":"ContentModifiedDate","type":"datetime"}],"field1_selected_option":"Title","field2_selected_option":"PublishStatus","field3_selected_option":"CreatedDate","dropdown_field_color":"#00A8FF"} /--></div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:sfcp/portal-recent-activity {"activity_block_id":"activity_block_43","selected_module":"task","view_style":"style_3","field_options_loaded":true,"field1_options":[{"label":"Activity ID","value":"Id","type":"id"},{"label":"Name ID","value":"WhoId","type":"reference"},{"label":"Related To ID","value":"WhatId","type":"reference"},{"label":"Subject","value":"Subject","type":"combobox"},{"label":"Due Date Only","value":"ActivityDate","type":"date"},{"label":"Status","value":"Status","type":"picklist"},{"label":"Priority","value":"Priority","type":"picklist"},{"label":"High Priority","value":"IsHighPriority","type":"boolean"},{"label":"Assigned To ID","value":"OwnerId","type":"reference"},{"label":"Description","value":"Description","type":"textarea"},{"label":"Deleted","value":"IsDeleted","type":"boolean"},{"label":"Account ID","value":"AccountId","type":"reference"},{"label":"Closed","value":"IsClosed","type":"boolean"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Created By ID","value":"CreatedById","type":"reference"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"Last Modified By ID","value":"LastModifiedById","type":"reference"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Archived","value":"IsArchived","type":"boolean"},{"label":"Call Duration","value":"CallDurationInSeconds","type":"int"},{"label":"Call Type","value":"CallType","type":"picklist"},{"label":"Call Result","value":"CallDisposition","type":"string"},{"label":"Call Object Identifier","value":"CallObject","type":"string"},{"label":"Reminder Date/Time","value":"ReminderDateTime","type":"datetime"},{"label":"Reminder Set","value":"IsReminderSet","type":"boolean"},{"label":"Recurrence Activity ID","value":"RecurrenceActivityId","type":"reference"},{"label":"Create Recurring Series of Tasks","value":"IsRecurrence","type":"boolean"},{"label":"Recurrence Start","value":"RecurrenceStartDateOnly","type":"date"},{"label":"Recurrence End","value":"RecurrenceEndDateOnly","type":"date"},{"label":"Recurrence Time Zone","value":"RecurrenceTimeZoneSidKey","type":"picklist"},{"label":"Recurrence Type","value":"RecurrenceType","type":"picklist"},{"label":"Recurrence Interval","value":"RecurrenceInterval","type":"int"},{"label":"Recurrence Day of Week Mask","value":"RecurrenceDayOfWeekMask","type":"int"},{"label":"Recurrence Day of Month","value":"RecurrenceDayOfMonth","type":"int"},{"label":"Recurrence Instance","value":"RecurrenceInstance","type":"picklist"},{"label":"Recurrence Month of Year","value":"RecurrenceMonthOfYear","type":"picklist"},{"label":"Repeat This Task","value":"RecurrenceRegeneratedType","type":"picklist"},{"label":"Task Subtype","value":"TaskSubtype","type":"picklist"},{"label":"Completed Date","value":"CompletedDateTime","type":"datetime"}],"field2_options":[{"label":"Status","value":"Status","type":"picklist"},{"label":"Priority","value":"Priority","type":"picklist"},{"label":"Call Type","value":"CallType","type":"picklist"},{"label":"Recurrence Time Zone","value":"RecurrenceTimeZoneSidKey","type":"picklist"},{"label":"Recurrence Type","value":"RecurrenceType","type":"picklist"},{"label":"Recurrence Instance","value":"RecurrenceInstance","type":"picklist"},{"label":"Recurrence Month of Year","value":"RecurrenceMonthOfYear","type":"picklist"},{"label":"Repeat This Task","value":"RecurrenceRegeneratedType","type":"picklist"},{"label":"Task Subtype","value":"TaskSubtype","type":"picklist"}],"field3_options":[{"label":"Due Date Only","value":"ActivityDate","type":"date"},{"label":"Created Date","value":"CreatedDate","type":"datetime"},{"label":"Last Modified Date","value":"LastModifiedDate","type":"datetime"},{"label":"System Modstamp","value":"SystemModstamp","type":"datetime"},{"label":"Reminder Date/Time","value":"ReminderDateTime","type":"datetime"},{"label":"Recurrence Start","value":"RecurrenceStartDateOnly","type":"date"},{"label":"Recurrence End","value":"RecurrenceEndDateOnly","type":"date"},{"label":"Completed Date","value":"CompletedDateTime","type":"datetime"}],"field1_selected_option":"Subject","field2_selected_option":"Status","field3_selected_option":"CreatedDate","dropdown_field_color":"#00A8FF"} /--></div>
            <!-- /wp:column --></div>
            <!-- /wp:columns -->

            <!-- wp:paragraph -->
            <p></p>
            <!-- /wp:paragraph -->';

        return $detail_content;
    }

    /**
     * Edit page
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function CreateEditPage() {
        $id = wp_insert_post(
                array(
                    'post_title' => __('Portal Edit Page', 'bcp_portal'),
                    'post_type' => 'page',
                    'post_content' => $this->GetEditPageContent(),
                    'post_status' => 'publish',
                )
        );
        update_post_meta($id, '_bcp_portal_elements', true);
        update_post_meta($id, '_bcp_page_type', 'bcp_add_edit');
        update_post_meta($id, '_bcp_modules', 'All');
        update_option('bcp_add_edit_page', $id);
        return $id;
    }

    /**
     * Edit page content
     *
     * @since 4.0.0
     * @return void
     * @author Akshay Kungiri 
     */
    public function GetEditPageContent() {
        return '<!-- wp:sfcp/portal-add-edit-block /-->';
    }

}
