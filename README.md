# 阿里云Green内容安全扩展
PhalApi 2.x扩展类库，基于Aliyun的内容安全扩展。

## 安装和配置

执行```composer require haokeed/aliyun-green```。  

安装成功后，将config文件夹下的aliyun_green.php文件复制到项目的config目录下面,然后进行设置：  
```php
    /**
     * 阿里云Green相关配置
     */
    'AliyunGreen' =>  array(
        'accessKeyId'       => '<yourAccessKeyId>',
        'accessKeySecret'   => '<yourAccessKeySecret>',
        'regionId'          => 'cn-hangzhou',
    ),
```
并根据自己的情况修改填充。  

## 注册
在/application/provider.php文件中，注册：  
```php
return [
    'txtgreen'      =>\Haokeed\AliyunGreen\Think51Provider::class
];
```

## 使用
使用方式：
```php
  app('txtgreen')->textScan('测试内容');
```  

