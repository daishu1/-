<?php
// The original remote request did not check or return authorization.
function check_authorization() {}

function check_install(){
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/install/install.lock")==0){
		exit('
		<script>
		layui.use("layer", function(){
			var layer = layui.layer;
			
		layer.open({
			title: "提示"
			,content:"您还未安装程序，点击确定进行安装"
			
				,yes: function(index, layero){
				window.location.href="/install/index.php";
					layer.close(index);
					}
				
				,cancel: function(index, layero){ 
				window.location.href="/install/index.php";
					layer.close(index);
				}  
			
		});     
		
		}); 						
		</script>');
	}
	}
?>