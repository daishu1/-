<?php
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
$key=$_GET['key'] ?? '';
if (!is_string($key)) $key='';
$key=substr($key,0,255);
?>
<!doctype html><html lang="zh-CN"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>邀请卡片生成</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f2f6f4;color:#193b34;font:16px/1.7 system-ui,"Microsoft Yahei",sans-serif}main{max-width:1080px;margin:48px auto;padding:0 24px}a{color:#15775e}.heading{font-size:13px;letter-spacing:3px;color:#518275}h1{font-size:32px;margin:8px 0}.grid{display:grid;grid-template-columns:1fr 420px;gap:48px;margin-top:30px}.form{background:white;border:1px solid #dce8e1;padding:28px;border-radius:20px;align-self:start}label{display:block;margin-bottom:20px}input{display:block;width:100%;padding:12px;border:1px solid #ccdcd4;border-radius:8px;font:inherit}button,.download{border:0;border-radius:8px;padding:12px 18px;background:#167d62;color:white;cursor:pointer;font:inherit;display:inline-block;text-decoration:none;margin:5px 6px 0 0}button.secondary{background:#e4f1eb;color:#245c4b}p.note{font-size:14px;color:#647c73}canvas{display:none}#card-preview{width:100%;height:auto;border-radius:22px;box-shadow:0 18px 48px #1b48351a}#status{min-height:30px} @media(max-width:760px){main{margin:24px auto}.grid{grid-template-columns:1fr;gap:24px}.form{padding:20px}}
</style></head><body><main><a href="/">← 返回原项目</a><div class="heading">LOCATION INVITATION</div><h1>把邀请，做成一张卡片。</h1><p>保存图片发到微信，或复制链接直接发送。对方可扫码打开授权定位页。</p><div class="grid"><section class="form">
<label>标题（最多12字）<input id="card-title" maxlength="12" value="分享此刻的位置"></label>
<label>说明（最多20字）<input id="card-subtitle" maxlength="20" value="方便相约，也尊重你的选择"></label>
<label>已生成的探针密钥<input id="card-key" maxlength="255" value="<?=htmlspecialchars($key,ENT_QUOTES,'UTF-8')?>" placeholder="先在原首页创建，并勾选授权定位"></label>
<label>HTTP/HTTPS 站点地址<input id="card-origin" value="" placeholder="http://location.example.com 或 https://location.example.com"></label>
<button id="card-generate" type="button">生成邀请卡片</button><button id="card-copy" type="button" class="secondary" disabled>复制网页链接</button><a id="card-download" class="download" hidden download="位置分享邀请.png">保存卡片图片</a>
<p id="status" role="status">请输入已创建的密钥，然后生成。</p><p class="note">这是一张网页邀请图片，不是真正的小程序卡片。微信中可长按图片识别二维码；若下载不可用，可长按右侧预览保存。是否展示链接缩略图由微信决定。</p><p class="note">图片和二维码在当前浏览器本地生成。修改输入后，请重新生成；密钥必须在本站真实存在且已开启定位。</p></section><section><canvas id="card-canvas" width="900" height="1200"></canvas><img id="card-preview" alt="生成后显示邀请卡片预览" hidden></section></div></main><script src="/assets/qrcode.js"></script><script src="/assets/share.js"></script></body></html>
