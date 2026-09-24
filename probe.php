<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('IN_CRONLITE', true);
require __DIR__.'/include/location_common.php';
location_session();
header('Content-Type: text/html; charset=utf-8');
$key = $_GET['key'] ?? '';
if (!is_string($key) || $key === '' || strlen($key) > 255) { http_response_code(404); exit('链接无效'); }
try {
    $db = location_database();
    $stmt = $db->prepare('SELECT * FROM list_probe WHERE key_probe=? LIMIT 1');
    $stmt->bind_param('s', $key); $stmt->execute(); $probe = $stmt->get_result()->fetch_assoc();
} catch (Throwable $e) { http_response_code(503); exit('服务暂时不可用，请联系站点管理员'); }
if (!$probe) { http_response_code(404); exit('链接不存在'); }
$pages = ['404'=>'404','503'=>'503','新建网站'=>'new','停止网站'=>'stop','你好世界'=>'hello','百度一下'=>'baidu'];
$page = $pages[$probe['probe_page']] ?? 'hello';
// Preserve the original selected page, then add an unmistakable consent panel.
ob_start(); include __DIR__.'/include/probe_page/'.$page.'.php'; $html=ob_get_clean();
$enabled = $probe['gps_location_function'] === '1';
$token = bin2hex(random_bytes(32));
$_SESSION['location_tokens'] = array_filter($_SESSION['location_tokens'] ?? [], function($v) { return $v['expires'] > time(); });
if (count($_SESSION['location_tokens']) >= 20) array_shift($_SESSION['location_tokens']);
$_SESSION['location_tokens'][$token] = ['key'=>$key,'expires'=>time()+1800];
ob_start();
?>
<section id="location-consent" data-token="<?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?>" style="position:relative;z-index:2147483647;background:white;color:#222;border:2px solid #009688;border-radius:8px;padding:18px;margin:18px auto;max-width:560px;font:16px/1.7 sans-serif;box-sizing:border-box">
  <h2 style="font-size:20px">自愿分享当前位置</h2>
  <p>此链接由本站运营者创建。定位用途：向本站运营者分享您本次所在的位置。获取后先在本页预览；只有点击“确认提交位置”，经纬度、精度、提交时间及基础访问信息才会保存到本站，供管理员查看。本站不向地图服务转发精确位置。</p>
  <p>您可以拒绝或取消，不影响浏览本页。请仅向您信任的运营者提交；如需删除已提交记录，请联系向您发送链接的人。网页服务器仍可能记录常规访问日志。</p>
  <?php if ($enabled): ?>
  <label><input type="checkbox" id="location-agree"> 我已了解用途，自愿获取本次位置</label><br>
  <button type="button" id="location-get">获取当前位置</button>
  <button type="button" id="location-submit" hidden>确认提交位置</button>
  <button type="button" id="location-cancel">取消</button>
  <?php else: ?><p>此链接未启用定位。</p><?php endif; ?>
  <p id="location-status" role="status" aria-live="polite">未获取、未提交位置。</p>
</section>
<script src="/assets/location.js" defer></script>
<?php
$panel=ob_get_clean();
if (stripos($html,'</body>') !== false) $html=preg_replace('~</body>~i',$panel.'</body>',$html,1);
else $html = '<!doctype html><html lang="zh-CN"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head><body>'.$html.$panel.'</body></html>'; 
echo $html;
