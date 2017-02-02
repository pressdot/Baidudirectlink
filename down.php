<?php
require 'inc.php';

$urlarr=explode('/',$_SERVER['REQUEST_URI']);
if (($length = count($urlarr)) > 1) {
$url = $urlarr[$length-1];
}
if(stripos($url,'.')!==false){
	$url=substr($url,0,stripos($url,'.'));
}

$row=$DB->query("SELECT * FROM baidupan_share WHERE hash='{$url}' limit 1")->fetch();
if($row){
	$path=$row['path'];
	$user=$DB->query("SELECT * FROM baidupan_user WHERE id='{$row['uid']}' limit 1")->fetch();
	if(!$user)exit('BDUSS not exists');
	$bduss=$user['bduss'];
}else{
	exit('Can\'t find this file');
}
$x=new Baidupan($bduss);

if($url=$x->getlink($path)){
?>
<html>
<body>
<script>
function open_without_referrer(link){
document.body.appendChild(document.createElement('iframe')).src='javascript:"<script>top.location.replace(\''+link+'\')<\/script>"';
}
open_without_referrer('<?php echo $url ?>');
</script>
</body></html>
<?php
	exit;
}else{
	exit('Failed to parse this file');
}