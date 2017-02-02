<?php
/**
 * 百度网盘操作类
 *
 * @author 消失的彩虹海
 * @website www.cccyun.cc
 * @version 1.0
 */
class Baidupan
{
	public $msg;
	private $cookie;
	private $mstring = 'devuid=742222580225582&clienttype=1&channel=android_4.4.2_H650_bd-netdisk_1523a&version=7.13.3&vip=2';
	public function __construct($bduss)
	{
		$this->cookie='BDUSS='.$bduss.';';
    }

	/**
     * 检测BUDSS是否有效
     * @return int
     */
	public function checkcookie() {
		$url='http://tieba.baidu.com/dc/common/tbs';
		$data=$this->get_curl($url,0,0,$this->cookie);
		$arr=json_decode($data,true);
		if($arr['is_login']==1){
			return true;
		}else{
			return false;
		}
	}

	/**
     * 获取文件列表
	 * @param string $path 文件路径
	 * @param int $num 显示数量
	 * @param string $order 按什么排序
	 * @param int $desc 是否为降序
	 * @param int $page 页数
     * @return array
     */
	public function getlist($path='/', $num=100, $order='name', $desc=0, $page=1) {
		$url='http://pan.baidu.com/api/list?dir='.urlencode($path).'&num='.$num.'&order='.$order.'&desc='.$desc.'&showempty=0&page='.$page.'&web=1&'.$this->mstring;
		$data=$this->get_curl($url,0,0,$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('errno',$arr) && $arr['errno']==0){
			return $arr['list'];
		}elseif($arr['errno']==-6){
			$this->msg='BDUSS已经失效';
			return false;
		}elseif($arr['errno']==-9){
			$this->msg='路径不存在';
			return false;
		}else{
			$this->msg='参数错误';
			return false;
		}
	}

	/**
     * 获取单个文件信息
	 * @param string $path 文件路径
	 * @param int $media 是否多媒体文件
     * @return array
     */
	public function getmeta($path, $media=0) {
		$target=urlencode('['.json_encode($path).']');
		$url='http://pan.baidu.com/api/filemetas?target='.$target.'&media='.$media.'&dlink=1&'.$this->mstring;
		$data=$this->get_curl($url,0,0,$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('errno',$arr) && $arr['errno']==0){
			return $arr['info'];
		}elseif($arr['errno']==-6){
			$this->msg='BDUSS已经失效';
			return false;
		}elseif($arr['errno']==12){
			$this->msg='该文件不存在';
			return false;
		}else{
			$this->msg='参数错误';
			return false;
		}
	}

	/**
     * 获取文件下载直链
	 * @param string $path 文件路径
     * @return string
     */
	public function getlink($path) {
		$url='http://d.pcs.baidu.com/rest/2.0/pcs/file?method=locatedownload&path='.urlencode($path).'&ver=2.0&dtype=0&esl=1&ehps=0&app_id=250528&check_blue=1&'.$this->mstring;
		$data=$this->get_curl($url,0,0,$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('urls',$arr)){
			return $arr['urls'][0]['url'];
		}elseif($arr['error_code']==31045){
			$this->msg='BDUSS已经失效';
			return false;
		}elseif($arr['error_code']==31066){
			$this->msg='该文件不存在';
			return false;
		}else{
			$this->msg='['.$arr['errno'].']'.$arr['error_msg'];
			return false;
		}
	}

	public function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$httpheader[] = "Accept:*/*";
		$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
		$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
		$httpheader[] = "Connection:close";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		if($header){
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
		}
		if($cookie){
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		if($referer){
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
		if($ua){
			curl_setopt($ch, CURLOPT_USERAGENT,$ua);
		}else{
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; Android 4.4.2; zh-cn) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36');
		}
		if($nobaody){
			curl_setopt($ch, CURLOPT_NOBODY,1);
		}
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}
}