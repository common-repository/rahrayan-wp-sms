<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//new comment
function rahrayan_comment($id, $object)
{
    if (!empty($object->comment_type))
        return;
    global $rahrayan;
    $to = explode(';', get_option('rahrayan_admin'));
    $message = get_option('rahrayan_comment_text');
    $message = str_replace(array(
        '{author}',
        '{email}',
        '{ip}',
        '{date}',
        '{comment}',
        '{url}'
    ), array(
        $object->comment_author,
        $object->comment_author_email,
        $object->comment_author_IP,
        $object->comment_date,
        $object->comment_content,
        $object->comment_author_url
    ), $message);
    $rahrayan->send($to, $message);
}

if (get_option('rahrayan_comment'))
    add_action('wp_insert_comment', 'rahrayan_comment', 99, 2);

//2FA
if (get_option('rahrayan_2fa')) {
    function two_factor_rahrayan($providers)
    {
        $providers['Two_Factor_Rahrayan'] = dirname(__FILE__) . '/2FA.php';

        return $providers;
    }

    add_filter('two_factor_providers', 'two_factor_rahrayan');

}

//new user
function rahrayan_register($id)
{
    global $rahrayan, $date;
    $to = explode(';', get_option('rahrayan_admin'));
    $message = get_option('rahrayan_register_text');
    $user_info = get_userdata($id);
    $message = str_replace(array(
        '{username}',
        '{date}',
        '{email}'
    ), array(
        $user_info->user_login,
        $rahrayan->date(),
        $user_info->user_email
    ), $message);
    $rahrayan->send($to, $message);
}

if (get_option('rahrayan_register'))
    add_action('user_register', 'rahrayan_register', 10, 1);

//login
function rahrayan_login($user_login, $user)
{
    global $rahrayan;
    $to = explode(';', get_option('rahrayan_admin'));
    $message = get_option('rahrayan_login_text');
    $message = str_replace(array(
        '{username}',
        '{date}'
    ), array(
        $user->user_login,
        $rahrayan->date()
    ), $message);
    $rahrayan->send($to, $message);
}

if (get_option('rahrayan_login'))
    add_action('wp_login', 'rahrayan_login', 99, 2);

//edd new order
function rahrayan_edd()
{
    global $rahrayan;
    $to = explode(';', get_option('rahrayan_admin'));
    $message = get_option('rahrayan_edd_text');
    $message = str_replace(array('{date}'), array($rahrayan->date()), $message);
    $rahrayan->send($to, $message);
}

if (get_option('rahrayan_edd'))
    add_action('edd_complete_purchase', 'rahrayan_edd');

function rahrayan_prepare_wc_text($order, $text)
{
    global $rahrayan;

    $statuses = array(
        'pending' => 'معلق',
        'processing' => 'درحال پردازش',
        'completed' => 'انجام شده',
        'cancelled' => 'کنسل شده',
        'refunded' => 'برگشت داده شده',
        'on-hold' => 'در انتظار پرداخت',
    );

    $items = $order->get_items();
    $items_string = array();
    foreach ($items as $item) {
        $items_string[] = "{$item['name']}x{$item['qty']}";
    }

    return str_replace(array(
        '{first_name}',
        '{last_name}',
        '{status}',
        '{price}',
        '{transaction_id}',
        '{id}',
        '{date}',
        '{items}'
    ), array(
        isset($order->shipping_first_name) && !empty($order->shipping_first_name) ? $order->shipping_first_name : $order->billing_first_name,
        isset($order->shipping_last_name) && !empty($order->shipping_last_name) ? $order->shipping_last_name : $order->billing_last_name,
        $statuses[$order->status] ? $statuses[$order->status] : $order->status,
        number_format($order->total),
        $order->transaction_id,
        $order->id,
        $rahrayan->date(),
        implode("\n", $items_string)
    ), $text);
}

//woocommerce
function rahrayan_wc_new($order_id)
{
    if (get_post_meta($order_id, 'mp_wc_new_sms_sent', true))
        return;

    update_post_meta($order_id, 'mp_wc_new_sms_sent', true);

    global $rahrayan;
    $order = new WC_Order($order_id);

    if (get_option('rahrayan_wc2')) {
        $message = get_option('rahrayan_wc2_text');
        $rahrayan->send(array($order->billing_phone), rahrayan_prepare_wc_text($order, $message));
    }

    if (get_option('rahrayan_wc')) {
        $to = explode(';', get_option('rahrayan_admin'));
        $message = get_option('rahrayan_wc_text');
        $rahrayan->send($to, rahrayan_prepare_wc_text($order, $message));
    }
}

add_action('woocommerce_thankyou', 'rahrayan_wc_new');

