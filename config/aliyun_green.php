<?php
return array(
    /**
     * 相关配置
     */
    'AliyunGreen' => array(
        'accessKeyId' => '<yourAccessKeyId>',
        'accessKeySecret' => '<yourAccessKeySecret>',
        'regionId' => 'cn-hangzhou',
        #  允许通过的文本
        #  normal：正常文本 spam：含垃圾信息 ad：广告 politics：涉政 terrorism：暴恐 abuse：辱骂 porn：色情 flood：灌水 contraband：违禁 meaningless：无意义 customized：自定义（例如命中自定义关键词）
        #  这里填写正常文本之外的值
        'text_allow_labels' => [],
        #  允许通过的图片
        /**
         * 图片智能鉴黄    识别图片中的色情内容。    porn [normal：正常图片，无色情内容 sexy：性感图片 porn：色情图片]
         * 图片暴恐涉政识别    识别图片中的暴恐涉政内容。    terrorism [normal：正常图片 bloody：血腥 explosion：爆炸烟光 outfit：特殊装束 logo：特殊标识 weapon：武器 politics：涉政 violence ： 打斗 crowd：聚众 parade：游行 carcrash：车祸现场 flag：旗帜 location：地标 others：其他
         * 图文违规识别    识别图片中的广告以及文字违规信息。    ad [normal：正常图片 politics：文字含涉政内容 porn：文字含涉黄内容 abuse：文字含辱骂内容 terrorism：文字含暴恐内容 contraband：文字含违禁内容 spam：文字含其他垃圾内容 npx：牛皮藓广告 qrcode：包含二维码 programCode：包含小程序码 ad：其他广告]
         * 图片二维码识别    识别图片中的二维码。    qrcode [normal：正常图片 qrcode：含二维码的图片 programCode：含小程序码的图片]
         * 图片不良场景识别    识别直播或视频中出现的黑屏、黑边、昏暗画面，画中画，抽烟，打架等不良场景图片。    live [normal：正常图片 meaningless：无意义图片 PIP：画中画 smoking：吸烟 drivelive：车内直播]
         * 图片logo识别    识别图片中的logo信息，例如台标，商标等。    logo [normal：正常图片 TV：带有管控logo的图片 trademark：商标]
         */
        'image_allow_labels' => [
            //'porn',
            //'porn.',
            //'porn.sexy',
        ],
    ),
);