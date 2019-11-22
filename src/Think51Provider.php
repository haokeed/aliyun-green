<?php
namespace Haokeed\AliyunGreen;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\facade\Config as Config;
use think\facade\Log as Log;

class Think51Provider {

	public function __construct($config = NULL) {
		if (is_null($config)) {
			$config = Config::get('aliyun_green');
		}
		AlibabaCloud::accessKeyClient($config['accessKeyId'], $config['accessKeySecret'])
			->regionId($config['regionId']) // 设置客户端区域，使用该客户端且没有单独设置的请求都使用此设置
			->timeout(6) 										// 超时10秒，使用该客户端且没有单独设置的请求都使用此设置
			->connectTimeout(10) 						// 连接超时10秒，当单位小于1，则自动转换为毫秒，使用该客户端且没有单独设置的请求都使用此设置
			//->debug(true) 								// 开启调试，CLI下会输出详细信息，使用该客户端且没有单独设置的请求都使用此设置
			->asDefaultClient();
	}

	/**
	 * 文本检测
	 */
	public function textScan($content) {
		$task = array(
			'dataId' =>  uniqid(),
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
	public function imageScan($url) {
		$task = array(
			'dataId' =>  uniqid(),
			'url' => $url
		);
		$body = array(
			"tasks" => array($task),
			"scenes" => array("porn", "terrorism")
		);
		$params = array();
		return $this->roaRequest('/green/image/scan', $body, $params);
	}

	private function roaRequest($action, $body, $params) {
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
			if($result->isSuccess()) {
				return $result->toArray();
			} else {
				return $result;
			}
		} catch (ClientException $e) {
            Log::error('AliyunGreen \ textScan',$e->getErrorMessage());
			return null;
		} catch (ServerException $e) {
            Log::error('AliyunGreen \ textScan',$e->getErrorMessage());
			return null;
		}
	}
}
