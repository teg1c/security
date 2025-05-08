<?php

declare(strict_types=1);
/**
 * This file is part of Security.
 *
 * @Author     Tegic
 * @Contact  https://github.com/teg1c
 */

namespace Tegic\Security\Driver;

use AlibabaCloud\SDK\Green\V20220302\Models\TextModerationPlusRequest;
use AlibabaCloud\Tea\Exception\TeaUnableRetryError;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use AlibabaCloud\SDK\Green\V20220302\Green;

use Tegic\Security\Exception\ContentErrorException;
use Tegic\Security\Exception\SecurityException;

class AliGreenDriver implements DriverInterface
{

    private $accessKeyId;

    private $accessKeySecret;

    private $regionId;
    private $endpoint;

    private $debug;

    private $timeout;

    private $connectTimeout;

    protected $options;

    protected $client;

    public function __construct($config = [])
    {
        $this->accessKeyId = $config['access_key_id'] ?? '';
        $this->accessKeySecret = $config['access_key_secret'] ?? '';
        $this->regionId = $config['region_id'] ?? '';
        $this->endpoint = $config['endpoint'] ?? '';
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
        $request = new TextModerationPlusRequest();
        $request->service = $this->options['service'] ?? 'ugc_moderation_byllm';
        $serviceParameters = ["content" => $content];

        $request->serviceParameters = json_encode($serviceParameters);

        $runtime = new RuntimeOptions();
        $runtime->readTimeout = $this->timeout * 1000;
        $runtime->connectTimeout = $this->connectTimeout * 1000;
        /** @var Green $client */
        $client = $this->client;
        try {
            $response = $client->textModerationPlusWithOptions($request, $runtime);
            if (200 != $response->statusCode) {
                throw new SecurityException('AliGreenDriver: text moderation not success. statusCode:' . $response->statusCode);
            }
            $body = $response->body;
            if (200 != $body->code) {
                throw new ContentErrorException('AliGreenDriver: text moderation not success. code:' . $body->code, $body->toMap());
            }
            $result = $body->toMap();
        } catch (TeaUnableRetryError $e) {
            throw new SecurityException('AliGreenDriver: text moderation not success. ' . $e->getMessage());
        } catch (\Throwable $e) {
            // SecurityException 和 ContentErrorException 排除
            if ($e instanceof SecurityException || $e instanceof ContentErrorException) {
                throw $e;
            }
            throw new SecurityException('AliGreenDriver: text moderation not success. ' . $e->getMessage());
        }
        $data = $result['Data'] ?? [];
        $riskLevel = $data['RiskLevel'] ?? "none";
        if ($riskLevel !== "none") {
            throw new ContentErrorException('AliGreenDriver: text moderation not success. riskLevel:' . $riskLevel, $data);
        }
        return $data;
    }

    protected function init()
    {
        $config = new Config([

            "accessKeyId" => $this->accessKeyId,
            "accessKeySecret" => $this->accessKeySecret,
            "endpoint" => $this->endpoint,
            "regionId" => $this->regionId
        ]);
        // 注意，此处实例化的client请尽可能重复使用，避免重复建立连接，提升检测性能。
        $this->client = new Green($config);
    }

    public function setOption(array $options = [])
    {
        $this->options = $options;
    }
}
