<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RTMediaPrivacy
 *
 * @author saurabh
 */
class RTMediaPrivacy {

    /**
     *
     * @var object default application wide privacy levels
     */
    public $default_privacy;

    function __construct() {
        add_action('rtmedia_after_file_upload_ui', array($this, 'uploader_privacy_ui'));
        add_action('rtmedia_add_edit_fields', array($this, 'select_privacy_ui'));
        add_action('bp_init', array($this, 'add_nav'));
        add_action('bp_template_content', array($this, 'content'));
        add_filter('bp_activity_get_user_join_filter', array($this, 'activity_privacy'), 10, 6);
    }

    function uploader_privacy_ui($attr) {
        if (!isset($attr['privacy'])) {
            $this->select_privacy_ui();
        }
    }

    function select_privacy_ui() {
        global $rtmedia;
        
        if (intval($rtmedia->options["privacy_enabled"]) === 0)
            return false;
        
        if (intval($rtmedia->options["privacy_userOverride"]) === 0)
            return false;
        
        global $rtmedia_media;
        $default = 0;
        if (isset($rtmedia_media->privacy))
            $default = $rtmedia_media->privacy;
        else 
            $default = intval($rtmedia->options["privacy_default"]);

        $form = new rtForm();
        $attributes = array(
            'name' => 'privacy',
            'id' => 'privacy'
        );
        global $rtmedia;
        $privacy_levels = $rtmedia->privacy_settings['levels'];
        if (class_exists('BuddyPress')) {
            if (!bp_is_active('friends')) {
                unset($privacy_levels[40]);
            }
        } else {
            unset($privacy_levels[40]);
        }
        foreach ($privacy_levels as $key => $value) {
            $privacy = explode(' - ', $value);
            $attributes['rtForm_options'][] = array(
                $privacy[0] => $key,
                'selected' => ($key == $default) ? 1 : 0
            );
        }


        echo $form->get_select($attributes);
    }

    public function system_default() {
        return 0;
    }

    public function site_default() {
        global $rtmedia;

        return rtmedia_get_site_option('privacy_settings');
    }

    public function user_default() {
        return;
    }

    public function get_default() {
        $default_privacy = $this->user_default();

        if ($default_privacy === false) {
            $default_privacy = $this->site_default();
        }

        if (!$default_privacy === false) {
            $default_privacy = $this->system_default();
        }
    }

    static function is_enabled() {
        global $bp_media;
        $options = $bp_media->options;
        if (!array_key_exists('privacy_enabled', $options)) {
            return false;
        } else {
            if ($options['privacy_enabled'] != true) {
                return false;
            }
        }
        return true;
    }

    static function save_user_default($level = 0, $user_id = false) {
        if ($user_id == false) {
            global $bp;
            $user_id = $bp->loggedin_user->id;
        }
        return update_user_meta($user_id, 'bp_media_privacy', $level);
    }

    static function get_user_default($user_id = false) {
        if ($user_id == false) {
            global $bp;
            $user_id = $bp->loggedin_user->id;
        }
        $user_privacy = get_user_meta($user_id, 'bp_media_privacy', true);
        if ($user_privacy === false) {
            
        }
        return $user_privacy;
    }

    static function required_access($object_id = false) {
        if (BPMediaPrivacy::is_enabled() == false)
            return;
        if ($object_id == false)
            return;
        $privacy = BPMediaPrivacy::get_privacy($object_id);
        $parent = get_post_field('post_parent', $object_id, 'raw');
        $parent_privacy = BPMediaPrivacy::get_privacy($parent);

        if ($privacy === false) {
            if ($parent_privacy !== false) {
                $privacy = $parent_privacy;
            } else {
                $privacy = BPMediaPrivacy::default_privacy();
            }
        }
        return $privacy;
    }

