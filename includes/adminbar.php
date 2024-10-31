<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
//admin bar
if ($rahrayan -> access()) {
	add_action('admin_bar_menu', 'rahrayan_adminbar', 15);
	function rahrayan_adminbar() {
		global $wp_admin_bar, $rahrayan;
		if (!is_super_admin() || !is_admin_bar_showing())
			return;
		$wp_admin_bar -> add_menu(array('id' => 'rahrayan', 'title' => '<img style="padding-top: 8px" src="' . plugin_dir_url(__FILE__) . '/images/logo.png"/>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=rahrayan'));
		$balance = $rahrayan -> credit;
		if ($balance && $rahrayan -> is_ready) {
			$balance = number_format($balance);
			$wp_admin_bar -> add_menu(array('parent' => 'rahrayan', 'title' => 'موجودی حساب: ' . $balance . ' ریال', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=rahrayan_setting'));
		}
		$t = 'اعضای خبرنامه: ' . number_format(intval($rahrayan -> count)) . ' نفر';
		$wp_admin_bar -> add_menu(array('parent' => 'rahrayan', 'title' => $t, 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=rahrayan_phonebook'));
		$wp_admin_bar -> add_menu(array('parent' => 'rahrayan', 'title' => 'مشاهده پیام ها', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=rahrayan_smessages'));
		$wp_admin_bar -> add_menu(array('parent' => 'rahrayan', 'title' => 'ره رایان پیامک', 'href' => 'http://rahco.ir'));
	}

}
