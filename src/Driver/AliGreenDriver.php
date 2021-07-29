<?php

declare(strict_types=1);
/**
 * This file is part of Security.
 *
 * @Author     Tegic
 * @Contact  https://github.com/teg1c
 */
namespace Tegic\Security\Driver;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Tegic\Security\Exception\ContentErrorException;
use Tegic\Security\Exception\SecurityException;

class AliGreenDriver implements DriverInterface
{

    private $accessKeyId;

    private $accessKeySecret;

    private $regionId;

    private $debug;

    private $timeout;

    private $connectTimeout;

    public function __construct($config = [])
    {
        $this->accessKeyId = $config['access_key_id'] ?? '';
        $this->accessKeySecret = $config['access_key_secret'] ?? '';
        $this->regionId = $config['region_id'] ?? '';
        $this->debug = $config['debug'] ?? false;
        $this->timeout = $config['timeout'] ?? 6;
        $this->connectTimeout = $config['connect_timeout'] ?? 10;

        $this->init();
    }

    public function text($content = '')
    {
        if (empty($content)) {
            return true;
        }
        try {
            $task = [
                'dataId' => uniqid('', true),
                'content' => $content,
            ];
            $result = AlibabaCloud::green()
                ->v20180509()
                ->textScan()
                ->jsonBody([
                    'tasks' => [$task],
                    'scenes' => ['antispam'],
                ])
                ->request();
            $result = $result->toArray();
        } catch (ClientException $e) {
            throw new SecurityException(sprintf('ali green ClientException : %s', $e->getErrorMessage()));
        } catch (ServerException $e) {
            throw new SecurityException(sprintf('ali green ServerException : %s', $e->getErrorMessage()));
        } catch (\Throwable $e) {
            throw new SecurityException(sprintf('ali green system Exception : %s', $e->getMessage()));
        }
        if ($result['code'] != 200) {
            throw new SecurityException('ali green result errorCode:%s  message:%s', $result['code'], $result['msg']);
        }
        $data = current($result['data']);
        if (isset($data['filteredContent'])) {
            throw new ContentErrorException('ali green result Do not pass', $result['data']);
        }
        return true;
    }

    protected function init()
    {
        try {
            AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
                ->regionId($this->regionId)// 设置客户端区域，
                ->timeout($this->timeout)  // 超时10秒，使用该客户端且没有单独设置的请求都使用此设置
                ->connectTimeout($this->connectTimeout)// 连接超时10秒
                ->debug($this->debug) // 开启调试
                ->asDefaultClient();
        } catch (\Throwable $e) {
            throw new SecurityException($e->getMessage());
        }
    }

}
