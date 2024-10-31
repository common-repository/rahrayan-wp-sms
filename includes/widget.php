<?php
//check access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
//widget
/*add_action("widgets_init", array(
    'rahrayan_widget',
    'register'
));
register_activation_hook(__FILE__, array(
    'rahrayan_widget',
    'activate'
));
register_deactivation_hook(__FILE__, array(
    'rahrayan_widget',
    'deactivate'
));*/
class rahrayan_widget {
    function activate() {
        $data = array(
            'width' => '300',
            'before' => '',
            'after' => '',
            'title' => 'عضویت در خبرنامه پیامکی'
        );
        update_option('rahrayan_widget', $data);
    }

    function deactivate() {
        delete_option('rahrayan_widget');
    }

    function control() {
        $data = get_option('rahrayan_widget');
        if (isset($_POST['rahrayan'])) {
            $data['width'] = esc_html($_POST['rahrayan']['width']);
            $data['before'] = esc_html($_POST['rahrayan']['before']);
            $data['after'] = esc_html($_POST['rahrayan']['after']);
            $data['title'] = esc_html($_POST['rahrayan']['title']);
            update_option('rahrayan_widget', $data);
        }
        echo "<p><label>عنوان ابزارک</label><br/><input name='rahrayan[title]' type='text' value='{$data['title']}' /></p>";
        echo "<p><label>عرض فرم(استفاده از CSS مجاز است.)</label><br/><input name='rahrayan[width]' type='text' value='{$data['width']}' /></p>";
        echo "<p><label>متن قبل از فرم</label><br/><textarea name='rahrayan[before]'>{$data['before']}</textarea></p>";
        echo "<p><label>متن بعد از فرم</label><br/><textarea name='rahrayan[after]'>{$data['after']}</textarea></p>";
    }

    function widget($args) {
        $data = get_option('rahrayan_widget');
        echo $args['before_widget'];
        echo $args['before_title'] . $data['title'] . $args['after_title'] . $data['before'];
        if (is_numeric($data['width']))
            $data['width'] .= 'px';
        if (empty($data['width']))
            $data['width'] = '100%';
        echo "<iframe src=\"" . get_bloginfo('url') . "/?rahrayan_mini=1\" onload=\"this.style.height=this.contentWindow.document.body.scrollHeight+'px';this.style.display='inline'\" width=\"" . $data['width'] . "\" allowtransparency=\"yes\"  scrolling=\"no\" frameborder=\"0\"></iframe>";
        echo $data['after'] . $args['after_widget'];
    }

    function register() {
        register_sidebar_widget('ره رایان پیامک', array(
            'rahrayan_widget',
            'widget'
        ));
        register_widget_control('ره رایان پیامک', array(
            'rahrayan_widget',
            'control'
        ));
    }

}
