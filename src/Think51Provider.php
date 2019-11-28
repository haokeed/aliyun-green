<?php

namespace Haokeed\AliyunGreen;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\facade\Config as Config;
use think\facade\Log as Log;

class Think51Provider
{

    protected $error = '';
    protected $config = [];

    public function __construct($config = NULL)
    {
        if (is_null($config)) {
            $config = Config::get('aliyun_green.')['AliyunGreen'];
        }
        $this->config = $config;
        $this->config['image_allow_labels'] = $this->getImageAllowLabels();
        AlibabaCloud::accessKeyClient($config['accessKeyId'], $config['accessKeySecret'])
            ->regionId($config['regionId'])// 设置客户端区域，使用该客户端且没有单独设置的请求都使用此设置
            ->timeout(6)// 超时10秒，使用该客户端且没有单独设置的请求都使用此设置
            ->connectTimeout(10)// 连接超时10秒，当单位小于1，则自动转换为毫秒，使用该客户端且没有单独设置的请求都使用此设置
            //->debug(true) 								// 开启调试，CLI下会输出详细信息，使用该客户端且没有单独设置的请求都使用此设置
            ->asDefaultClient();
    }

    /**
     * 文本检测
     */
    public function textScan($content)
    {
        $task = array(
            'dataId' => uniqid(),
            'content' => $content
        );
        $body = array(
            "tasks" => array($task),
            "scenes" => array("antispam")
        );
        $params = array();
        return $this->roaRequest('/green/text/scan', $body, $params);
    }

    /**
     * 图片检测
     */
    public function imageScan($url)
    {
        $task = array(
            'dataId' => uniqid(),
            'url' => $url
        );
        $body = array(
            "tasks" => array($task),
            "scenes" => array("porn", "terrorism")
        );
        $params = array();
        return $this->roaRequest('/green/image/scan', $body, $params);
    }

    /**
     * 简易检测内容,直接返回true|false结果的处理
     * @param $content
     * @return bool
     */
    public function simpleTextScan($content): bool
    {
        $res = $this->textScan($content);
        if ($res['code'] == 200) {
            // 单个图片判断结果
            if ($res['data'][0]['code'] != 200) {
                $this->error = $res['data'][0]['msg'];
                return false;
            }
            // 过滤结果
            if ($res['data'][0]['results'][0]['label'] == 'normal' || in_array($res['data'][0]['results'][0]['label'], $this->config['text_allow_labels'] ?: [])) {
                return true;
            }
            $this->error = $res['data'][0]['results'][0]['label'];
            return false;
        } else {
            $this->error = $res['msg'];
            return false;
        }
    }


    /**
     * 简易检测图片,直接返回true|false结果的处理
     * @param $content
     * @return bool
     */
    public function simpleImageScan($url): bool
    {
        $res = $this->imageScan($url);
        if ($res['code'] == 200) {
            // 单个图片判断结果
            if ($res['data'][0]['code'] != 200) {
                $this->error = $res['data'][0]['msg'];
                return false;
            }
            $err_arr = [];
            foreach ($res['data'][0]['results'] as $k => $item) {
                if ($item['label'] == 'normal' || in_array($item['scene'] . "." . $item['label'], $this->config['image_allow_labels'])) {
                    continue;
                } else {
                    $err_arr[] = $item['scene'] . "." . $item['label'];
                }
            }
            if (empty($err_arr)) {
                return true;
            }

            $this->error = implode(',', $err_arr);
            return false;
        } else {
            $this->error = $res['msg'];
            return false;
        }
    }

    private function getImageAllowLabels()
    {
        $static_config = [
            'porn' => [
                "sexy",
                "porn"
            ],
            'terrorism' => [
                "bloody",
                "explosion",
                "outfit",
                "logo",
                "weapon",
                "politics",
                "violence",
                "crowd",
                "parade",
                "carcrash",
                "flag",
                "location",
                "others"
            ],
            'ad' => [
                "politics",
                "porn",
                "abuse",
                "terrorism",
                "contraband",
                "spam",
                "npx",
                "qrcode",
                "programCode",
                "ad",
            ],
            "qrcode" => [
                "qrcode",
                "programCode",
            ],
            "live" => [
                "meaningless",
                "PIP",
                "smoking",
                "drivelive",
            ],
            "logo" => [
                "TV",
                "trademark",
            ]
        ];
        $config = isset($this->config['image_allow_labels']) && is_array($this->config['image_allow_labels']) ? $this->config['image_allow_labels'] : [];
        $new_config = [];
        foreach ($config as $value) {
            $value = substr($value, -1) == '.' ? substr($value, 0, strlen($value) - 1) : $value;
            if (strpos($value, '.') === false) {
                if (isset($static_config[$value])) {
                    foreach ($static_config[$value] as $lab) {
                        $new_config[] = $value . "." . $lab;
                    }
                }
            } else {
                $new_config[] = $value;
            }
        }
        $new_config = array_filter(array_unique($new_config));
        return $new_config;
    }

    public function getError()
    {
        return $this->error;
    }


    private function roaRequest($action, $body, $params)
    {
        try {
            $result = AlibabaCloud::roaRequest()
                ->product('Green')
                ->version('2018-05-09')
                ->pathPattern($action)
                ->method('POST')
                ->options([
                    'query' => $params
                ])
                ->body(json_encode($body))
                ->request();
            if ($result->isSuccess()) {
                return $result->toArray();
            } else {
                return $result;
            }
        } catch (ClientException $e) {
            Log::error('AliyunGreen \ textScan', $e->getErrorMessage());
            return null;
        } catch (ServerException $e) {
            Log::error('AliyunGreen \ textScan', $e->getErrorMessage());
            return null;
        }
    }
}
