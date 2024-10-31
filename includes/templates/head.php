<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('main-css', $url . 'style.css', true, $rahrayan_version);
wp_enqueue_script('main-js', $url . 'admin.js', true, $rahrayan_version);


//switching system error
if (!empty($rahrayan->hasnt))
    $error = "{$rahrayan -> hasnt} بر روی سرور شما یافت نشد. پلاگین قادر به فعالیت نیست.";
if (!$rahrayan->connection)
    $error = 'در برقراری ارتباط با ره رایان پیامک مشکلی پیش آمد، پلاگین قادر به ادامه فعالیت نیست.<br/><a href="admin.php?page=rahrayan_setting&tab=webservice">برای تلاش مجدد کلیک کنید.</a> همچنین ممکن است حساب شما منقضی شده باشد یا اعتبار شما صفر باشد.';
if (!$rahrayan->is_ready && $rahrayan->connection)
    $error = "جهت استفاده از پلاگین لطفا اطلاعات مربوط به پنل ره رایان‌پیامک خود را در <a href='admin.php?page=rahrayan_setting&tab=webservice'>تنظیمات</a> وارد کنید. جهت تهیه پنل پیامک به <a target='_blank' href=\"http://rahco.ir\">rahco.ir</a> مراجعه کنید.";
if ($error)
    $error = "<div class='error'><p>{$error}</p></div>";
if ($acerror)
    $acerror = "<div class='error'><p>{$acerror}</p></div>";
if ($acok) {
    if (is_array($acok)) {
        if (intval($acok[0]) == 0) {
            $acok = '<div class="error"><p>هیچ موردی ' . $acok[1] . ' نشد.</p></div>';
        } else {
            $acok = '<div id="message" class="updated below-h2"><p>تعداد ' . $acok[0] . ' مورد ' . $acok[1] . ' شد.</p></div><br/>';
        }
    } else
        $acok = '<div id="message" class="updated below-h2"><p>انجام شد.</p></div><br/>';
}
if ($acwarning)
    $acwarning = "<div class='error'><p>{$acwarning}</p></div>";
$update = $rahrayan->update;
if (empty($update) && empty($error) && empty($acerror) && empty($acwarning))
    $update = '<br/>';
?>
    <div class="mp_cover">
        <div class="content">
            لطفا صبر کنید...
        </div>
    </div>
    <div id="rahrayan">
    
    <div class="mp_title">
        <?php echo $title
        ?>
    </div>
    <div class="clear"></div>
<?php echo $error;
echo $update;
echo $acwarning;
echo $acerror;
echo $acok;
?>