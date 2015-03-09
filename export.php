<?php
/* 这里替换为连接的实例host和port */
$host = "127.0.0.1";
$port = 6379;

  /* 这里替换为实例id和实例password,没有则注释掉 */
/*$user = "xxxxxxxxxxxxxxxxxxxx";
$pwd = "xxxxxxx";*/
$Redis = new redis();
//$Redis->pconnect('127.0.0.1', 6379);
if ($Redis->pconnect($host, $port) == false) {
    die($Redis->getLastError());
}

//如果没有账户密码，就注释掉
/*if ($Redis->auth($user . ":" . $pwd) == false) {
    die($Redis->getLastError());
}*/

file_put_contents('./redis.json', '');
$it = NULL; /* Initialize our iterator to NULL */

$Redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY); /* retry when we get no keys back */
while($keys = $Redis->scan($it)) {

	$out = '';
    foreach($keys as $key) {
	//echo $Redis->type($key),"\n";
	$arr = array();
	$type = $Redis->type($key);
	$expire = $Redis->ttl($key);

	switch ($type) {
		case $Redis::REDIS_STRING:
			//echo "string\n";
			$arr['expire'] = $expire;
			$arr['type'] = $type;
			$arr['key'] = $key;
			$arr['val'] = $Redis->get($key);
			break;
		case $Redis::REDIS_HASH:
			//echo "hash\n";
			$arr['expire'] = $expire;
			$arr['type'] = $type;
			$arr['key'] = $key;
			$arr['val'] = $Redis->hGetAll($key);

			break;
		case $Redis::REDIS_LIST:
			//echo "list\n";
			$arr['expire'] = $expire;
			$arr['type'] = $type;
			$arr['key'] = $key;
			$arr['val'] = $Redis->lRange($key, 0, -1);
			break;
		case $Redis::REDIS_SET:
			//echo "set\n";
			$arr['expire'] = $expire;
			$arr['type'] = $type;
			$arr['key'] = $key;
			$arr['val'] = $Redis->sMembers($key);
			break;
		case $Redis::REDIS_ZSET:
			//echo "zset\n";zRange('key1', 0, -1, true);
			$arr['expire'] = $expire;
			$arr['type'] = $type;
			$arr['key'] = $key;
			$arr['val'] = $Redis->zRange($key, 0, -1, true);
			break;


		default:
			//echo "unknown\n";
			continue;
			break;
	}
	$out .= json_encode($arr)."\n";

	}
	file_put_contents('./redis.json', $out,FILE_APPEND);
}




/*$keys = $Redis->keys('*');
$out = '';*/

//echo $out;
$Redis->close();

