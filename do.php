<?php
require 'inc.php';

$act=daddslashes($_GET['act']);

if($act=='creat'){

$bduss=daddslashes($_POST['bduss']);
$path=daddslashes($_POST['path']);

$x=new Baidupan($bduss);

$row=$DB->query("SELECT * FROM baidupan_user WHERE bduss='{$bduss}' limit 1")->fetch();
if($row){
	$uid=$row['id'];
}else{
	if($x->checkcookie()){
		$sds=$DB->exec("INSERT INTO `baidupan_user` (`user`, `pwd`, `bduss`, `active`) VALUES (NULL, NULL, '{$bduss}', '1')");
		$uid=$DB->lastInsertId();
		if(!$sds)exit('{"code":-1,"msg":"保存BDUSS失败"}');
	}else{
		exit('{"code":-1,"msg":"BDUSS已失效"}');
	}
}

if($url=$x->getlink($path)){
	$hash=md5($uid.'..'.$path);
	$extension=explode('.',$path);
	if (($length = count($extension)) > 1) {
		$ext = strtolower($extension[$length - 1]);
	}
	$row=$DB->query("SELECT * FROM baidupan_share WHERE hash='{$hash}' limit 1")->fetch();
	if($row){
		$download=$siteurl.'down.php/'.$hash.'.'.$ext;
		$result=array('code'=>0,'url'=>$url,'download'=>$download);
	}else{
		$sds=$DB->exec("INSERT INTO `baidupan_share` (`hash`, `uid`, `path`, `folder`, `type`, `time`) VALUES ('{$hash}', '{$uid}', '{$path}', '0', '{$ext}', '{$date}')");
		if($sds){
			$download=$siteurl.'down.php/'.$hash.'.'.$ext;
			$result=array('code'=>0,'url'=>$url,'download'=>$download);
		}else{
			$result=array('code'=>-1,'msg'=>'生成链接失败');
		}
	}
}else{
	$result=array('code'=>-1,'msg'=>$x->msg);
}
echo json_encode($result);
}elseif($act=='list'){

$bduss=daddslashes($_POST['bduss']);
$srow=$DB->query("SELECT * FROM baidupan_user WHERE bduss='{$bduss}' limit 1")->fetch();

if($srow){
	$rs=$DB->query("SELECT * FROM baidupan_share WHERE uid='{$srow['id']}' limit 20");
	$data=array();
	while($row=$rs->fetch()){
		$path=explode('/',$row['path']);
		if (($length = count($path)) > 1) {
			$array['name'] = $path[$length - 1];
		}
		$extension=explode('.',$row['path']);
		if (($length = count($extension)) > 1) {
			$ext = strtolower($extension[$length - 1]);
		}
		$array['hash']=$row['hash'];
		$array['download']=$siteurl.'down.php/'.$row['hash'].'.'.$ext;
		$data[]=$array;
	}
	$result=array('code'=>0,'data'=>$data);
}else{
	$result=array('code'=>-1);
}
echo json_encode($result);
}