function rahrayan_wc2($order_id)
{
    global $rahrayan, $woocommerce;
    $order = new WC_Order($order_id);
    $status = $order->status;
    $to = array($order->billing_phone);

    if ($status == 'processing' && get_option('rahrayan_wc3')) {
        $message = get_option('rahrayan_wc3_text');
    } elseif ($status == 'completed' && get_option('rahrayan_wc4')) {
        $message = get_option('rahrayan_wc4_text');
    }
    $rahrayan->send($to, rahrayan_prepare_wc_text($order, $message));
}

add_action('woocommerce_order_status_changed', 'rahrayan_wc2', 10, 3);

function rahrayan_wc5($order_id)
{
    global $rahrayan, $woocommerce;
    $order = new WC_Order($order_id['order_id']);
    $to = array($order->billing_phone);
    $message = get_option('rahrayan_wc5_text');
    $message = rahrayan_prepare_wc_text($order, $message);
    $message = str_replace(array(
        '{text}'
    ), array(
        wptexturize($order_id['customer_note'])
    ), $message);
    $rahrayan->send($to, $message);
}

if (get_option('rahrayan_wc5'))
    add_action('woocommerce_new_customer_note', 'rahrayan_wc5', 10);

function rahrayan_wc_verification($posted)
{
    if (!preg_match("/^09([0-9]{9})$/", $posted['billing_phone'])) {
        wc_add_notice('شماره موبایل وارد شده معتبر نیست.', 'error');
        return;
    }

}

function rahrayan_wc_final_verification($null, $instance)
{
    $mobile = $instance->get_posted_address_data('phone');
    $user = wp_get_current_user();
    if (0 != $user->ID) {
        $last_verified_mobile = get_user_meta($user->ID, 'mp_last_verified_mobile', true);
        if ($last_verified_mobile != $mobile) {
            update_user_meta($user->ID, 'billing_phone', $mobile);
            throw new Exception('لطفا ابتدا شماره موبایل خود را تایید کنید، سپس مجدد برروی دکمه ثبت سفارش کلیک کنید. <a style="border-bottom:1px dotted #ccc" target="_blank" href="?mp_verify_mobile=1">برای تایید شماره روی این نوشته کلیک کنید.</a>');
        }
    }
    return $null;
}


if (get_option('rahrayan_wc_mobile_verification')) {
    add_action('woocommerce_after_checkout_validation', 'rahrayan_wc_verification');

    add_filter('woocommerce_create_order', 'rahrayan_wc_final_verification', 10, 2);

    if (isset($_GET['mp_verify_mobile'])) {
        $user = wp_get_current_user();

        if (0 != $user->ID) {
            $billing_phone = get_user_meta($user->ID, 'billing_phone', true);

            $profile_link = '<br/><a href="' . get_site_url() . '/?p=' . get_option('woocommerce_myaccount_page_id') . '">بروزرسانی شماره موبایل</a>';

            if (!preg_match("/^09([0-9]{9})$/", $billing_phone)) {
                wp_die("{$profile_link}شماره موبایل شما معتبر نیست.");
                return;
            }

            if ($billing_phone != get_user_meta($user->ID, 'mp_last_verified_mobile', true)) {
                if (isset($_POST['code'])) {
                    if ($_POST['code'] == get_user_meta($user->ID, 'mp_verification_code', true)) {
                        $success = true;
                        update_user_meta($user->ID, 'mp_verification_code', '');
                        update_user_meta($user->ID, 'mp_last_verified_mobile', $billing_phone);
                    } else {
                        $error = 'کد وارد شده صحیح نمی‌باشد.';
                    }
                } else {
                    $code = rand(1000, 9999);
                    update_user_meta($user->ID, 'mp_verification_code', $code);

                    $to = array($billing_phone);
                    $message = get_option('rahrayan_wc_mobile_verification_text');
                    $message = str_replace(array(
                        '{first_name}',
                        '{last_name}',
                        '{code}',
                        '{date}'
                    ), array(
                        get_user_meta($user->ID, 'billing_first_name', true),
                        get_user_meta($user->ID, 'billing_last_name', true),
                        $code,
                        $rahrayan->date(),
                    ), $message);
                    $rahrayan->send($to, $message);
                }

                ?>
                <!DOCTYPE html>
                <!--[if IE 8]>
                <html xmlns="http://www.w3.org/1999/xhtml" class="ie8" dir="rtl" lang="fa-IR">
                <![endif]-->
                <!--[if !(IE 8) ]><!-->
                <html xmlns="http://www.w3.org/1999/xhtml" dir="rtl" lang="fa-IR">
                <!--<![endif]-->
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
                    <title>تایید شماره موبایل</title>
                    <link rel='stylesheet'
                          href='<?php echo get_site_url() ?>/wp-admin/load-styles.php?c=1&amp;dir=rtl&amp;load%5B%5D=dashicons,buttons,forms,l10n,login'
                          type='text/css' media='all'/>
                </head>
                <body class="login login-action-login wp-core-ui rtl  locale-fa-ir">
                <div id="login">
                    <h1>تایید شماره موبایل</h1>
                    <form name="loginform" id="loginform" action=""
                          method="post">
                        <?php if (isset($success)) {
                            echo '<div style="color:green">تایید با موفقیت انجام شد. به صفحه قبل بازگشته و برروی ثبت سفارش کلیک کنید.</div>';
                        } else {
                            ?>
                            <?php
                            if (isset($error))
                                echo "<div id=\"login_error\">{$error}</div>";
                            ?>
                            <p>
                                <label> کد تایید به <?php echo $billing_phone ?> ارسال شد. <br/>
                                    <input type="text" name="code" class="input" placeholder="کد تایید" value=""
                                           size="20"
                                           required/></label>
                            </p>
                            <p class="submit">
                                <input type="submit" name="wp-submit" id="wp-submit"
                                       class="button button-primary button-large" value="تایید"/>
                            </p>
                            <?php
                        }
                        ?>
                    </form>
                </div>
                </body>
                </html>
                <?php
                exit;
            } else {
                wp_die('شماره شما از قبل تایید شده است.');
            }
        }
    }
}

