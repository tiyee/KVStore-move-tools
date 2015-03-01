<?php
/* 这里替换为连接的实例host和port */
$host = "xxxxxxxxxxx.m.cnhza.kvstore.aliyuncs.com";//改成你自己的
$port = 6379;

  /* 这里替换为实例id和实例password */
$user = "xxxxxxxxxxxxxxxx";//改成你自己的
$pwd = "xxxxx";//改成你自己的
$Redis = new redis();
//$Redis->pconnect('127.0.0.1', 6379);
if ($Redis->pconnect($host, $port) == false) {
    die($Redis->getLastError());
}


if ($Redis->auth($user . ":" . $pwd) == false) {
    die($Redis->getLastError());
}



$file = fopen("redis.json","r");
$arr = array();
while(! feof($file))
  {
  //echo fgets($file). "<br />";
  $arr = json_decode(fgets($file),true);
  $key = $arr['key'];

  switch ($arr['type']) {
		case $Redis::REDIS_STRING:
			echo "string\n";

			$Redis->set($arr['key'],$arr['val']);
			$Redis->setTimeout($arr['key'],$arr['expire']);
			break;
		case $Redis::REDIS_HASH:
			echo "hash\n";


			$Redis->hMset($arr['key'],$arr['val']);
			$Redis->setTimeout($arr['key'],$arr['expire']);

			break;
		case $Redis::REDIS_LIST:
			echo "list\n";

			foreach($arr['val'] as  $v) {
				$Redis->rPush($arr['key'], $v);
			}
			$Redis->setTimeout($arr['key'],$arr['expire']);
			break;
		case $Redis::REDIS_SET:
			echo "set\n";

			foreach($arr['val'] as $v) {
				$Redis->sAdd($arr['key'], $v);
			}
			$Redis->setTimeout($arr['key'],$arr['expire']);
			break;
		case $Redis::REDIS_ZSET:
			echo "zset\n";

			foreach($arr['val'] as  $v =>$score) {
				$Redis->zAdd($arr['key'],$score, $v);
			}
			break;


		default:
			//echo "unknown\n";
			continue;
			break;
	}
  }
$Redis->close();
fclose($file);
