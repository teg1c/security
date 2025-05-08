<?php

declare(strict_types=1);
/**
 * This file is part of Security.
 *
 * @Author     Tegic
 * @Contact  https://github.com/teg1c
 */
namespace Tegic\Security\Driver;

use Tegic\Security\Exception\ContentErrorException;
use Tegic\Security\Exception\SecurityException;
use TencentCloud\Cms\V20190321\CmsClient;
use TencentCloud\Cms\V20190321\Models\TextModerationRequest;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

class TencentDriver implements DriverInterface
{
    private $accessKeyId;

    private $accessKeySecret;

    private $regionId;

    private $level = [];

    public function __construct($config = [])
    {
        $this->accessKeyId = $config['access_key_id'] ?? '';
        $this->accessKeySecret = $config['access_key_secret'] ?? '';
        $this->regionId = $config['region_id'] ?? '';
        $this->level = $config['level'] ?? [];
    }

    public function text($content = '')
    {
        if (empty($content)) {
            return true;
        }
        try {
            $service = $this->cmsService();
            $request = new TextModerationRequest();
            $params = [
                'Content' => base64_encode($content),
            ];
            $request->fromJsonString(json_encode($params));
            $response = $service->TextModeration($request);
            $result = json_decode($response->getData()->toJsonString(), true);
        } catch (TencentCloudSDKException $exception) {
            throw new SecurityException(sprintf('tencent TencentCloudSDKException : %s', $exception->getMessage()));
        } catch (\Throwable $exception) {
            throw new SecurityException(sprintf('tencent system Exception : %s', $exception->getMessage()));
        }
        if ($result['EvilFlag'] === 1 || ($result['EvilFlag'] === 1 && $this->level && in_array($result->EvilType, $this->level))) {
            throw new ContentErrorException('ali green result Do not pass', $result);
        }
        return true;
    }

    protected function cmsService()
    {
        $credential = new Credential($this->accessKeyId, $this->accessKeySecret);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("cms.{$this->regionId}.tencentcloudapi.com");

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);

        return new CmsClient($credential, $this->regionId, $clientProfile);
    }

    public function setOption(array $options = [])
    {
        // TODO: Implement setOption() method.
    }
}