    function add_nav() {

        if (bp_displayed_user_domain()) {
            $user_domain = bp_displayed_user_domain();
        } elseif (bp_loggedin_user_domain()) {
            $user_domain = bp_loggedin_user_domain();
        } else {
            return;
        }



        $settings_link = trailingslashit($user_domain . 'settings');

        $defaults = array(
            'name' => $this->title(), // Display name for the nav item
            'slug' => 'privacy', // URL slug for the nav item
            'parent_slug' => 'settings', // URL slug of the parent nav item
            'parent_url' => $settings_link, // URL of the parent item
            'item_css_id' => 'rtmedia-privacy-settings', // The CSS ID to apply to the HTML of the nav item
            'user_has_access' => true, // Can the logged in user see this nav item?
            'site_admin_only' => false, // Can only site admins see this nav item?
            'position' => 900, // Index of where this nav item should be positioned
            'screen_function' => array($this, 'settings_ui'), // The name of the function to run when clicked
            'link' => ''     // The link for the subnav item; optional, not usually required.
        );
        bp_core_new_subnav_item($defaults);
    }

    function settings_ui() {
        if (bp_action_variables()) {
            bp_do_404();
            return;
        }


        // Load the template
        bp_core_load_template(apply_filters('bp_settings_screen_delete_account', 'members/single/plugins'));
    }

    function content() {
        if (buddypress()->current_action != 'privacy')
            return;

        global $rtmedia;
        $default_user_privacy = array(
            'title' => __("Default Privacy", "rtmedia"),
            'callback' => array("RTMediaFormHandler", "radio"),
            'args' => array(
                'key' => 'privacy_default',
                'radios' => $rtmedia->privacy_settings['levels'],
                'default' => get_user_meta(get_current_user_id(), 'rtmedia-default-privacy')
            )
        );
        ?>
        <div class="large-12">
            <div class="row section">
                <div class="columns large-2"><?php echo $default_user_privacy['title']; ?></div>
                <div class="columns large-5">
        <?php call_user_func($default_user_privacy['callback'], $default_user_privacy['args']); ?>
                </div>
            </div>
        </div>
    <?php
    }

    function title() {
        return __('Privacy', 'rtmedia');
    }

    function activity_privacy($sql, $select_sql, $from_sql, $where_sql, $sort, $pag_sql = '') {
        //apply_filters( 'bp_activity_get_user_join_filter', "
        //"{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_recorded {$sort} {$pag_sql}",
        //$select_sql,
        //$from_sql,
        //$where_sql,
        //$sort,
        //$pag_sql

        $sql = '';

        $where = '';

//		$from_sql = " FROM {$bp->activity->table_name} a LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID";

        global $bp, $wpdb;

        if (is_user_logged_in()) {
            $user = get_current_user_id();
        } else {
            $user = 0;
        }

        $where .= "AND (CONVERT(m.meta_value,SIGNED INTEGER) <= 0)";

        if ($user) {
            $where .= "OR ((m.meta_value=20)";
            $where .= " OR (a.user_id={$user} AND CONVERT(m.meta_value, UNSIGNED INTEGER) >= 40)";
            if (class_exists('BuddyPress')) {
                if (bp_is_active('friends')) {
                    $friendship = new RTMediaFriends();
                    $friends = $friendship->get_friends_cache($user);
                    $where .= " OR (CONVERT(m.meta_value,UNSIGNED INTEGER)=40 AND a.user_id IN ('" . implode("','", $friends) . "'))";
                }
            }
            $where .= ')';
        }
        if (function_exists("bp_core_get_table_prefix"))
            $bp_prefix = bp_core_get_table_prefix();
        else
            $bp_prefix = "";

        $select_sql = str_replace("SELECT", "SELECT distinct", $select_sql);

        $from_sql = " FROM {$bp->activity->table_name} a LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID LEFT JOIN {$bp->activity->table_name_meta} m ON a.id = m.activity_id";
        $where_sql = $where_sql . " AND (NOT EXISTS (SELECT m.activity_id FROM {$bp_prefix}bp_activity_meta m WHERE m.meta_key='rtmedia_privacy' AND m.activity_id=a.id) OR (m.meta_key='rtmedia_privacy' {$where} ) )";
        $newsql = "{$select_sql} {$from_sql} {$where_sql} ORDER BY a.date_recorded {$sort} {$pag_sql}";
        return $newsql;
    }

}
?>
