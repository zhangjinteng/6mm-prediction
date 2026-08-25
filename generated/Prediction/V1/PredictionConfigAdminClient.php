<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionConfigAdmin 是平台管理员和商户管理员共用的玩法配置页面入口。
 * 服务根据可信 metadata 中是否存在 x-merchant-id 自动确定配置范围，请求体不得选择商户身份。
 */
class PredictionConfigAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Prediction\V1\GetGameplayConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetGameplayConfigResponse>
     */
    public function GetGameplayConfig(\Prediction\V1\GetGameplayConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/GetGameplayConfig',
        $argument,
        ['\Prediction\V1\GetGameplayConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * SaveSymbolConfig 对应玩法配置页的“保存配置”，成功即发布新版本。
     * @param \Prediction\V1\SaveSymbolConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveSymbolConfigResponse>
     */
    public function SaveSymbolConfig(\Prediction\V1\SaveSymbolConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/SaveSymbolConfig',
        $argument,
        ['\Prediction\V1\SaveSymbolConfigResponse', 'decode'],
        $metadata, $options);
    }

}
