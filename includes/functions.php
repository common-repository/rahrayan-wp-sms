<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//admin panel
function rahrayan_admin()
{
    add_menu_page('ره رایان پیامک', 'ره رایان پیامک', 'manage_options', 'rahrayan', 'rahrayan_admin_main', plugin_dir_url(__FILE__) . '/images/logo.png');
    add_submenu_page('rahrayan', 'تنظیمات', 'تنظیمات', 8, 'rahrayan_setting', 'rahrayan_admin_setting');
    add_submenu_page('rahrayan', 'پیام های ارسالی', 'پیام های ارسالی', 8, 'rahrayan_smessages', 'rahrayan_admin_smessages');
    add_submenu_page('rahrayan', 'پیام های دریافتی', 'پیام های دریافتی', 8, 'rahrayan_rmessages', 'rahrayan_admin_rmessages');
    add_submenu_page('rahrayan', 'ارسال پیام', 'ارسال پیام', 8, 'rahrayan_send', 'rahrayan_admin_send');
    add_submenu_page('rahrayan', 'گروه های دفترچه تلفن', 'گروه های دفترچه تلفن', 8, 'rahrayan_groups', 'rahrayan_admin_groups');
    add_submenu_page('rahrayan', 'دفترچه تلفن', 'دفترچه تلفن', 8, 'rahrayan_phonebook', 'rahrayan_admin_phonebook');
    add_submenu_page('rahrayan', 'گزارشات', 'گزارشات', 8, 'rahrayan_reports', 'rahrayan_admin_reports');
    add_submenu_page('rahrayan', 'فرم عضویت خبرنامه', 'فرم عضویت خبرنامه', 8, 'rahrayan_form', 'rahrayan_admin_form');
    add_submenu_page('rahrayan', 'برون ریزی', 'برون ریزی', 8, 'rahrayan_export', 'rahrayan_admin_export');
}

//clean inputs
function rahrayan_clean(&$string)
{
    $string = trim($string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = strip_tags($string);
    $string = sanitize_textarea_field($string);
    $string = esc_sql($string);
    return $string;
}

//install function
function rahrayan_install()
{
    global $table_prefix, $wpdb;
    //add options
    add_option('rahrayan_update_period', 6);
    add_option('rahrayan_page', 10);
    add_option('rahrayan_iborder', '#e0dfdf');
    add_option('rahrayan_ihborder', '#83b2d8');
    add_option('rahrayan_sbg', '#579bd3');
    add_option('rahrayan_shbg', '#4887bc');
    add_option('rahrayan_sborder', '#4887bc');
    add_option('rahrayan_shborder', '#4887bc');
    add_option('rahrayan_fontc', '#818181');
    add_option('rahrayan_cfontc', '#6b9ecc');
    add_option('rahrayan_fonts', '17');
    add_option('rahrayan_fontm', 'Yekan');
    add_option('rahrayan_form', '#f5f5f5');
    add_option('rahrayan_border', '2');
    add_option('rahrayan_radius', '3');
    add_option('rahrayan_ilbg', '#ffffff');
    add_option('rahrayan_ilhbg', '#ffffff');
    add_option('rahrayan_ibg', 'none');
    add_option('rahrayan_ihbg', 'none');
    //create tables
    $q1 = "CREATE TABLE IF NOT EXISTS `{$table_prefix}rahrayan_messages` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `date` varchar(50) NOT NULL,
          `message` text CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
          `sender` varchar(50) NOT NULL,
          `recipient` text NOT NULL,
          `mode` smallint(1) NOT NULL,
          `flash` smallint(1) NOT NULL,
          PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
    $q2 = "CREATE TABLE IF NOT EXISTS `{$table_prefix}rahrayan_members` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `date` varchar(50) NOT NULL,
          `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
          `lname` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
          `gender` smallint(1) NOT NULL,
          `sync` smallint(1) NOT NULL,
          `status` smallint(1) NOT NULL,
          `mobile` varchar(12) NOT NULL,
          `gid` varchar(12) NOT NULL,
           PRIMARY KEY (`id`),
           UNIQUE KEY `mobile` (`mobile`)
           ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
    $q3 = "CREATE TABLE IF NOT EXISTS `{$table_prefix}rahrayan_groups` (
		  `gid` bigint(20) NOT NULL AUTO_INCREMENT,
          `gdate` varchar(50) NOT NULL,
          `gname` varchar(255) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
          `gshow` smallint(1) NOT NULL,
           PRIMARY KEY (`gid`)
           ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
    $q4 = "ALTER TABLE `{$table_prefix}rahrayan_messages` ADD `delivery` TEXT NOT NULL AFTER `flash`;";
    $q5 = "CREATE TABLE IF NOT EXISTS `{$table_prefix}rahrayan_GFVerification` (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id mediumint(8) unsigned not null,
            lead_id mediumint(10) unsigned not null,	
            try_num mediumint(10) unsigned not null,
            sent_num mediumint(10) unsigned not null,		
			mobile VARCHAR(12) NOT NULL,
			code VARCHAR(250),
			status tinyint(1),
			PRIMARY KEY  (id),
			KEY form_id (form_id)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($q1);
    dbDelta($q2);
    dbDelta($q3);
    $wpdb->query($q4);
    dbDelta($q5);
}

//convert stdclass to array
function rahrayan_o2a($d)
{
    if (is_object($d)) {
        $d = get_object_vars($d);
    }
    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    } else {
        return $d;
    }
}

function rahrayan_array2csv($title, $array)
{
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, $title);
    foreach ($array as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}
