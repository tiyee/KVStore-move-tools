# KVStore-move-tools
阿里云KVStore迁移工具
##运行环境   
请先安装[redis的php扩展](https://github.com/phpredis/phpredis)
##使用方法   
* 先在原始数据的服务器上执行 php ./export.php,生成redis.json文件，
* 将生成的redis.json文件和import.php文件复制到目标服务器，然后执行php ./import.php

（请根据实际情况更改redis的连接信息）