//send new post
function rahrayan_metabox()
{
    add_meta_box('rahrayan', 'ارسال پیامک به مشترکین', 'rahrayan_metabox2', 'post', 'normal', 'high');
}

function rahrayan_metabox2($post)
{
    include dirname(__FILE__) . '/templates/metabox.php';
}

function rahrayan_send($id)
{
    global $wpdb, $table_prefix, $rahrayan;
    $x = intval($_POST['rahrayan_select']);
    if ($x == '1') {
        $to = $wpdb->get_col("SELECT mobile FROM {$table_prefix}rahrayan_members WHERE status = 1");
        if (empty($_POST['rahrayan_smessage'])) {
            $message = get_option('rahrayan_send_text');
            $message = str_replace(array(
                '{title}',
                '{date}',
                '{url}'
            ), array(
                get_the_title($id),
                get_post_time(get_option('date_format'), true, $id),
                wp_get_shortlink($id)
            ), $message);
        } else
            $message = $_POST['rahrayan_smessage'];
        $rahrayan->send($to, $message);
        return $id;
    }
}

if (get_option('rahrayan_send')) {
    add_action('add_meta_boxes', 'rahrayan_metabox');
    add_action('publish_post', 'rahrayan_send');
}
//wpcf7
function rahrayan_cf7_form($panels)
{
    $new_page = array('rahrayan' => array(
        'title' => 'ره رایان پیامک',
        'callback' => 'rahrayan_cf7_form_content'
    ));
    $panels = array_merge($panels, $new_page);
    return $panels;
}

function rahrayan_cf7_form_content($form)
{
    $admin_message = get_option('wpcf7_rahrayan_' . $form->id);
    $mobile_field = get_option('wpcf7_rahrayan_mobile_field_' . $form->id);
    $user_message = get_option('wpcf7_rahrayan_user_' . $form->id);
    include dirname(__FILE__) . '/templates/wpcf7.php';
}

function rahrayan_cf7_form2($form)
{
    update_option('wpcf7_rahrayan_' . $form->id, sanitize_textarea_field($_POST['wpcf7_rahrayan_admin']));
    update_option('wpcf7_rahrayan_mobile_field_' . $form->id, sanitize_text_field($_POST['wpcf7_rahrayan_mobile_field']));
    update_option('wpcf7_rahrayan_user_' . $form->id, sanitize_textarea_field($_POST['wpcf7_rahrayan_user']));
}

function rahrayan_cf7_send($form)
{
    global $rahrayan;
    $message = get_option('wpcf7_rahrayan_' . $form->id);
    if (!empty($message)) {
        $message = wpcf7_mail_replace_tags($message);
        $to = explode(';', get_option('rahrayan_admin'));
        $rahrayan->send($to, $message);
    }

    $user_message = get_option('wpcf7_rahrayan_user_' . $form->id);
    $mobile = wpcf7_mail_replace_tags('[' . get_option('wpcf7_rahrayan_mobile_field_' . $form->id) . ']');
    if (!empty($user_message) && !empty($mobile) && preg_match("/^09([0-9]{9})$/", $mobile)) {
        $user_message = wpcf7_mail_replace_tags($user_message);
        $rahrayan->send(array($mobile), $user_message);
    }
    return $form;
}

if (get_option('rahrayan_cf7')) {
    add_action('wpcf7_editor_panels', 'rahrayan_cf7_form');
    add_action('wpcf7_before_send_mail', 'rahrayan_cf7_send');
    add_action('wpcf7_after_save', 'rahrayan_cf7_form2');
}

//Gravity Forms
function rahrayan_gf()
{
    if (!method_exists('GFForms', 'include_feed_addon_framework')) {
        return;
    }
    require_once('GF.php');
    GFAddOn::register('GFRahrayan');
}

if (get_option('rahrayan_gravity_forms')) {
    add_action('gform_loaded', 'rahrayan_gf', 5);
}

