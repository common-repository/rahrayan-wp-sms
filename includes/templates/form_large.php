<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
$url = plugins_url('/form/', __FILE__);
$inc_url = includes_url();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<script src="<?php echo $inc_url ?>js/jquery/jquery.js"></script>
		<script src="<?php echo $url ?>main.js"></script>
		<style>
			body {
				direction: rtl;
				text-align: right;
			}
			@font-face {
				font-family: 'Yekan';
				src: url('<?php echo $url ?>fonts/YekanWeb-Regular.woff') format('woff');
				font-weight: normal;
				font-style: normal;
			}
			input, select {
				outline: none;
			}
			.rahrayan_group_box:focus, .textbox_rahrayan_box:focus,{
				color: <?php echo get_option('rahrayan_cfontc') ?>;
				border: <?php echo get_option('rahrayan_border') ?>px solid <?php echo get_option('rahrayan_ihborder') ?>;
			}
			.submit_rahrayan_box:hover{
				background: <?php echo get_option('rahrayan_shbg') ?>;
				border: <?php echo get_option('rahrayan_border') ?>px solid <?php echo get_option('rahrayan_shborder') ?>;
			}
			<?php $color=(get_option('rahrayan_ilbg') != 'none')? get_option('rahrayan_ilbg') : ''; ?>
			.rahrayan_name{
				background: url(<?php echo $url ?>images/rahrayan_user.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_gender {
				background: url(<?php echo $url ?>images/rahrayan_gender.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_mobile {
				background: url(<?php echo $url ?>images/rahrayan_phone.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_group {
				background: url(<?php echo $url ?>images/rahrayan_group.png) right 8px center no-repeat <?php echo $color ?>;
			}
			<?php $color=(get_option('rahrayan_ilhbg') != 'none')? get_option('rahrayan_ilhbg') : ''; ?>
			.rahrayan_name:focus {
				background: url(<?php echo $url ?>images/rahrayan_user_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_gender:focus {
				background: url(<?php echo $url ?>images/rahrayan_gender_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_mobile:focus {
				background: url(<?php echo $url ?>images/rahrayan_phone_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_group:focus {
				background: url(<?php echo $url ?>images/rahrayan_group_hover.png) right 8px center no-repeat <?php echo $color ?>;
			}
			.rahrayan_box {
				width: 100%;
				background:  <?php echo get_option('rahrayan_form') ?>;
				border-radius: 3px;
				padding: 10px 0px;
				padding-top:5px;
			}
			.textbox_rahrayan_box {
				transition: 0.2s;
				padding-right: 50px;
				box-sizing: border-box;
				margin: 5px 1.18% 0px 0px;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				line-height: 32px;
				width: 48%;
				border: <?php echo get_option('rahrayan_border') ?>px solid <?php echo get_option('rahrayan_iborder') ?>;
				border-radius: <?php echo get_option('rahrayan_radius') ?>px;
				color: <?php echo get_option('rahrayan_fontc') ?>;
				font-family: <?php echo get_option('rahrayan_fontm') ?>;
				font-size: <?php echo get_option('rahrayan_fonts') ?>px;
				height:44px;
			}
			.rahrayan_group_box {
				transition: 0.2s;
				padding-right: 50px;
				box-sizing: border-box;
				margin: 5px 1.18% 0px;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				line-height: 32px;
				width: 97.7%;
				border: <?php echo get_option('rahrayan_border') ?>px solid <?php echo get_option('rahrayan_iborder') ?>;
				border-radius: <?php echo get_option('rahrayan_radius') ?>px;
				color: <?php echo get_option('rahrayan_fontc') ?>;
				font-family: <?php echo get_option('rahrayan_fontm') ?>;
				font-size: <?php echo get_option('rahrayan_fonts') ?>px;
				height:44px;
			}

			.submit_rahrayan_box {
				cursor: pointer;
				transition: 0.2s;
				box-sizing: border-box;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				line-height: 32px;
				width: 97.7%;
				margin: 5px 1.18% 0px;
				background: <?php echo get_option('rahrayan_sbg') ?>;
				border: <?php echo get_option('rahrayan_border') ?>px solid <?php echo get_option('rahrayan_sborder') ?>;
				border-radius: <?php echo get_option('rahrayan_radius') ?>px;
				color: #fff;
				font-family: <?php echo get_option('rahrayan_fontm') ?>;
				font-size: <?php echo get_option('rahrayan_fonts') ?>px;
				height:44px;
			}
			.rahrayan_invalid{
				border: <?php echo get_option('rahrayan_border') ?>px solid #c0392b !important;
				color:#c0392b !important;
			}
			select.rahrayan_invalid{
				color: <?php echo get_option('rahrayan_fontc') ?> !important;
			}
			#mpcode{
				font-family: <?php echo get_option('rahrayan_fontm') ?>;
				font-size: 13px;
				color: <?php echo get_option('rahrayan_fontc') ?> !important;
				text-align:center;
				margin-left: 23px;
			}
			.rahrayan_code{
				text-align:center;
				padding-right:0px;
			}
			select{
                 -webkit-appearance: none;
                  -moz-appearance: none;
                  appearance: none;
            }
		</style>
	</head>
	<body>
		<form method="post" id="rahrayan" action="">
     <section class="rahrayan_box">
        <div class="mpnew">
        	<input maxlength="255" type="text" class="textbox_rahrayan_box rahrayan_fname rahrayan_name" placeholder="نام">
			<input maxlength="255" type="text" class="textbox_rahrayan_box rahrayan_name rahrayan_lname" placeholder="نام خانوادگی">
			<input type="text" class="textbox_rahrayan_box rahrayan_mobile" placeholder="موبایل">
			<select style="width: 48% !important;margin: 5px 1.18% 0px 0px;" class="rahrayan_group_box rahrayan_gender">
				<option  disabled selected value="">جنسیت</option>
				<option value="1">زن</option>
				<option value="2">مرد</option>
			</select>
			<select  class="rahrayan_group_box rahrayan_group">
				<option disabled selected value="">گروه کاربری</option>
				<?php echo $rahrayan -> fetch_phonebook_groups(true,'',false,true) ?>
			</select>
			<input type="submit" id="submit_rahrayan" class="submit_rahrayan_box" value="اشتراک یا لغو اشتراک">
        </div>
			<div style="display:none" class="mpcode">
				<div id="mpcode"></div>
					<input style="width: 97.7%;" type="text" class="textbox_rahrayan_box rahrayan_code" placeholder="کد تایید">
				<input type="submit" id="submitcmp" class="submit_rahrayan_box" value="ارسال کد تایید">
			</div>
		</section>
		</form>
	</body>
</html>
