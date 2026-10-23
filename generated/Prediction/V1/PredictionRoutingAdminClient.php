<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionRoutingAdmin 仅供可信 platform_admin 控制面调用。
 * operator、source_service、trace_id 与 capability 全部来自服务端认证 metadata，
 * 请求体不得自报或覆盖这些身份字段。
 */
class PredictionRoutingAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Prediction\V1\GetSiteBindingRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetSiteBindingResponse>
     */
    public function GetSiteBinding(\Prediction\V1\GetSiteBindingRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionRoutingAdmin/GetSiteBinding',
        $argument,
        ['\Prediction\V1\GetSiteBindingResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\PrepareSiteBindingChangeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\PrepareSiteBindingChangeResponse>
     */
    public function PrepareSiteBindingChange(\Prediction\V1\PrepareSiteBindingChangeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionRoutingAdmin/PrepareSiteBindingChange',
        $argument,
        ['\Prediction\V1\PrepareSiteBindingChangeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\CommitSiteBindingChangeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\CommitSiteBindingChangeResponse>
     */
    public function CommitSiteBindingChange(\Prediction\V1\CommitSiteBindingChangeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionRoutingAdmin/CommitSiteBindingChange',
        $argument,
        ['\Prediction\V1\CommitSiteBindingChangeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\CancelSiteBindingChangeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\CancelSiteBindingChangeResponse>
     */
    public function CancelSiteBindingChange(\Prediction\V1\CancelSiteBindingChangeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionRoutingAdmin/CancelSiteBindingChange',
        $argument,
        ['\Prediction\V1\CancelSiteBindingChangeResponse', 'decode'],
        $metadata, $options);
    }

}