//get replies of a ticket
if (is_admin() && $rahrayan->access() && isset($_GET['rahrayan_ticket']) && !empty($_GET['tid'])) {
    if (!$rahrayan->credit)
        die('0');
    $url = plugins_url('templates/assets/', __FILE__);
    echo '<div style="direction:rtl;text-align:right;font-family:Yekan"><h2>ارسال جواب برای تیکت شماره ' . rahrayan_clean($_GET['tid']) . '</h2><ul>';
    $ticket = $rahrayan->call('GetSentTickets', 'tickets', array(
        'ticketOwner' => $_GET['tid'],
        'ticketType' => 'All',
        'Keyword' => $search
    ));
    $ticket = rahrayan_o2a($ticket);
    $ticket = $ticket['TicketList'];
    if ($ticket['TicketID']) {
        $t = $ticket;
        unset($ticket);
        $ticket = array();
        $ticket[0] = $t;
    }
    if ($ticket[0]) {
        date_default_timezone_set('Asia/Tehran');
        foreach ($ticket as $key => $value) {
            $date = $rahrayan->date(strtotime($value['InsertDate']));
            echo '<li style="border:1px dotted #ccc; border-radius:3px;padding:8px;font-family:Mitra,Oxygen;font-size:17px">' . $date . '<br/>' . $rahrayan->nl2br($value['Contents']) . '</li>';
        }
    } else {
        echo '<li style="border:1px dotted #ccc; border-radius:3px;padding:8px;">هیچ پاسخی یافت نشد.</li>';
    }
    echo '<li style="border:1px dotted #ccc; border-radius:3px;padding:8px;"><h2 style="margin-top: 0px;">ارسال پاسخ</h2><form action="" method="post"><input type="hidden" name="do" value="reply" /><input type="hidden" name="name" value="' . rahrayan_clean($_GET['tid']) . '" /><textarea name="message" required></textarea><br/><input type="submit"  value="ارسال پاسخ"/>' . wp_nonce_field('mptaaction', 'mptaactionf') . '</form></li>';
    echo '</ul></div>';
    exit;
}
//create phonebook backup
if (is_admin() && $rahrayan->access() && isset($_GET['rahrayan_backup'])) {
    if (!$rahrayan->credit)
        die('0');
    $members = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_members", ARRAY_A);
    $groups = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_groups", ARRAY_A);
    $json = json_encode(array(
        'members' => $members,
        'groups' => $groups
    ));
    $filetype = 'application/octetstream';
    $url = str_replace(array(
        'http://',
        'https://',
        'www.',
        '/'
    ), '', get_bloginfo('url'));
    $filename = 'filename="' . 'backup-rahrayan-' . $rahrayan->date() . '-' . $url . '.mpb' . '"';
    header('Content-Type: ' . $filetype);
    header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Content-Disposition: inline; ' . $filename);
    header('Content-Length: ' . strlen($json));
    header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo $json;
    exit;
}
//get members of a group wih ajax
if (isset($_POST['mpgc']) && is_admin() && $rahrayan->access() && wp_verify_nonce($_POST['mpsactionf'], 'mpsaction')) {
    if (!$rahrayan->credit)
        die('0');
    if ($_POST['mpgc'] == 'all')
        $where = '';
    else
        $where = 'WHERE gid=' . intval($_POST['mpgc']);
    $r = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_members {$where}", ARRAY_A);
    echo '<td>انتخاب اعضا</td><td><select name="members[]" required multiple>';
    if ($r[0]) {
        foreach ($r as $key => $value) {
            $name = $value['name'] . ' ' . $value['lname'];
            echo '<option selected value="' . $value['id'] . '">' . $name . ' (' . $value['mobile'] . ')</option>';
        }
    } else {
        echo '<option disabled>هیچ عضوی یافت نشد.</option>';
    }
    echo '</select><p class="description">پیامک را چه کسانی دریافت کنند؟</p></td><div id="mcount">' . count($r) . '</div>';
    exit;
}
//show detail
if (is_admin() && $rahrayan->access() && isset($_GET['mpsd'])) {
    if (!$rahrayan->credit)
        die('0');
    $url = plugins_url('templates/assets/', __FILE__);
    $id = (int)$_GET['mpsd'];
    if (!isset($_GET['rebulid']))
        echo '<div style="direction:rtl;text-align:right;font-family:Yekan"><h2>مشاهده اطلاعات دریافت کنندگان پیامک شماره ' . $id . '</h2><ul style="list-style-type: decimal;margin-right:15px"><div class="minfo' . $id . '">';
    $s = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_messages WHERE id = '{$id}'", ARRAY_A);
    $mobile = $s[0]['recipient'];
    $m2 = explode(',', $s[0]['recipient']);
    $where = array();
    foreach ($m2 as $key => $value) {
        $where[] = "mobile LIKE '%{$value}'";
    }
    $where = implode(' OR ', $where);
    $m = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_members WHERE {$where}", ARRAY_A);
    $n = array();
    $mobile = explode(',', $s[0]['recipient']);
    if (!empty($s[0]['delivery'])) {
        $de = json_decode($s[0]['delivery'], true);
        if (isset($_GET['rebulid'])) {
            @set_time_limit(0);
            @ignore_user_abort(1);
            @ini_set('memory_limit', '512M');
            foreach ($de as $key => $value) {
                if ($key != 'time') {
                    if (!in_array($de[$key][0], array(
                        0,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        9,
                        10,
                        11,
                        12
                    ))) {	
					$dargah=explode("-","1000-2000-3000-9821-50001-50002-50004");
					$dargah_select="";
					foreach($dargah as $k => $v){
						 if(substr($s[0]['sender'], 0, strlen($v)) === $v)
							$dargah_select=$v;
					}
						
                        $result = $rahrayan->call('sms_deliver', 'send', array('sss'=>$s[0]['sender'],'dargah'=>$dargah_select,'smsid_arr' => explode(",",$de[$key][0])));
						
                        $de[$key][1] = $result;
                    }
                }
            }
            $d = json_encode($de);
            $wpdb->query("UPDATE {$table_prefix}rahrayan_messages SET delivery = '{$d}'  WHERE id = '{$id}'");
        }
    }
    if ($m[0]) {
        foreach ($m as $key => $value) {
            $n[$value['mobile']] = $value['name'] . ' ' . $value['lname'];
            $value['mobile'] = ltrim(trim($value['mobile']), '0');
            $n[$value['mobile']] = $value['name'] . ' ' . $value['lname'];
        }
        foreach ($mobile as $key => $value) {
            if ($de[$value]) {
                if (!in_array($de[$value][0], array(
                    0,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    8,
                    9,
                    10,
                    11,
                    12
                ))) {
                    if (!empty($de[$value][1])) {
                        switch ($de[$value][1]) {
                            case '0' :
                                $des = 'ارسال شده به مخابرات';
                                break;
                            case '1' :
                                $des = 'رسیده به گوشی';
                                break;
                            case '2' :
                                $des = 'نرسیده به گوشی';
                                break;
                            case '3' :
                                $des = 'خطای مخابراتی';
                                break;
                            case '5' :
                                $des = 'خطای نا مشخص';
                                break;
                            case '8' :
                                $des = 'رسیده به مخابرات';
                                break;
                            case '16' :
                                $des = 'نرسیده به مخابرات';
                                break;
                            case '100' :
                            default :
                                $des = 'نا مشخص';
                                break;
                        }
                    } else {
                        $des = "نا مشخص";
                    }
                } else {
                    $des = "ارسال نشده - {$de[$value][0]}";
                }
                $des = ' - ' . $des;
            }
            if (array_key_exists($value, $n)) {
                $name = $n[$value];
                echo "<li><a title='فیلتر سازی پیغام ها بر اساس این گیرنده' href='admin.php?page=rahrayan_smessages&search={$value}'>{$value} - {$name}{$des}</a></li>";
            } else
                echo "<li><a title='فیلتر سازی پیغام ها بر اساس این گیرنده' href='admin.php?page=rahrayan_smessages&search={$value}'>{$value}{$des}</a></li>";
        }
    } else {
        foreach ($mobile as $key => $value) {
            if ($de[$value]) {
                if (!in_array($de[$value][0], array(
                    0,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    8,
                    9,
                    10,
                    11,
                    12
                ))) {
                    if (!empty($de[$value][1])) {
                        switch ($de[$value][1]) {
                            case '0' :
                                $des = 'ارسال شده به مخابرات';
                                break;
                            case '1' :
                                $des = 'رسیده به گوشی';
                                break;
                            case '2' :
                                $des = 'نرسیده به گوشی';
                                break;
                            case '3' :
                                $des = 'خطای مخابراتی';
                                break;
                            case '5' :
                                $des = 'خطای نا مشخص';
                                break;
                            case '8' :
                                $des = 'رسیده به مخابرات';
                                break;
                            case '16' :
                                $des = 'نرسیده به مخابرات';
                                break;
                            case '100' :
                            default :
                                $des = 'نا مشخص';
                                break;
                        }
                    } else {
                        $des = "نا مشخص";
                    }
                } else {
                    $des = "ارسال نشده - {$de[$value][0]}";
                }
                $des = ' - ' . $des;
            }
            echo "<li><a title='فیلتر سازی پیغام ها بر اساس این گیرنده' href='admin.php?page=rahrayan_smessages&search={$value}'>{$value}{$des}</a></li>";
        }
    }
    echo "<script>
	function rahrayan_refresh_delivery(id) {
	var id2 = '#refresh' + id;
	if ($(id2).hasClass('working'))
		return false;
	$(id2).empty().addClass('working').append('در حال بارگذاری ...');
	var url = 'index.php?mpsd=' + id + '&width=600&height=400&rebulid=true';
	$.ajax({
		type : 'GET',
		url : url
	}).done(function(data) {
		var classn = '.minfo' + id;
		$(classn).hide().empty().append(data).fadeIn();
		$(id2).removeClass('working');
	}).fail(function(data) {
		alert('مشکلی پیش آمد. مجددا تلاش کنید.');
		$(id2).empty().removeClass('working').append('بروز رسانی وضعیت تحویل');
	});
}
	</script>";
    if (!empty($de['time']))
        echo '<a style="cursor:pointer" id="refresh' . $id . '" onClick="rahrayan_refresh_delivery(\'' . $id . '\');">بروز رسانی وضعیت تحویل</a>';
    if (!isset($_GET['rebulid']))
        echo '</ul></div></div>';
    else
        echo '</ul>';
    exit;
}
//show detail2
if (is_admin() && $rahrayan->access() && isset($_GET['mprd'])) {
    if (!$rahrayan->credit)
        die('0');
    $url = plugins_url('templates/assets/', __FILE__);
    $mobile = rahrayan_clean($_GET['mprd']);
    $id = rahrayan_clean($_GET['id']);
    echo '<div style="direction:rtl;text-align:right;font-family:Yekan"><h2>مشاهده اطلاعات ارسال کننده پیامک شماره ' . $id . '</h2><ul>';
    $m = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_members WHERE mobile LIKE '%{$mobile}'", ARRAY_A);
    if ($m[0]) {
        $name = $m[0]['name'] . ' ' . $m[0]['lname'];
        echo "<li>{$mobile} - {$name}</li>";
    } else {
        echo "<li>{$mobile}</li>";
    }
    echo '</ul></div>';
    exit;
}
//add new user to phonebook or delete user from phonebook
if (isset($_POST['mpadn'])) {
    @session_start();
    if (!$rahrayan->credit)
        die('0');
    if (!isset($_POST['code']))
        unset($_SESSION['rahrayan_code']);
    $name = rahrayan_clean($_POST['name']);
    $lname = rahrayan_clean($_POST['lname']);
    $mobile = rahrayan_clean($_POST['mobile']);
    $gender = rahrayan_clean($_POST['gender']);
    $group = (int)$_POST['group'];
    $code = rahrayan_clean($_POST['code']);
    if (empty($name) || empty($lname))
        die('1');
    if (!preg_match("/^09([0-9]{9})$/", $mobile))
        die('2');
    if ($gender != 1 && $gender != 2)
        die('3');
    $g = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_groups WHERE gid = '{$group}'", ARRAY_A);
    if (!$g[0]['gid'])
        die('4');
    if (!empty($_SESSION['rahrayan_code']) && empty($code))
        die('5');
    $m = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_members WHERE mobile = '{$mobile}'", ARRAY_A);
    $scode = get_option('rahrayan_code');
    if ($m[0]['id']) {
        if ($scode) {
            if (!isset($_SESSION['rahrayan_code'])) {
                $mcode = 'remove/code';
            } else {
                if (empty($code))
                    die('6');
                if ($_SESSION['rahrayan_code'] == $code)
                    $delete = true;
                else
                    die('incorrect');
            }
        } else {
            $delete = true;
        }
    } else {
        if ($scode) {
            if (!isset($_SESSION['rahrayan_code'])) {
                $mcode = 'add/code';
            } else {
                if (empty($code))
                    die('7');
                if ($_SESSION['rahrayan_code'] == $code)
                    $new = true;
                else
                    die('incorrect');
            }
        } else {
            $new = true;
        }
    }
    $uname = $name . ' ' . $lname;
    $code2 = rand(1000, 9999);
    $ugender = ($gender == 1) ? 'خانم' : 'آقای';
    if ($mcode) {
        $_SESSION['rahrayan_code'] = $code2;
        $sms = str_replace(array(
            '{name}',
            '{gender}',
            '{code}'
        ), array(
            $uname,
            $ugender,
            $code2
        ), get_option('rahrayan_code_text'));
        if ($rahrayan->send(array($mobile), $sms))
            die($mcode);
        else
            die('8');
    }
    if ($delete) {
        unset($_SESSION['rahrayan_code']);
        $id = $wpdb->query("DELETE FROM {$table_prefix}rahrayan_members WHERE mobile = '{$mobile}'");
        if ($id) {
            $sync = (get_option('rahrayan_sync')) ? 1 : 0;
            if ($sync == 1) {
                //$rahrayan->call('RemoveContact', 'contacts', array('mobilenumber' => $mobile));
            }
            die('deleted');
        } else {
            die('0');
        }
    }
    if ($new) {
        unset($_SESSION['rahrayan_code']);
        $sync = (get_option('rahrayan_sync')) ? 1 : 0;
        $date = $rahrayan->date();
        $id = $wpdb->insert($table_prefix . 'rahrayan_members', array(
            'sync' => 0,
            'status' => 1,
            'gid' => $group,
            'mobile' => $mobile,
            'gender' => $gender,
            'name' => $name,
            'lname' => $lname,
            'date' => $date
        ));
        if ($id) {
            $id = $wpdb->insert_id;
            if ($sync == 1) {
                $sync = $rahrayan->call('CheckMobileExistInContact', 'contacts', array('mobileNumber' => $mobile));
                if ($sync == 1)
                    $rahrayan->call('RemoveContact', 'contacts', array('mobilenumber' => $mobile));
                $sync = $rahrayan->call('AddContact', 'contacts', array(
                    'lastname' => $lname,
                    'gender' => $gender,
                    'groupIds' => get_option('rahrayan_group'),
                    'firstname' => $name,
                    'mobilenumber' => $mobile,
                    'phone' => '',
                    'fax' => '',
                    'email' => '',
                    'postalCode' => '',
                    'address' => '',
                    'city' => '',
                    'province' => '',
                    'birthdate' => '23:59:59.9999999',
                    'additionaldate' => '23:59:59.9999999'
                ));
                if ($sync == 1)
                    $wpdb->query("UPDATE {$table_prefix}rahrayan_members SET sync = 1 WHERE id = '{$id}' ");
            }
            if (get_option('rahrayan_welcome')) {
                $sms = str_replace(array(
                    '{name}',
                    '{gender}',
                    '{mobile}',
                    '{date}'
                ), array(
                    $uname,
                    $ugender,
                    $mobile,
                    $date
                ), get_option('rahrayan_welcome_text'));
                $rahrayan->send(array($mobile), $sms);
            }
            if (get_option('rahrayan_nregister')) {
                $sms = str_replace(array(
                    '{username}',
                    '{mobile}',
                    '{date}'
                ), array(
                    $uname,
                    $mobile,
                    $date
                ), get_option('rahrayan_nregister_text'));
                $rahrayan->send(explode(';', get_option('rahrayan_admin')), $sms);
            }
            die('added');
        } else {
            die('0');
        }
    }
    exit;
}
//show mini form
if (isset($_GET['rahrayan_mini'])) {
    include dirname(__FILE__) . '/templates/form_mini.php';
    exit;
}
//show large form
if (isset($_GET['rahrayan_large'])) {
    include dirname(__FILE__) . '/templates/form_large.php';
    exit;
}
//in a glance
function rahrayan_glance()
{
    global $rahrayan;
    echo '<style>#rahrayan1 a:before{content: "\f110" !important;}#rahrayan2 a:before{content: "\f173" !important;}</style>';
    echo "<li id='rahrayan1'><a href='admin.php?page=rahrayan'>" . number_format($rahrayan->count) . " مشترک</a></li>";
    echo "<li id='rahrayan2'><a href='admin.php?page=rahrayan'>" . number_format($rahrayan->credit) . " موجودی پیامک</a></li>";
}

