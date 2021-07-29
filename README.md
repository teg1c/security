# 阿里云绿网内容审核/腾讯云内容审核

## 运行环境

- php >= 7.3
- composer

## 安装

```shell
composer require tegic/security
```

## 说明

### 发布配置文件

```shell
php bin/hyperf.php vendor:publish tegic/security
```

### 使用

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class IndexController extends AbstractController
{
    public function index()
    {
        $config = [
            'access_key_id'=>'',
            'access_key_secret'=>'',
            'region_id'=>'cn-shanghai',
            'debug'=>false,
        ];
        try {
            //普通方式调用 传入配置
            $client = \Tegic\Security\Security::instance('tencent',$config);
            //hyperf 调用 默认使用配置文件
            $client = \Tegic\Security\Security::instance('tencent');
            $result = $client->text('你好啊傻逼');// true 为内容通过
        }catch (\Tegic\Security\Exception\ContentErrorException $exception){
            //内容审核不通过，$exception->getData() 可以获取 sdk 返回的内容
            var_dump($exception->getData());
        }catch (\Tegic\Security\Exception\SecurityException $securityException){
            //系统错误，如 配置不正确等等..
        }
        
    }
}

```
