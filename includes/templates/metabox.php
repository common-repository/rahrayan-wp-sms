<?php
//check access
if (!function_exists('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
?>
<table class="form-table">
	<tr>
		<td><label for="rahrayan_select">آیا این پست از طریق پیامک اطلاع رسانی شود؟</label></td><td>
		<select name="rahrayan_select" id="rahrayan_select">
			<option value="1" >بلی</option>
			<option value="0"  selected>خیر</option>
		</select></td>
	</tr>
	<tr>
		<td><label for="rahrayan_message">متن پیامک</label></td><td>		<textarea cols="30"  rows="7" name="rahrayan_smessage" id="rahrayan_message"></textarea>
		<p class="description">
			اگر متن را وارد نکنید، از متن وارد شده در تنظیمات استفاده خواهد شد.
		</p></td>
	</tr>
</table>