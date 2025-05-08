# 阿里云绿网内容审核/腾讯云内容审核

## 运行环境

- php >= 8.0
- composer

## 安装

```shell
composer require tegic/security
```

## 说明


### 使用

```php
$config = [
    'access_key_id' => 'LTAI5tE***********',
    'access_key_secret' => 'ybdyNZ69kU7dRA***********',
    'region_id' => 'cn-shanghai',
    'endpoint' => 'green-cip.cn-shanghai.aliyuncs.com'
];

try {
    //普通方式调用 传入配置
    $client = Security::instance('ali', $config);
    $client->setOption([
        'service' => 'ugc_moderation_byllm'
    ]);****
    //hyperf 调用 默认使用配置文件
//    $client = \Tegic\Security\Security::instance('tencent');
    $result = $client->text('测试');// true 为内容通过
    var_dump($result ?? '');
} catch (\Tegic\Security\Exception\ContentErrorException $exception) {
    //内容审核不通过，$exception->getData() 可以获取 sdk 返回的内容
    var_dump("不通过", $exception->getData());
} catch (\Tegic\Security\Exception\SecurityException $securityException) {
    //系统错误，如 配置不正确等等..
    var_dump("系统错误", $securityException->getMessage());
}

```
