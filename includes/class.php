<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

class rahrayan
{
    public $groups = array(), $is_ready = false, $hasnt = false, $update, $username, $password, $tel, $credit = false, $connection = true, $date, $ip, $count = 0;

    //getting ready
    public function __construct()
    {
		include_once('nusoap/nusoap.php');
        global $rahrayan_version, $wpdb, $table_prefix;
        //should force update informations?
        if (isset($_REQUEST['rahrayan_update']) && is_admin())
            $force_update = true;
        //set update period time
        $update_period = (intval(get_option('rahrayan_update_period') >= 1) && intval(get_option('rahrayan_update_period') < 13)) ? get_option('rahrayan_update_period') : 6;
        $this->username = get_option('rahrayan_username');
        $this->password =get_option('rahrayan_password');
        $this->tel = get_option('rahrayan_tel');
	
        if ($this->access())
            $this->count = $wpdb->get_var("SELECT count(id) FROM {$table_prefix}rahrayan_members");
        if (empty($this->username) || empty($this->password) || empty($this->tel)) {
            return false;
        } else {
            if (class_exists('soapclient_nu')) {
                @ini_set("soap.wsdl_cache_enabled", "0");
                $this->set_credit();
                if ($this->credit == '') {
                    $this->connection = false;
                    return false;
                } else {
                    if (intval($this->credit) === 0) {
                        return false;
                    } else {
                        $this->is_ready = true;
                        if (is_admin()) {
                            
                            return true;
                        } else {
                            $this->hasnt = 'JSON';
                            return false;
                        }
                    }
                }
            } else {
                $this->hasnt = 'SoapClient';
                return false;
            }
        }
    }

    //call to rahrayan
    public function call($action, $name, $parameters = array(), $r = true)
    {
        try {
			//file_put_contents("1.txt",file_get_contents("1.txt")+1);
            $sms_client = new soapclient_nu('http://www.5m5.ir/webservice/soap/smsService.php?wsdl', 'wsdl');
			$sms_client->soap_defencoding = 'UTF-8';
			$sms_client->decode_utf8 = false;
			$err = $sms_client->getError();
			if ($err){
				//print_r(  'error'.$err);
				return false;
			}
            $parameters['username'] = $this->username;
            $parameters['password'] = $this->password;
            $result = $sms_client->call($action,$parameters);
            
				//file_put_contents("7.txt",json_encode([$action,$parameters,$result]));
            
			$rr = $sms_client->getError();
			
            if ($rr){
                //print_r(  'error'.$err);
				return false;
			}
            else{
                return $result;
				
			}
        } catch (SoapFault $ex) {
            if (defined('WP_DEBUG') AND WP_DEBUG === true)
                echo $ex;
            return false;
        }
    }

    //check premission
    public function check_premission()
    {
        if (!$this->access()) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (defined('RAHRAYAN_READY') && !$this->is_ready) {
            if (!$this->connection)
                wp_die('در برقراری ارتباط با ره رایان پیامک مشکلی پیش آمد، پلاگین قادر به ادامه فعالیت نیست.<br/><a href="admin.php?page=rahrayan_setting&tab=webservice">برای تلاش مجدد کلیک کنید.</a> همچنین ممکن است حساب شما منقضی شده باشد یا اعتبار شما صفر باشد.');
            else
                wp_die('<a href="admin.php?page=rahrayan_setting&tab=webservice">لطفا جهت ادامه فعالیت پلاگین، تنظیمات وب سرویس پلاگین را بررسی نمایید.</a>');
        }
    }

    //check access
    public function access()
    {
        return current_user_can('manage_options');
    }

    //fetch rahrayan groups
    public function fetch_groups($show, $selected)
    {
        $this->update_groups();
        $groups = get_option('rahrayan_groups');
        $groups = $groups[1];
        if ($groups['GroupID']) {
            $b = $groups;
            unset($groups);
            $groups[] = $b;
        }
        if ($show) {
            if ($groups[0]) {
                $return = "<select style='width: 200px;height: 34px;' name='rahrayan_group' required>";
                foreach ($groups as $key => $value) {
                    $select = '';
                    if ($value['id'] == $selected)
                        $select = 'selected';
                    $return .= "<option value='{$value['id']}' {$select}>{$value['title']}</option>";
                }
                return $return . '</select>';
            } else {
                return "<select style='width: 200px;height: 34px;' name='rahrayan_group' required><option disabled value=''>هیچ گروهی یافت نشد.</option></select>";
            }
        } else {
            return $groups;
        }
    }

    //phonebook groups
    public function fetch_phonebook_groups($show, $selected = null, $select = true, $where = false)
    {
        global $table_prefix, $wpdb;
        if (!$GLOBALS['mpcache']) {
            if ($where)
                $where = "WHERE gshow = '1'";
            $GLOBALS['mpcache'] = $wpdb->get_results("SELECT * FROM {$table_prefix}rahrayan_groups {$where} ORDER BY gid DESC", ARRAY_A);
        }
        $groups = $GLOBALS['mpcache'];
        if ($show) {
            if ($groups[0]) {
                if ($select)
                    $return = "<select  name='group' required>";
                else
                    $return = '';
                foreach ($groups as $key => $value) {
                    $select = '';
                    if ($value['gid'] == $selected)
                        $select = 'selected';
                    $return .= "<option value='{$value['gid']}' {$select}>{$value['gname']}</option>";
                }
                if ($select)
                    return $return . '</select>';
                else
                    return $return;
            } else {
                if ($select)
                    return "<select  name='group' required><option disabled value=''>هیچ گروهی یافت نشد.</option></select>";
                else
                    return null;
            }
        } else {
            return $groups;
        }
    }

