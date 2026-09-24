(function(){
'use strict';
var byId=function(id){return document.getElementById(id);};
var title=byId('card-title'), subtitle=byId('card-subtitle'), key=byId('card-key'), origin=byId('card-origin');
var canvas=byId('card-canvas'), ctx=canvas.getContext('2d'), download=byId('card-download'), preview=byId('card-preview'), status=byId('status'), copy=byId('card-copy');
var generatedUrl='';
origin.value=(location.protocol==='https:'||location.protocol==='http:')?location.origin:'';
function text(s,x,y,size,color,weight){ctx.fillStyle=color;ctx.font=(weight||'400')+' '+size+'px "Microsoft Yahei",sans-serif';ctx.fillText(s,x,y);}
function rect(x,y,w,h,color){ctx.fillStyle=color;ctx.fillRect(x,y,w,h);}
function fit(s,x,y,size,color,maxWidth,weight){while(size>16){ctx.font=(weight||'400')+' '+size+'px "Microsoft Yahei",sans-serif';if(ctx.measureText(s).width<=maxWidth)break;size--;}text(s,x,y,size,color,weight);}
function invalidate(){generatedUrl='';download.hidden=true;download.removeAttribute('href');copy.disabled=true;preview.hidden=true;status.textContent='内容已修改，请重新生成。';}
[title,subtitle,key,origin].forEach(function(el){el.addEventListener('input',invalidate);});
byId('card-generate').onclick=function(){
try{
 var base=new URL(origin.value.trim());
 if((base.protocol!=='https:'&&base.protocol!=='http:')||base.username||base.password||base.pathname!=='/'||base.search||base.hash)throw new Error('请输入不含路径、账号或参数的 HTTP 或 HTTPS 站点地址。');
 if(!key.value.trim())throw new Error('请输入已创建的探针密钥。');
 if(!title.value.trim())throw new Error('请输入卡片标题。');
 var url=base.origin+'/probe.php?key='+encodeURIComponent(key.value.trim());
 if(url.length>1400)throw new Error('链接过长，请使用更短的密钥。');
 var qr=qrcode(0,'M');qr.addData(url);qr.make();
 rect(0,0,900,1200,'#f4f8f5');rect(0,0,900,360,'#143e33');
 ctx.fillStyle='#2d6956';ctx.beginPath();ctx.arc(815,60,230,0,Math.PI*2);ctx.fill();
 ctx.fillStyle='#3a7960';ctx.beginPath();ctx.arc(830,45,150,0,Math.PI*2);ctx.fill();
 text('位置分享邀请',64,85,26,'#c0dccd');text('INVITATION',64,119,15,'#8fb5a4');
 fit(title.value.trim(),64,225,58,'#ffffff',772,'600');fit(subtitle.value.trim(),66,282,28,'#d0e3d9',765);
 text('由你决定，要不要分享',64,430,34,'#193e31','600');
 text('扫码打开网页，在了解用途后自愿授权。',64,482,25,'#557363');
 text('先预览位置，再由你确认是否提交。',64,526,25,'#557363');
 // Render QR modules at integer pixels with a four-module quiet zone.
 var n=qr.getModuleCount(), cell=Math.max(1,Math.floor(370/(n+8))), size=(n+8)*cell;
 var left=Math.floor((900-size)/2), top=590;
 rect(left,top,size,size,'#fff');
 for(var r=0;r<n;r++)for(var c=0;c<n;c++)if(qr.isDark(r,c))rect(left+(c+4)*cell,top+(r+4)*cell,cell,cell,'#153a30');
 ctx.textAlign='center';text('长按识别二维码 · 打开邀请',450,1005,26,'#235b46','600');
 fit(base.host,450,1048,20,'#698071',760);ctx.textAlign='left';
 rect(64,1090,772,1,'#d4e2d8');text('网页邀请 · 自愿授权',64,1140,23,'#31624d');text('可拒绝，不持续追踪',556,1140,21,'#6b8174');
 var image=canvas.toDataURL('image/png');preview.src=image;preview.hidden=false;download.href=image;download.hidden=false;copy.disabled=false;generatedUrl=url;
 status.textContent='卡片已生成。请确认密钥有效，再保存图片或复制链接发送。';
}catch(e){invalidate();status.textContent=e.message||'生成失败，请检查输入。';}
};
copy.onclick=function(){
if(!generatedUrl)return;
if(navigator.clipboard&&window.isSecureContext){navigator.clipboard.writeText(generatedUrl).then(function(){status.textContent='链接已复制，可粘贴到微信。';},function(){status.textContent='请手动复制：'+generatedUrl;});}
else status.textContent='请手动复制：'+generatedUrl;
};
}());
