<?php
function qqshare($url,$title,$pics,$summary,$desc){
    return 'https://connect.qq.com/widget/shareqq/index.html?'.http_build_query(['url'=>$url,'sharesource'=>'qzone','title'=>$title,'pics'=>$pics,'summary'=>$summary,'desc'=>$desc],'','&',PHP_QUERY_RFC3986);
}
