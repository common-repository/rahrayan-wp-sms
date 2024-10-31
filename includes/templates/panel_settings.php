<br/>
<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$title = 'تنظیمات ره رایان پیامک';
$url = plugins_url('../', __FILE__);
include dirname(__FILE__) . '/head.php';
?>
<script>
	var $ = jQuery;
$(document).ready(function() {
	//$('#rahrayan-ibg').wpColorPicker();
	$('#rahrayan-cp').wpColorPicker();
});
</script>
<h2 class="nav-tab-wrapper"><a href="?page=rahrayan_setting" class="nav-tab <?php
if ($_GET['tab'] == '') { echo "nav-tab-active";
}
?>">عمومی</a><a href="?page=rahrayan_setting&tab=webservice" class="nav-tab <?php
if ($_GET['tab'] == 'webservice') { echo "nav-tab-active";
}
?>">وب سرویس</a><a href="?page=rahrayan_setting&tab=notifications" class="nav-tab <?php
if ($_GET['tab'] == 'notifications') { echo "nav-tab-active";
}
?>">اطلاع رسانی ها</a><a href="?page=rahrayan_setting&tab=newsletter" class="nav-tab <?php
if ($_GET['tab'] == 'newsletter') { echo "nav-tab-active";
}
?>">خبرنامه</a><a href="?page=rahrayan_setting&tab=form" class="nav-tab <?php
if ($_GET['tab'] == 'form') { echo "nav-tab-active";
}
?>">شخصی سازی فرم خبرنامه</a>
<a href="?page=rahrayan_setting&tab=woocommerce" class="nav-tab <?php
if ($_GET['tab'] == 'woocommerce') { echo "nav-tab-active";
}
?>">ووکامروس</a></h2>
<?php
if(isset($_GET['settings-updated']))
echo '<div id="message" class="updated below-h2"><p>انجام شد.</p></div><br/>'
?>
<table class="form-table">
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<?php
switch($_GET['tab']) {
case 'webservice' :
		?>
		<tr>
			<td>نام کاربری ره رایان پیامک</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="rahrayan_username" value="<?php echo get_option('rahrayan_username'); ?>" required/>
			<p class="description">
				نام کاربری خود را در ره رایان پیامک وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>کلمه عبور ره رایان پیامک</td>
			<td>
			<input type="password" style="width: 200px;" name="rahrayan_password" value='<?php echo get_option('rahrayan_password'); ?>' required/>
			<p class="description">
				کلمه عبور خود را در ره رایان پیامک وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>شماره ارسال کننده پیامک</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="rahrayan_tel" value="<?php echo get_option('rahrayan_tel'); ?>" required/>
			<p class="description">
				شماره ارسال کننده پیامک را وارد نمایید.
			</p></td>
		</tr>
		<input type="hidden" name="page_options" value="rahrayan_tel,rahrayan_password,rahrayan_username" />
		<?php
		break;
		case 'newsletter' :
	    ?>
	    <tr>
			<td>تایید اشتراک یا لغو اشتراک با ارسال کد؟</td>
			<td>
			<input type="checkbox" name="rahrayan_code" id="rahrayan_code" <?php echo get_option('rahrayan_code') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_code">برای تایید اشتراک  یا لغو اشتراک کاربر، کد تایید ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_code') == false ? 'style="display:none"' : ''; ?> id="rahrayan_code_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_code_text"><?php echo get_option('rahrayan_code_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کامل کاربر: <code>{name}</code> جنسیت کاربر به صورت آقای یا خانم: <code>{gender}</code>
						کد فعالسازی: <code>{code}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیغام خوش آمد گویی</td>
			<td>
			<input type="checkbox" name="rahrayan_welcome" id="rahrayan_welcome" <?php echo get_option('rahrayan_welcome') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_welcome">پس از عضویت برای کاربر پیغام خوش آمد گویی ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_welcome') == false ? 'style="display:none"' : ''; ?> id="rahrayan_welcome_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_welcome_text"><?php echo get_option('rahrayan_welcome_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کامل کاربر: <code>{name}</code> جنسیت کاربر به صورت آقای یا خانم: <code>{gender}</code>
						تاریخ عضویت: <code>{date}</code> شماره موبایل: <code>{mobile}</code>
					</p>
				</td>
			</tr>
		<input type="hidden" name="page_options" value="rahrayan_code,rahrayan_jquery,rahrayan_welcome_text,rahrayan_welcome,rahrayan_code_text" />
		<?php
		break;
		case 'woocommerce' :
		?>
		<tr>
			<td>ارسال پیام به مدیر هنگام سفارش جدید</td>
			<td>
			<input type="checkbox" name="rahrayan_wc" id="rahrayan_wc" <?php echo get_option('rahrayan_wc') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_wc">هنگام ثبت سفارش جدید در WooCommerce به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_wc') == false ? 'style="display:none"' : ''; ?> id="rahrayan_wc_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_wc_text"><?php echo get_option('rahrayan_wc_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام سفارش جدید</td>
			<td>
			<input type="checkbox" name="rahrayan_wc2" id="rahrayan_wc2" <?php echo get_option('rahrayan_wc2') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_wc2">هنگام ثبت سفارش جدید در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_wc2') == false ? 'style="display:none"' : ''; ?> id="rahrayan_wc2_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_wc2_text"><?php echo get_option('rahrayan_wc2_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
                </p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام تغییر وضعیت سفارش به در حال پردازش</td>
			<td>
			<input type="checkbox" name="rahrayan_wc3" id="rahrayan_wc3" <?php echo get_option('rahrayan_wc3') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_wc3">هنگام تغییر وضعیت سفارش به در حال پردازش در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_wc3') == false ? 'style="display:none"' : ''; ?> id="rahrayan_wc3_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_wc3_text"><?php echo get_option('rahrayan_wc3_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
                </p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام تکمیل سفارش</td>
			<td>
			<input type="checkbox" name="rahrayan_wc4" id="rahrayan_wc4" <?php echo get_option('rahrayan_wc4') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_wc4">هنگام تکمیل سفارش در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_wc4') == false ? 'style="display:none"' : ''; ?> id="rahrayan_wc4_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_wc4_text"><?php echo get_option('rahrayan_wc4_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code>
                </p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیام به کاربر هنگام نوشتن یادداشت برای سفارش</td>
			<td>
			<input type="checkbox" name="rahrayan_wc5" id="rahrayan_wc5" <?php echo get_option('rahrayan_wc5') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_wc5">هنگام نوشتن یادداشت برای سفارش در WooCommerce به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_wc5') == false ? 'style="display:none"' : ''; ?> id="rahrayan_wc5_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_wc5_text"><?php echo get_option('rahrayan_wc5_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
                <p class="description">
                    متغیر های قابل استفاده:	آیدی سفارش: <code>{id}</code>  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> وضعیت: <code>{status}</code>  مبلغ: <code>{price}</code> آیتم‌های سفارش: <code>{items}</code>  شماره تراکنش: <code>{transaction_id}</code> متن پیغام: <code>{text}</code>
                </p>
				</td>
			</tr>
            <tr>
                <td>تایید شماره موبایل کاربران قبل از ثبت سفارش</td>
                <td>
                    <input type="checkbox" name="rahrayan_wc_mobile_verification" id="rahrayan_wc_mobile_verification" <?php echo get_option('rahrayan_wc_mobile_verification') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="rahrayan_wc_mobile_verification">قبل از ثبت سفارش، شماره موبایل کاربر تایید شود؟</label></td>
            </tr>
            <tr <?php echo get_option('rahrayan_wc_mobile_verification') == false ? 'style="display:none"' : ''; ?> id="rahrayan_wc_mobile_verification_text">
                <td scope="row">
                    متن پیامک
                </td><td>
                    <textarea cols="50"  rows="7" name="rahrayan_wc_mobile_verification_text"><?php echo get_option('rahrayan_wc_mobile_verification_text'); ?></textarea>
                    <p class="description">متن پیامک را وارد نمایید.</p>
                    <p class="description">
                        متغیر های قابل استفاده:	  تاریخ: <code>{date}</code> نام: <code>{first_name}</code>  نام‌خانوادگی: <code>{last_name}</code> کد تایید: <code>{code}</code>
                    </p>
                </td>
            </tr>
				<input type="hidden" name="page_options" value="rahrayan_wc,rahrayan_wc_text,rahrayan_wc2,rahrayan_wc2_text,rahrayan_wc3,rahrayan_wc3_text,rahrayan_wc4,rahrayan_wc4_text,rahrayan_wc5,rahrayan_wc5_text,rahrayan_wc_mobile_verification,rahrayan_wc_mobile_verification_text" />
	    <?php
		break;
		case 'notifications' :
		?>
		<tr>
			<td>ارسال پست های جدید به کاربران</td>
			<td>
			<input type="checkbox" name="rahrayan_send" id="rahrayan_send" <?php echo get_option('rahrayan_send') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_send">پست های جدید به عضو های خبرنامه ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_send') == false ? 'style="display:none"' : ''; ?> id="rahrayan_send_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_send_text"><?php echo get_option('rahrayan_send_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						عنوان نوشته: <code>{title}</code> تاریخ نوشته: <code>{date}</code>
						آدرس نوشته: <code>{url}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیامک هنگام نام نویسی کاربر به مدیر سایت</td>
			<td>
			<input type="checkbox" name="rahrayan_register" id="rahrayan_register" <?php echo get_option('rahrayan_register') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_register">هنگام نام نویسی کاربر جدید به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_register') == false ? 'style="display:none"' : ''; ?> id="rahrayan_register_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_register_text"><?php echo get_option('rahrayan_register_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ عضویت: <code>{date}</code>
						ایمیل کاربر: <code>{email}</code>
					</p>
				</td>
			</tr>
			<?php if(get_option('rahrayan_mfield') == true){ ?>
			<tr>
			<td>ارسال پیامک هنگام نام نویسی کاربر به کاربر</td>
			<td>
			<input type="checkbox" name="rahrayan_register2" id="rahrayan_register2" <?php echo get_option('rahrayan_register2') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_register2">هنگام نام نویسی کاربر جدید به کاربر پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_register2') == false ? 'style="display:none"' : ''; ?> id="rahrayan_register2_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_register2_text"><?php echo get_option('rahrayan_register2_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ عضویت: <code>{date}</code>
						ایمیل کاربر: <code>{email}</code> کلمه عبور: <code>{password}</code>
					</p>
				</td>
			</tr>
			<tr>
			<td>ارسال پیامک هنگام درخواست برای بازیابی رمز عبور</td>
			<td>
			<input type="checkbox" name="rahrayan_lostpw" id="rahrayan_lostpw" <?php echo get_option('rahrayan_lostpw') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_lostpw">ارسال لینک بازیابی رمز عبور هنگام درخواست کاربر برای بازیابی رمز عبور</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_lostpw') == false ? 'style="display:none"' : ''; ?> id="rahrayan_lostpw_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_lostpw_text"><?php echo get_option('rahrayan_lostpw_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ درخواست: <code>{date}</code>
						ایمیل کاربر: <code>{email}</code> لینک بازیابی: <code>{link}</code>  لینک کوتاه شده بازیابی: <code>{slink}</code>
					</p>
				</td>
			</tr>
			<?php } ?>
		<tr>
			<td>ارسال پیامک هنگام ورود کاربر</td>
			<td>
			<input type="checkbox" name="rahrayan_login" id="rahrayan_login" <?php echo get_option('rahrayan_login') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_login">هنگام ورود کاربر به سایت به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_login') == false ? 'style="display:none"' : ''; ?> id="rahrayan_login_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_login_text"><?php echo get_option('rahrayan_login_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربری: <code>{username}</code> تاریخ: <code>{date}</code>
					</p>
				</td>
			</tr>
            </tr>
            <tr>
                <td>ورود ۲مرحله‌ای</td>
                <td>
                    <input type="checkbox" name="rahrayan_2fa" id="rahrayan_2fa" <?php echo get_option('rahrayan_2fa') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="rahrayan_2fa">فعال کردن ورود ۲مرحله‌ای برای کاربران با وارد کردن کد تایید<br/>جهت استفاده از این مورد، نصب بودن پلاگین <a target="_blank" href="https://wordpress.org/plugins/two-factor/">Two-Factor</a> لازم می‌باشد. همچنین از فعال بودن دریافت شماره موبایل کاربران اطمینان حاصل کنید.</label></td>
            </tr>
            <tr <?php echo get_option('rahrayan_2fa') == false ? 'style="display:none"' : ''; ?> id="rahrayan_2fa_text">
                <td scope="row">
                    متن پیامک
                </td><td>
                    <textarea cols="50"  rows="7" name="rahrayan_2fa_text"><?php echo get_option('rahrayan_2fa_text'); ?></textarea>
                    <p class="description">متن پیامک را وارد نمایید.</p>
                    <p class="description">
                        متغیر های قابل استفاده:						کد تایید: <code>{token}</code>  تاریخ: <code>{date}</code>
                    </p>
                </td>
            </tr>
			<tr>
			<td>ارسال پیامک هنگام ثبت نام کاربر جدید در خبرنامه</td>
			<td>
			<input type="checkbox" name="rahrayan_nregister" id="rahrayan_nregister" <?php echo get_option('rahrayan_nregister') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_nregister">هنگام ثبت نام کاربر جدید در خبرنامه به شما پیامک ارسال شود؟</label></td>
		<tr <?php echo get_option('rahrayan_nregister') == false ? 'style="display:none"' : ''; ?> id="rahrayan_nregister_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_nregister_text"><?php echo get_option('rahrayan_nregister_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نام کاربر: <code>{username}</code>  شماره موبایل: <code>{mobile}</code>  تاریخ: <code>{date}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>ارسال پیامک هنگام ثبت دیدگاه جدید</td>
			<td>
			<input type="checkbox" name="rahrayan_comment" id="rahrayan_comment" <?php echo get_option('rahrayan_comment') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_comment">هنگام ثبت دیدگاه تازه به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_comment') == false ? 'style="display:none"' : ''; ?> id="rahrayan_comment_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_comment_text"><?php echo get_option('rahrayan_comment_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p>
					<p class="description">
						متغیر های قابل استفاده:						نویسنده: <code>{author}</code> ایمیل: <code>{email}</code> آیپی: <code>{ip}</code> تاریخ: <code>{date}</code>
						 متن دیدگاه: <code>{comment}</code>  وبسایت: <code>{url}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>Easy Digital Downloads</td>
			<td>
			<input type="checkbox" name="rahrayan_edd" id="rahrayan_edd" <?php echo get_option('rahrayan_edd') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_edd">هنگام ثبت سفارش جدید در Easy Digital Downloads به شما پیامک ارسال شود؟</label></td>
		</tr>
		<tr <?php echo get_option('rahrayan_edd') == false ? 'style="display:none"' : ''; ?> id="rahrayan_edd_text">
				<td scope="row">
			    متن پیامک
				</td><td>
					<textarea cols="50"  rows="7" name="rahrayan_edd_text"><?php echo get_option('rahrayan_edd_text'); ?></textarea>
					<p class="description">متن پیامک را وارد نمایید.</p> <p class="description">
						متغیر های قابل استفاده:					  تاریخ: <code>{date}</code>
					</p>
				</td>
			</tr>
		<tr>
			<td>Contact form 7</td>
			<td>
			<input type="checkbox" name="rahrayan_cf7" id="rahrayan_cf7" <?php echo get_option('rahrayan_cf7') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_cf7">هنگامی که یکی از فرم های این پلاگین تکمیل می شود، به شما پیام کوتاه ارسال شود؟</label></td>

</tr>
            <tr>
                <td>Gravity Forms</td>
                <td>
                    <input type="checkbox" name="rahrayan_gravity_forms" id="rahrayan_gravity_forms" <?php echo get_option('rahrayan_gravity_forms') == true ? 'checked="checked"' : ''; ?>/>
                    <label for="rahrayan_gravity_forms">فعال‌سازی هماهنگی با Gravity Forms</label></td>

            </tr>
		<input type="hidden" name="page_options" value="rahrayan_lostpw,rahrayan_lostpw_text,rahrayan_register2_text,rahrayan_register2,rahrayan_nregister_text,rahrayan_nregister,rahrayan_send,rahrayan_register,rahrayan_login,rahrayan_comment,rahrayan_edd,rahrayan_cf7,rahrayan_send_text,rahrayan_register_text,rahrayan_login_text,rahrayan_comment_text,rahrayan_edd_text,rahrayan_2fa_text,rahrayan_2fa,rahrayan_gravity_forms" />
		<?php
		break;
        case 'form':
		?>
		<div id="cp" style="display:none;"><div style="direction:rtl;text-align:right;font-family:Yekan"><h2>انتخاب رنگ</h2><p><input id="rahrayan-cp" /><br/>رنگ را انتخاب کرده، سپس آن را کپی کنید و در جای مورد نظر وارد کنید.</p></div></div>
		<div id="cp2" style="display:none;"><div style="direction:rtl;text-align:right;font-family:Yekan"><h2>نمونه فرم کوچک</h2><p style="text-align:center;margin:auto"><iframe src="index.php?rahrayan_mini=1" width="300px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe></p><br/><div class="note">توجه فرمایید که تغییرات انجام شده پس از ذخیره نمایان خواهد شد.</div></div></div>
		<div id="cp3" style="display:none;"><div style="direction:rtl;text-align:right;font-family:Yekan"><h2>نمونه فرم بزرگ</h2><p style="text-align:center;margin:auto"><iframe src="index.php?rahrayan_large=1" width="580px" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'" allowtransparency="yes"  scrolling="no" frameborder="0"></iframe></p><br/><div class="note">توجه فرمایید که تغییرات انجام شده پس از ذخیره نمایان خواهد شد.</div></div></div>
		<div style="float:left;margin-top: 8px;"><a href="#TB_inline?width=300&height=380&inlineId=cp" class="thickbox add-new">انتخاب رنگ مناسب</a>
			<a href="#TB_inline?width=940&height=320&inlineId=cp2" class="thickbox add-new">مشاهده نمونه فرم کوچک</a>
			<a href="#TB_inline?width=840&height=320&inlineId=cp3" class="thickbox add-new">مشاهده نمونه فرم بزرگ</a>
		</div>
		<div class="note">
	 برای بکگراند ها می توانید هم کد HEX رنگ هارا وارد کنید و هم از دستورات CSS استفاده کنید.
		</div>
		<div class="note" style="margin-top:4px">
		برای رنگ بردر ها فقط کد HEX رنگ هارا وارد کنید.
		</div>
		<div class="note" style="margin-top:4px">
		برای رنگ متن ها فقط کد HEX رنگ هارا وارد کنید.
		</div>
		<div class="note" style="margin-top:4px">
		برای انتخاب رنگ ها می توانید از لینک روبرو نیز استفاده کنید.
		</div>
		<div class="note" style="margin-top:4px">
		قسمت هایی که باید بر حسب پیکسل وارد شود را بدون هیچ عبارت اضافی و فقط عدد را وارد کنید.
		</div>
		<div class="note" style="margin-top:4px"> برای سایر تغییرات مورد نظر می توانید پرونده های <code style="cursor:default">wp-content/plugins/rahrayan/includes/templates/form_mini.php</code> و <code style="cursor:default">wp-content/plugins/rahrayan/includes/templates/form_large.php</code> را ویرایش کنید.
		</div>
		<tr>
			<td>بکگراند ورودی های فرم کوچک</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color=""  class="mp-color-field" name="rahrayan_ibg" value="<?php echo get_option('rahrayan_ibg'); ?>" required/>
			<p class="description">
				رنگ بکگراند ورودی های فرم کوچک را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند ورودی های فرم کوچک در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color=""  class="mp-color-field" name="rahrayan_ihbg" value="<?php echo get_option('rahrayan_ihbg'); ?>" required/>
			<p class="description">
				بکگراند ورودی های فرم کوچک در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند ورودی های فرم بزرگ</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#fff" class="mp-color-field" name="rahrayan_ilbg" value="<?php echo get_option('rahrayan_ilbg'); ?>" required/>
			<p class="description">
				رنگ بکگراند ورودی های فرم بزرگ را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند ورودی های فرم بزرگ در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#fff"  class="mp-color-field" name="rahrayan_ilhbg" value="<?php echo get_option('rahrayan_ilhbg'); ?>" required/>
			<p class="description">
				بکگراند ورودی های فرم بزرگ در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر ورودی ها</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#e0dfdf"  class="mp-color-field" name="rahrayan_iborder" value="<?php echo get_option('rahrayan_iborder'); ?>" required/>
			<p class="description">
				رنگ بردر ورودی ها را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر ورودی ها در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#83b2d8"  class="mp-color-field" name="rahrayan_ihborder" value="<?php echo get_option('rahrayan_ihborder'); ?>" required/>
			<p class="description">
				بردر ورودی ها در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند دکمه تایید</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#579bd3" class="mp-color-field" name="rahrayan_sbg" value="<?php echo get_option('rahrayan_sbg'); ?>" required/>
			<p class="description">
				رنگ بکگراند دکمه تایید را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بکگراند دکمه تایید در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#4887bc"  class="mp-color-field" name="rahrayan_shbg" value="<?php echo get_option('rahrayan_shbg'); ?>" required/>
			<p class="description">
				بکگراند دکمه تایید در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر دکمه تایید</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;"  data-default-color="#4887bc"  class="mp-color-field" name="rahrayan_sborder" value="<?php echo get_option('rahrayan_sborder'); ?>" required/>
			<p class="description">
				رنگ بردر دکمه تایید ها را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>بردر دکمه تایید در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;"  data-default-color="#4887bc" class="mp-color-field" name="rahrayan_shborder" value="<?php echo get_option('rahrayan_shborder'); ?>" required/>
			<p class="description">
				بردر دکمه تایید در حالت focus را انتخاب کنید.
			</p></td>
		</tr>
		<tr>
			<td>رنگ فونت ها</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#818181"  class="mp-color-field" name="rahrayan_fontc" value="<?php echo get_option('rahrayan_fontc'); ?>" required/>
			<p class="description">
				رنگ فونت ها را انتخاب نمایید.
			</p></td>
		</tr>
		<tr>
			<td>رنگ فونت ها در حالت focus</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" data-default-color="#6b9ecc"   class="mp-color-field" name="rahrayan_cfontc" value="<?php echo get_option('rahrayan_cfontc'); ?>" required/>
			<p class="description">
				رنگ فونت ها در حالت focus را انتخاب نمایید.
			</p></td>
		</tr>
		<tr>
			<td>رنگ بکگراند فرم در سایز بزرگ</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;"  data-default-color="#f5f5f5"  class="mp-color-field" name="rahrayan_form" value="<?php echo get_option('rahrayan_form'); ?>" required/>
			<p class="description">
				رنگ بکگراند فرم در سایز بزرگ را انتخاب نمایید.
			</p></td>
		</tr>
		<tr>
			<td>ضخامت بردر ها</td>
			<td>
			<input type="number" min="0" max="5" dir="ltr" style="width: 200px;" name="rahrayan_border" value="<?php echo get_option('rahrayan_border'); ?>" required/>
			<p class="description">
				ضخامت بردر های ورودی ها و دکمه تایید را به پیسکل وارد کنید.
			</p></td>
		</tr>
		<tr>
			<td>سایز فونت</td>
			<td>
			<input type="number" dir="ltr" min="1" style="width: 200px;" name="rahrayan_fonts" value="<?php echo get_option('rahrayan_fonts'); ?>" required/>
			<p class="description">
				سایز فونت ها را به پیکسل وارد کنید.
			</p></td>
		</tr>
		<tr>
			<td>font family</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="rahrayan_fontm" value="<?php echo get_option('rahrayan_fontm'); ?>" required/>
			<p class="description">
				font family را وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>border radius</td>
			<td>
			<input type="number" dir="ltr" min="0" style="width: 200px;" name="rahrayan_radius" value="<?php echo get_option('rahrayan_radius'); ?>" required/>
			<p class="description">
				border radius اینپوت ها و دکمه تایید را وارد نمایید.
			</p></td>
		</tr>
		<input type="hidden" name="page_options" value="rahrayan_ilhbg,rahrayan_ilbg,rahrayan_radius,rahrayan_cfontc,rahrayan_form,rahrayan_border,rahrayan_fontc,rahrayan_fonts,rahrayan_fontm,rahrayan_ibg,rahrayan_ihbg,rahrayan_iborder,rahrayan_ihborder,rahrayan_sbg,rahrayan_shbg,rahrayan_sborder,rahrayan_shborder" />
		<?php
		break;
		default:
		?>
		<tr>
			<td>شماره موبایل مدیر سایت</td>
			<td>
			<input type="text" dir="ltr" style="width: 200px;" name="rahrayan_admin" value="<?php echo get_option('rahrayan_admin'); ?>" required/>
			<p class="description">
				شماره موبایل مدیر سایت را وارد نمایید.
                <br/>
                شماره هارا با <code>;</code> از هم جدا کنید.
			</p></td>
		</tr>
		<tr>
			<td>امضای پیامک ها</td>

	<td>			<textarea style="width: 200px;height:100px" name="rahrayan_sig"><?php echo get_option('rahrayan_sig'); ?></textarea>
			<p class="description">
				امضای پیامک ها را وارد نمایید.
			</p></td>
		</tr>
		<tr>
			<td>صفحه بندی پلاگین</td>
			<td>
			<input type="number" dir="ltr" min="1" max="30" style="width: 200px;" name="rahrayan_page" value="<?php echo get_option('rahrayan_page'); ?>" required/>
			<p class="description">
				در هر صفحه چند رکورد قرار بگیرد؟
			</p></td>
		</tr>
		<tr>
			<td>دوره زمانی برای آپدیت اعتبار و بررسی برای آپدیت پلاگین</td>
			<td>
			<input type="number" min="1" max="12" dir="ltr" style="width: 200px;" name="rahrayan_update_period" value="<?php echo get_option('rahrayan_update_period'); ?>" required/>
			<p class="description">
				دوره زمانی جهت بررسی برای آپدیت پلاگین و سینک میزان اعتبار به ساعت
			</p></td>
		</tr>
		 <tr>
			<td>اضافه کردن فیلد شماره موبایل هنگام ثبت نام کاربران</td>
			<td>
			<input type="checkbox" name="rahrayan_mfield" id="rahrayan_mfield" <?php echo get_option('rahrayan_mfield') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_mfield">دریافت شماره موبایل از کاربران هنگام عضویت و ویرایش اطلاعات</label></td>
		</tr>
        <tr>
				<td>گروه دفترچه تلفن</td>
			<td><?php echo $rahrayan -> fetch_groups(true, get_option('rahrayan_group')); ?>
			<p class="description">
				کاربران سینک شده از پلاگین، در دفترچه تلفن اصلی شما در ره رایان پیامک در کدام گروه قرار بگیرند؟
	<br/>
	توجه کنید منظور از گروه در این قسمت گروه های ساخته شده در پنل SMS شما می باشد نه گروه های ساخته شده از پلاگین.
			</p></td>
			</tr>
		 <tr>
			<td>سینک دفترچه تلفن با ره رایان پیامک؟</td>
			<td>
			<input type="checkbox" name="rahrayan_sync" id="rahrayan_sync" <?php echo get_option('rahrayan_sync') == true ? 'checked="checked"' : ''; ?>/>
			<label for="rahrayan_sync">شماره هایی که در دفترچه تلفن پلاگین شما اضافه می شود به دفترچه تلفن اصلی شما در ره رایان پیامک اضافه شود؟</label></td>
		</tr>
	
		<input type="hidden" name="page_options" value="rahrayan_mfield,rahrayan_group,rahrayan_admin,rahrayan_sig,rahrayan_page,rahrayan_update_period,rahrayan_sync" />
		<?php
		break;
		}
		?>
		<tr style="background:none">

			<td>
			<p class="submit">
				<input type="hidden" name="action" value="update" />
				<input type="submit" class="button-primary" name="Submit" value="بروزرسانی" />
			</p></td>
		</tr>
	</form>
</table>
<?php
include dirname(__FILE__) . '/footer.php';
?>