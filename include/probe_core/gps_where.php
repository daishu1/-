<?php
// No legacy ip_id updates; every write requires a live session and its random token.
define('IN_CRONLITE', true);
require dirname(__DIR__).'/location_common.php';
location_session();
header('Content-Type: application/json; charset=utf-8');
function reply($code,$message,$ok=false) { http_response_code($code); echo json_encode(['ok'=>$ok,'message'=>$message],JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); reply(405,'只接受 POST'); }
if (($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '') === 'cross-site') reply(403,'不允许跨站提交');
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 4096) reply(413,'请求过大');
$input = json_decode(file_get_contents('php://input',false,null,0,4097),true);
if (!is_array($input) || ($input['consent'] ?? null) !== true) reply(400,'需要明确确认提交');
$token = $input['token'] ?? '';
if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D',$token)) reply(403,'授权信息无效');
$binding = $_SESSION['location_tokens'][$token] ?? null;
if (!$binding || $binding['expires'] < time()) reply(403,'页面已过期，请刷新并重新授权');
if (!empty($binding['saved'])) reply(200,'本次位置已提交，请勿重复操作。',true);
foreach (['lat'=>90,'lon'=>180,'accuracy'=>10000000] as $field=>$bound) {
    $value=$input[$field] ?? null;
    if ((!is_int($value) && !is_float($value)) || !is_finite((float)$value) || abs($value)>$bound || ($field==='accuracy' && $value<0)) reply(400,'坐标或精度无效');
}
try {
    $db=location_database();
    $stmt=$db->prepare('SELECT gps_location_function FROM list_probe WHERE key_probe=? LIMIT 1');
    $stmt->bind_param('s',$binding['key']); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
    if (!$row || $row['gps_location_function'] !== '1') reply(403,'此链接已停止定位');
    $lonlat=sprintf('经度:%.6f/纬度:%.6f (WGS84)',$input['lon'],$input['lat']);
    $description='用户主动授权; 精度约'.round($input['accuracy']).'米; '.gmdate('Y-m-d H:i:s').' UTC';
    $ip=$_SERVER['REMOTE_ADDR'] ?? ''; // Do not trust arbitrary forwarded headers.
    $ua=substr($_SERVER['HTTP_USER_AGENT'] ?? '',0,1000);
    $lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',0,200);
    $stmt=$db->prepare("INSERT INTO list_ip (key_ip,ip,ip_location,ip_lonlat,gps_location,gps_lonlat,camera_img,system,browser,browser_ua,browser_language) VALUES (?,?,'未查询','未查询',?,?,'未启用','未解析','浏览器授权定位',?,?)");
    $stmt->bind_param('ssssss',$binding['key'],$ip,$description,$lonlat,$ua,$lang); $stmt->execute();
    $_SESSION['location_tokens'][$token]['saved']=true;
    reply(200,'位置已提交给本站管理员。',true);
} catch (Throwable $e) { reply(503,'暂时无法保存，请稍后重试'); }