if ($rahrayan->credit && $rahrayan->access())
    add_action('dashboard_glance_items', 'rahrayan_glance');
//mobile field
if (get_option('rahrayan_mfield')) {
    function rahrayan_mfieldr()
    {
        $mobile = rahrayan_clean($_REQUEST['mpmobile']);
        echo "<p><label for='mpmobile'>شماره موبایل شما<input type='text' size='25' name='mpmobile' id='mpmobile' class='input' value='{$mobile}'/></label></p>";
    }

    function rahrayan_mfield($field)
    {
        $field['mpmobile'] = 'شماره موبایل';
        return $field;
    }

    function rahrayan_mfield_add_new_user($user)
    {
        echo '<table class="form-table"><tr><th><label for="mpmobile">شماره موبایل</label></th><td><input type="text" class="regular-text" name="mpmobile" value="' . esc_attr(get_the_author_meta('company', $user->ID)) . '" id="mpmobile" /></tr></table>';
    }

    function rahrayan_mfielde($error, $login, $email)
    {
        if (empty($_POST['mpmobile']) || !preg_match("/^09([0-9]{9})$/", $_POST['mpmobile']))
            $error->add('mobile_error', '<strong>خطا</strong>: لطفا یک شماره موبایل معتبر وارد نمایید.');
        else
            $GLOBALS['rahrayan_pass'] = true;
        return $error;
    }

    function rahrayan_mfields($id)
    {
        update_user_meta($id, 'mpmobile', rahrayan_clean($_POST['mpmobile']));
        if (get_option('rahrayan_register2')) {
            global $rahrayan;
            $to = array($_POST['mpmobile']);
            $message = get_option('rahrayan_register2_text');
            $info = get_userdata($id);
            $message = str_replace(array(
                '{username}',
                '{email}',
                '{password}',
                '{date}'
            ), array(
                $info->user_login,
                $info->user_email,
                is_string($GLOBALS['rahrayan_pass']) ? $GLOBALS['rahrayan_pass'] : ((is_admin() && isset($_POST['pass1'])) ? $_POST['pass1'] : 'نا معلوم'),
                $rahrayan->date()
            ), $message);
            $rahrayan->send($to, $message);
        }
    }

    function rahrayan_mfieldp($pass)
    {
        if ($GLOBALS['rahrayan_pass'])
            $GLOBALS['rahrayan_pass'] = (string)$pass;
        return $pass;
    }

    add_filter('random_password', 'rahrayan_mfieldp');
    add_action('register_form', 'rahrayan_mfieldr');
    add_filter('user_contactmethods', 'rahrayan_mfield');
    add_filter('registration_errors', 'rahrayan_mfielde', 10, 3);
    add_action('user_profile_update_errors', "rahrayan_mfielde", 10, 3);
    add_action('user_register', 'rahrayan_mfields');
    add_action('user_new_form', 'rahrayan_mfield_add_new_user');
}
//password reset
if (get_option('rahrayan_lostpw')) {

    function rahrayan_lostpw_register($id)
    {
        $GLOBALS['mp_skip_lost_pw'] = true;
    }

    function rahrayan_lostpw($user_login, $key)
    {
        if (isset($GLOBALS['mp_skip_lost_pw']))
            return;
        $user = get_user_by('login', $user_login);
        global $rahrayan;
        $m = get_user_meta($user->ID, 'mpmobile');
        $to = array($m[0]);
        $link = "wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login);
        $message = get_option('rahrayan_lostpw_text');
        $link = get_site_url(null, $link);
        if (strpos($message, '{slink}') !== false) {
            $encoded_link = urlencode($link);
            $result = wp_remote_get("https://api-ssl.bitly.com/v3/shorten?access_token=fcbde0a4817ecaa9b59faed643a7845047890d05&longUrl={$encoded_link}");
            if (isset($result) && !empty($result['body'])) {
                $result = json_decode($result['body'], true);
                $slink = $result['data']['url'];
                if (empty($slink))
                    $slink = $link;
            } else {
                $slink = $link;
            }
        } else
            $slink = '';
        $message = str_replace(array(
            '{username}',
            '{email}',
            '{link}',
            '{date}',
            '{slink}'
        ), array(
            $user->user_login,
            $user->user_email,
            $link,
            $rahrayan->date(),
            $slink
        ), $message);
        $rahrayan->send($to, $message);
    }

    add_action('user_register', 'rahrayan_lostpw_register', 10, 1);
    add_action('retrieve_password_key', "rahrayan_lostpw", 10, 3);
}
//start exporting
if (isset($_POST['group']) && $rahrayan->access() && isset($_GET['mpexport']) && is_admin()) {
    if (wp_verify_nonce($_POST['mpeactionf'], 'mpeaction')) {
        switch ($_POST['group']) {
            case 'all' :
            default :
                if ($_POST['group'] != 'all') {
                    $value = (int)$_POST['group'];
                    $field = 'gid';
                    $where = "WHERE {$table_prefix}rahrayan_members.{$field} = '{$value}'";
                    $name = 'group' . $value;
                } else
                    $name = 'all';
                $members = $wpdb->get_results("SELECT *
FROM {$table_prefix}rahrayan_members
INNER JOIN {$table_prefix}rahrayan_groups
ON {$table_prefix}rahrayan_members.gid = {$table_prefix}rahrayan_groups.gid
{$where}
ORDER BY {$table_prefix}rahrayan_members.id DESC", ARRAY_A);
                $title = array(
                    'موبایل',
                    'نام',
                    'نام خوانوادگی',
                    'جنسیت',
                    'گروه'
                );
                $a = true;
                break;
            case 'users' :
                $data = $wpdb->get_results("SELECT meta_value,user_login,user_email,display_name
FROM {$table_prefix}usermeta
INNER JOIN {$table_prefix}users
ON {$table_prefix}usermeta.user_id = {$table_prefix}users.ID
WHERE {$table_prefix}usermeta.meta_key = 'mpmobile' AND {$table_prefix}usermeta.meta_value != ''
ORDER BY {$table_prefix}users.ID DESC", ARRAY_N);
                $name = 'users';
                $title = array(
                    'موبایل',
                    'نام کاربری',
                    'ایمیل',
                    'نام نمایشی'
                );
                break;
        }
        if ($a) {
            $data = array();
            foreach ($members as $key => $value) {
                $g = ($value['gender'] == 1) ? 'زن' : 'مرد';
                $data[] = array(
                    $value['mobile'],
                    $value['name'],
                    $value['lname'],
                    $g,
                    $value['gname']
                );
            }
        }
        $name = $name . '-' . $rahrayan->date() . '.' . $_POST['format'];
        switch ($_POST['format']) {
            case 'txt' :
                $content = array();
                $content[] = implode('-', $title);
                foreach ($data as $key => $value) {
                    $content[] = implode('-', $value);
                }
                $content = implode("\n", $content);
                break;
            case 'csv' :
                $content = rahrayan_array2csv($title, $data);
        }
        if ($content) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Content-Length: ' . strlen($content));
            echo $content;
            exit;
        }
    }
}
