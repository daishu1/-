(function () {
  'use strict';
  var panel = document.getElementById('location-consent');
  var get = document.getElementById('location-get');
  if (!panel || !get) return;
  var submit = document.getElementById('location-submit');
  var agree = document.getElementById('location-agree');
  var cancel = document.getElementById('location-cancel');
  var status = document.getElementById('location-status');
  var position = null, generation = 0, sending = false;
  function say(text) { status.textContent = text; }
  function clear() { generation++; position = null; submit.hidden = true; get.disabled = false; }
  cancel.onclick = function () {
    if (sending) return;
    clear(); agree.checked = false; say('已取消，未提交位置。');
  };
  agree.onchange = function () { if (!agree.checked) { clear(); say('已取消授权操作，未提交位置。'); } };
  get.onclick = function () {
    clear();
    if (!agree.checked) { say('请先阅读说明并勾选自愿获取位置。'); return; }
    if (!navigator.geolocation) { say('当前微信或浏览器不支持定位，可使用系统浏览器打开此 HTTPS 链接。'); return; }
    var current = generation;
    get.disabled = true; say('正在请求本次位置，请在微信/系统提示中自行选择是否允许。');
    navigator.geolocation.getCurrentPosition(function (p) {
      if (current !== generation || !agree.checked) return;
      get.disabled = false;
      var c = p.coords;
      if (!isFinite(c.latitude) || !isFinite(c.longitude) || !isFinite(c.accuracy)) { say('定位返回无效数据，请重试。'); return; }
      position = {lat:c.latitude, lon:c.longitude, accuracy:c.accuracy, obtained:Date.now()};
      submit.hidden = false;
      say('仅在本页预览：纬度 '+c.latitude.toFixed(6)+'，经度 '+c.longitude.toFixed(6)+'；精度约 '+Math.round(c.accuracy)+' 米。尚未提交。');
    }, function (e) {
      if (current !== generation) return;
      get.disabled = false;
      var errors = {1:'定位授权被拒绝或受系统限制。您可在系统及微信权限设置中自行允许后重试。',2:'暂时无法获取位置，请检查系统定位服务和网络，或使用系统浏览器。',3:'定位超时，请移动到信号较好的位置后重试。'};
      say(errors[e.code] || '定位失败，请使用系统浏览器打开。');
    }, {enableHighAccuracy:true, timeout:15000, maximumAge:0});
  };
  submit.onclick = function () {
    if (sending || !position || !agree.checked) return;
    if (Date.now()-position.obtained > 120000) { clear(); say('位置预览已过期，请重新获取。'); return; }
    sending = true; submit.disabled = get.disabled = cancel.disabled = agree.disabled = true;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/include/probe_core/gps_where.php', true);
    xhr.timeout = 20000;
    xhr.setRequestHeader('Content-Type','application/json');
    function finish(message, ok) {
      sending = false; submit.disabled = get.disabled = cancel.disabled = agree.disabled = false;
      if (ok) { clear(); get.disabled = true; agree.disabled = true; cancel.disabled = true; }
      say(message);
    }
    xhr.onload = function () {
      var data; try { data = JSON.parse(xhr.responseText); } catch (_) { finish('服务器响应异常，提交结果未知，请联系管理员核实。', false); return; }
      finish(data.message || '提交失败，请刷新后重试。', xhr.status === 200 && data.ok === true);
    };
    xhr.onerror = xhr.ontimeout = function () { finish('网络异常，提交结果未知；可以重试，服务器会避免重复记录。', false); };
    say('正在提交您确认的位置…');
    xhr.send(JSON.stringify({token:panel.getAttribute('data-token'), consent:true, lat:position.lat, lon:position.lon, accuracy:position.accuracy}));
  };
}());
