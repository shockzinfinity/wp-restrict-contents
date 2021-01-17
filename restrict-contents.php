<?php
/*
  Plugin Name: Restrict contents
  Plugin URI: https://shockzinfinity.github.io
  Description: 컨텐츠 보기 제한 플러그인
  Version: 0.1
  Author: shockz
  Author URI: https://shockzinfinity.github.io
  Text Domain: shockz-restrict-admin-page
  Licence: MIT
  License URI: http://opensource.org/licenses/MIT
 */

// if (!define('WPINC')) {
//   die;
// }

include_once(plugin_dir_path(__FILE__) . 'shared/class-deserializer.php');
include_once(plugin_dir_path(__FILE__) . 'public/class-content-messenger.php');

function restrict_contents_activation()
{
  // 권한 테이블이 없을 경우 생성
  global $wpdb;
  $db_table_name = $wpdb->prefix . 'shockz_restricts';  // table name
  $charset_collate = $wpdb->get_charset_collate();

  //Check to see if the table exists already, if not, then create it
  if ($wpdb->get_var("show tables like '$db_table_name'") != $db_table_name) {
    $sql = "CREATE TABLE $db_table_name (
                id int(11) NOT NULL auto_increment,
                post_number varchar(25) NOT NULL,
                username varchar(60) NOT NULL,
                viewPriv TINYINT(1) NOT NULL,
                UNIQUE KEY id (id)
				) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
register_activation_hook(__FILE__, 'restrict_contents_activation');

function restrict_contents_deactivation()
{
}
register_deactivation_hook(__FILE__, 'restrict_contents_deactivation');

add_filter('the_content', 'restrict_filter');
function restrict_filter($content)
{
  // Don't proceed with this function if we're not viewing a single post.
  if (!is_single()) {
    //echo '<pre> not single </pre>';
    return $content;
  }

  global $post;
  global $wpdb;
  // 보기 권한 체크할 post id 등록 부분
  // TODO: 플러그인 활성화 체크 후, 디비 데이터 기준으로 제한 걸려 있는 포스트 아이디 조회
  // 테이블 인덱싱 고려
  $check_posts = array(215436);

  $post_id = $post->ID;
  if (!in_array($post_id, $check_posts)) {
    // TODO: 여기서 특정 post_id 체크, array 로 체크, 추후 확장가능하도록 custom post type 으로 분리 필요
    // custom post type 으로 분리하게 되면 post meta 체크해서 별도 쿼리 필요
    return $content;
  }

  // 여기서 로그인 여부 체크
  if (!is_user_logged_in()) {
    // TODO: $post 의 메타 데이터 조회하여 ref-url 로 보낼 permalink 로 처리 필요
    wp_redirect('/login/?ref-url=/2020/12/22/215436/');
    //return;
  }

  $current_user = wp_get_current_user();
  $username = $current_user->user_login;
  $result = $wpdb->get_var("SELECT viewPriv FROM {$wpdb->prefix}shockz_restricts WHERE post_number = '{$post_id}' AND username = '{$username}' LIMIT 1");

  // do something
  //wp_get_current_user()
  //->user_login
  // 1. 로그인한 사용자인지 체크
  // 2. 테이블에서 해당 포스트를 볼 권한이 있는지 체크
  //    SELECT TOP 1 viewPriv FROM wp_shockz_restricts WHERE post_number = '' AND username = ''
  //    0 row 혹은 값이 0 일 경우는 볼 권한 없음
  //    값이 1 일 경우는 볼 수 있음
  // 3. true 일 경우엔 return $content
  // 4. false 일 경우엔 message (상품을 구매해야 볼 수 있습니다.)
  //echo $result;
  if ($result) {
    // $content = '<h2>ALLOWED !!!!!!!!!!!</h2>' . $content;
    return $content;
  } else {
    $html = '<pre>';
    //$temp_meta = get_post_meta($post_id, "classic-editor-remember", true);
    $html .= "<h2>NOT ALLOWED</h2>";
    $html .= '</pre>';

    return $html;
  }
}

// add_filter('login_redirect', 'redirect_previous_page', 10, 1);
// function redirect_previous_page($redirect_to)
// {
// 	global $user;
// 	$request = $_SERVER["HTTP_REFERER"];

// 	if (in_array($user->roles[0], array('administrator'))) {
// 		return admin_url();
// 	} elseif (in_array($user->roles[0], array('subscriber'))) {
// 		return $request;
// 	}

// 	return $redirect_to;
// }

// include the dependencies needed to instantiate the plugin.
foreach (glob(plugin_dir_path(__FILE__) . 'admin/*.php') as $file) {
  include_once $file;
}

add_action('plugins_loaded', 'restrict_custom_settings');
function restrict_custom_settings()
{
  $serializer = new Serializer();
  $serializer->init();

  $deserializer = new Deserializer();

  // $plugin = new Submenu(new Submenu_Page($serializer));
  $plugin = new Submenu(new Submenu_Page($serializer, $deserializer));
  $plugin->init();

  $public = new Content_messaenger($deserializer);
  $public->init();
}
