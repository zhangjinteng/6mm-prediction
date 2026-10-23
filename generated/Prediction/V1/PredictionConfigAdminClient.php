<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionConfigAdmin 是平台管理员和商户管理员共用的当前玩法配置入口。
 * 服务根据可信 metadata 中是否存在 x-agent-id 自动确定配置范围，请求体不得选择商户身份。
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
     * Identity-derived root merchant configuration; main accounts only.
     * @param \Prediction\V1\ListOwnerConfigsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ListOwnerConfigsResponse>
     */
    public function ListOwnerConfigs(\Prediction\V1\ListOwnerConfigsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/ListOwnerConfigs',
        $argument,
        ['\Prediction\V1\ListOwnerConfigsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetOwnerConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetOwnerConfigResponse>
     */
    public function GetOwnerConfig(\Prediction\V1\GetOwnerConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/GetOwnerConfig',
        $argument,
        ['\Prediction\V1\GetOwnerConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SaveOwnerConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveOwnerConfigResponse>
     */
    public function SaveOwnerConfig(\Prediction\V1\SaveOwnerConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/SaveOwnerConfig',
        $argument,
        ['\Prediction\V1\SaveOwnerConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * ListSymbolConfigs 查询当前可信范围内的全部交易对配置，可按玩法过滤。
     * @param \Prediction\V1\ListSymbolConfigsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ListSymbolConfigsResponse>
     */
    public function ListSymbolConfigs(\Prediction\V1\ListSymbolConfigsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/ListSymbolConfigs',
        $argument,
        ['\Prediction\V1\ListSymbolConfigsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetSymbolConfig 查询当前可信范围内一个玩法和交易对的完整多周期配置。
     * @param \Prediction\V1\GetSymbolConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetSymbolConfigResponse>
     */
    public function GetSymbolConfig(\Prediction\V1\GetSymbolConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/GetSymbolConfig',
        $argument,
        ['\Prediction\V1\GetSymbolConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * SaveSymbolConfig 保存当前玩法和交易对的完整配置，成功即发布新版本。
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

    /**
     * GetSiteConfig 查询精确站点、玩法和交易对的已存与当前生效配置。
     * @param \Prediction\V1\GetSiteConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetSiteConfigResponse>
     */
    public function GetSiteConfig(\Prediction\V1\GetSiteConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/GetSiteConfig',
        $argument,
        ['\Prediction\V1\GetSiteConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * SaveSiteConfig 保存精确站点配置；授权站点只来自服务端文件门禁。
     * @param \Prediction\V1\SaveSiteConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveSiteConfigResponse>
     */
    public function SaveSiteConfig(\Prediction\V1\SaveSiteConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionConfigAdmin/SaveSiteConfig',
        $argument,
        ['\Prediction\V1\SaveSiteConfigResponse', 'decode'],
        $metadata, $options);
    }

}
