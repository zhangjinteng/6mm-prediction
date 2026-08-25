<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionPlatformAdmin 是兼容旧调用的管理接口；新玩法配置页面使用 PredictionConfigAdmin。
 */
class PredictionPlatformAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Prediction\V1\GetPlatformTemplateRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetPlatformTemplateResponse>
     */
    public function GetPlatformTemplate(\Prediction\V1\GetPlatformTemplateRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/GetPlatformTemplate',
        $argument,
        ['\Prediction\V1\GetPlatformTemplateResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * SavePlatformSymbolConfig 对应玩法配置页的“保存配置”，成功即发布新版本。
     * @param \Prediction\V1\SavePlatformSymbolConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SavePlatformSymbolConfigResponse>
     */
    public function SavePlatformSymbolConfig(\Prediction\V1\SavePlatformSymbolConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/SavePlatformSymbolConfig',
        $argument,
        ['\Prediction\V1\SavePlatformSymbolConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SavePlatformTemplateDraftRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SavePlatformTemplateDraftResponse>
     */
    public function SavePlatformTemplateDraft(\Prediction\V1\SavePlatformTemplateDraftRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/SavePlatformTemplateDraft',
        $argument,
        ['\Prediction\V1\SavePlatformTemplateDraftResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\PublishPlatformTemplateRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\PublishPlatformTemplateResponse>
     */
    public function PublishPlatformTemplate(\Prediction\V1\PublishPlatformTemplateRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/PublishPlatformTemplate',
        $argument,
        ['\Prediction\V1\PublishPlatformTemplateResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListPlatformTemplateVersionsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\PlatformTemplateVersionPage>
     */
    public function ListPlatformTemplateVersions(\Prediction\V1\ListPlatformTemplateVersionsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/ListPlatformTemplateVersions',
        $argument,
        ['\Prediction\V1\PlatformTemplateVersionPage', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ActivatePlatformTemplateVersionRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ActivatePlatformTemplateVersionResponse>
     */
    public function ActivatePlatformTemplateVersion(\Prediction\V1\ActivatePlatformTemplateVersionRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/ActivatePlatformTemplateVersion',
        $argument,
        ['\Prediction\V1\ActivatePlatformTemplateVersionResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListMerchantConfigStatesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\MerchantConfigStatePage>
     */
    public function ListMerchantConfigStates(\Prediction\V1\ListMerchantConfigStatesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/ListMerchantConfigStates',
        $argument,
        ['\Prediction\V1\MerchantConfigStatePage', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetPlatformMerchantConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetPlatformMerchantConfigResponse>
     */
    public function GetMerchantConfig(\Prediction\V1\GetPlatformMerchantConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/GetMerchantConfig',
        $argument,
        ['\Prediction\V1\GetPlatformMerchantConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\DisableMerchantOverrideRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\DisableMerchantOverrideResponse>
     */
    public function DisableMerchantOverride(\Prediction\V1\DisableMerchantOverrideRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/DisableMerchantOverride',
        $argument,
        ['\Prediction\V1\DisableMerchantOverrideResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ResetMerchantConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ResetMerchantConfigResponse>
     */
    public function ResetMerchantConfig(\Prediction\V1\ResetMerchantConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/ResetMerchantConfig',
        $argument,
        ['\Prediction\V1\ResetMerchantConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetTemplateReconciliationJobRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetTemplateReconciliationJobResponse>
     */
    public function GetTemplateReconciliationJob(\Prediction\V1\GetTemplateReconciliationJobRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/GetTemplateReconciliationJob',
        $argument,
        ['\Prediction\V1\GetTemplateReconciliationJobResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListConfigAuditLogsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ListConfigAuditLogsResponse>
     */
    public function ListConfigAuditLogs(\Prediction\V1\ListConfigAuditLogsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/ListConfigAuditLogs',
        $argument,
        ['\Prediction\V1\ListConfigAuditLogsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetPlatformFeatureAvailabilityRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetPlatformFeatureAvailabilityResponse>
     */
    public function GetFeatureAvailability(\Prediction\V1\GetPlatformFeatureAvailabilityRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformAdmin/GetFeatureAvailability',
        $argument,
        ['\Prediction\V1\GetPlatformFeatureAvailabilityResponse', 'decode'],
        $metadata, $options);
    }

}
