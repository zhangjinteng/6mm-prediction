<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionMerchantAdmin 是兼容旧调用的商户接口；新玩法配置页面使用 PredictionConfigAdmin。
 */
class PredictionMerchantAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Prediction\V1\GetOwnMerchantConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetOwnMerchantConfigResponse>
     */
    public function GetMerchantConfig(\Prediction\V1\GetOwnMerchantConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/GetMerchantConfig',
        $argument,
        ['\Prediction\V1\GetOwnMerchantConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * SaveMerchantSymbolConfig 对应商户玩法配置页的“保存配置”，成功即发布新商户版本。
     * @param \Prediction\V1\SaveMerchantSymbolConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveMerchantSymbolConfigResponse>
     */
    public function SaveMerchantSymbolConfig(\Prediction\V1\SaveMerchantSymbolConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/SaveMerchantSymbolConfig',
        $argument,
        ['\Prediction\V1\SaveMerchantSymbolConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SaveMerchantConfigDraftRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveMerchantConfigDraftResponse>
     */
    public function SaveMerchantConfigDraft(\Prediction\V1\SaveMerchantConfigDraftRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/SaveMerchantConfigDraft',
        $argument,
        ['\Prediction\V1\SaveMerchantConfigDraftResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\PublishMerchantConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\PublishMerchantConfigResponse>
     */
    public function PublishMerchantConfig(\Prediction\V1\PublishMerchantConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/PublishMerchantConfig',
        $argument,
        ['\Prediction\V1\PublishMerchantConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListMerchantConfigVersionsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\MerchantConfigVersionPage>
     */
    public function ListMerchantConfigVersions(\Prediction\V1\ListMerchantConfigVersionsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/ListMerchantConfigVersions',
        $argument,
        ['\Prediction\V1\MerchantConfigVersionPage', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListMerchantConfigAuditLogsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ListMerchantConfigAuditLogsResponse>
     */
    public function ListMerchantConfigAuditLogs(\Prediction\V1\ListMerchantConfigAuditLogsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/ListMerchantConfigAuditLogs',
        $argument,
        ['\Prediction\V1\ListMerchantConfigAuditLogsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetMerchantFeatureAvailabilityRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetMerchantFeatureAvailabilityResponse>
     */
    public function GetFeatureAvailability(\Prediction\V1\GetMerchantFeatureAvailabilityRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMerchantAdmin/GetFeatureAvailability',
        $argument,
        ['\Prediction\V1\GetMerchantFeatureAvailabilityResponse', 'decode'],
        $metadata, $options);
    }

}