    //update groups
    public function update_groups()
    {
        $result = $this->call('sms_get_cat_list', 'contacts');
        if (!empty($result)) {
            $result = rahrayan_o2a($result);
            update_option('rahrayan_groups', array(
                time(),
                $result
            ));
			//print_r(get_option('rahrayan_groups'));
        }
    }

    //shamsi date
    public function date($time = null, $format = 'Y/m/d H:i', $tz = null)
    {
        if (!$time)
            $time = time();
        if (!is_object($this->date))
            $this->date = new jDateTime(false, true, 'Asia/Tehran');

        return $this->date->date($format, $time);
    }

    //send sms
    public function send($to, $message, $is_auto = 1, $flash = false, $update = true)
    {
        if (!$message || !$to[0])
            return false;
        $message = html_entity_decode($message);
        if (count($to) == 1) {
            $to[0] = str_replace(array(
              
                '+',
                '-'
            ), '', $to[0]);
        }
        global $wpdb, $table_prefix;
        $message = ($is_auto == 1) ? $message . "\n" . get_option('rahrayan_sig') : $message;
        $parameters = array();
        $parameters['sender_number'] = array($this->tel);
        $parameters['receiver_number'] = $to;
        $parameters['note'] = array($message);
        $parameters['onlysend'] = 'yes';
        $parameters['ersal_flash'] = $flash?"yes":"no";
      

        
			$result = $this->call('send_sms', 'send', $parameters);
				
            $time = $this->date();
            $tob = $to;
            $to = rahrayan_clean(implode(',', $to));
            $message = rahrayan_clean($message);
            $de = array();
            $de['time'] = time();
            $flash = ($flash) ? 1 : 0;
            if (is_array($result)) {
                foreach ($result as $key => $value) {
                    $de[$tob[$key]] = array(
                        $value,
                        ''
                    );
                }
                $de = json_encode($de);
                $wpdb->insert($table_prefix . 'rahrayan_messages', array(
                    'flash' => $flash,
                    'date' => $time,
                    'message' => $message,
                    'sender' => $this->tel,
                    'recipient' => $to,
                    'mode' => $is_auto,
                    'delivery' => $de
                    
                ));
                if ($update)
                    $this->set_credit(true);
                return true;
            } else {
                foreach (explode(',', $result) as $key => $value) {
                    $de[$tob[$key]] = array(
                        $value,
                        ''
                    );
                }
                $de = json_encode($de);
                $wpdb->insert($table_prefix . 'rahrayan_messages', array(
                    'flash' => $flash,
                    'date' => $time,
                    'message' => $message,
                    'sender' => $this->tel,
                    'recipient' => $to,
                    'mode' => $is_auto,
                    'delivery' => $de,
                   
                ));
                if (!in_array($result, array(
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
                    if ($update)
                        $this->set_credit(true);
                    return true;
                } else
                    return false;
            }
        
    }

    //set user credit
    public function set_credit($force = false)
    {
        global $wpdb;
        $update_period = (intval(get_option('rahrayan_update_period') >= 1)) ? get_option('rahrayan_update_period') : 6;
        if (((isset($_REQUEST['rahrayan_update']) || (isset($_REQUEST['page']) && $_REQUEST['page'] == 'rahrayan_setting' && $_REQUEST['tab'] == 'webservice')) && is_admin()) || $force)
            $force_update = true;
        if (isset($_GET['page']) && $_GET['page'] == 'rahrayan_send' && is_admin())
            $force_update = true;
        if (isset($_GET['page']) && $_GET['page'] == 'rahrayan_reports' && is_admin())
            $force_update = true;
        $now = time();
        $credit = get_option('rahrayan_credit');
        $last = $credit[0];
        if ($now - ($update_period * 60 * 60) > $last || isset($force_update)) {
            $credit = $this->call('sms_credit', 'Send');
            update_option('rahrayan_credit', array(
                time(),
                $credit
            ));
            $this->credit = $credit;
        } else {
            $this->credit = $credit[1];
        }
    }

    //create a string to show for user in correct way :)
    public function nl2br($string)
    {
        $string = str_replace(array(
            '<br/>',
            '<br />',
            '<br>',
            '</br>'
        ), '%rahrayan_br%', $string);
        $string = trim($string);
        $string = htmlspecialchars($string, ENT_QUOTES);
        $string = str_replace(array(
            '\r\n',
            '\r',
            '\n'
        ), "<br/>", $string);
        $string = stripslashes($string);
        $string = " " . $string;
        $string = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $string);
        $string = str_replace('%rahrayan_br%', '<br/>', $string);
        $string = str_replace(array(
            '<br/>',
            '<br />',
            '<br>',
            '</br>'
        ), '<br/>', $string);
        $string = str_replace(array(
            '<br/><br/>',
            '<br/><br/><br/>',
            '<br/><br/><br/><br/>'
        ), '<br/>', $string);
        return nl2br(stripslashes($string));
    }

    public function nl2br2($string)
    {
        $string = trim($string);
        $string = str_replace(array(
            '\r\n',
            '\r',
            '\n'
        ), "<br/>", $string);
        $string = str_replace(array(
            '<br/>',
            '<br />',
            '<br>',
            '</br>'
        ), '<br/>', $string);
        $string = str_replace(array(
            '<br/><br/>',
            '<br/><br/><br/>',
            '<br/><br/><br/><br/>'
        ), '<br/>', $string);
        return nl2br($string);
    }

}
