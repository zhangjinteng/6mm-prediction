<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionAdmin 只接受可信 Gateway 管理端的内部调用。
 * Gateway 必须从已经验证的管理上下文注入 operator 和 capability；变更请求体不得携带 operator_id。
 */
class PredictionAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * 需要 prediction.admin.order.read。
     * @param \Prediction\V1\ListAdminOrdersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AdminOrderPage>
     */
    public function ListOrders(\Prediction\V1\ListAdminOrdersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/ListOrders',
        $argument,
        ['\Prediction\V1\AdminOrderPage', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.order.read。
     * @param \Prediction\V1\GetAdminOrderRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AdminOrderDetail>
     */
    public function GetOrder(\Prediction\V1\GetAdminOrderRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/GetOrder',
        $argument,
        ['\Prediction\V1\AdminOrderDetail', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.round.read。
     * @param \Prediction\V1\ListAdminRoundsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AdminRoundPage>
     */
    public function ListRounds(\Prediction\V1\ListAdminRoundsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/ListRounds',
        $argument,
        ['\Prediction\V1\AdminRoundPage', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.exception.read。
     * @param \Prediction\V1\ListExceptionalTasksRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ExceptionalTaskPage>
     */
    public function ListExceptionalTasks(\Prediction\V1\ListExceptionalTasksRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/ListExceptionalTasks',
        $argument,
        ['\Prediction\V1\ExceptionalTaskPage', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.config.read。
     * @param \Prediction\V1\ListConfigVersionsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ConfigVersionPage>
     */
    public function ListConfigVersions(\Prediction\V1\ListConfigVersionsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/ListConfigVersions',
        $argument,
        ['\Prediction\V1\ConfigVersionPage', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.audit.read。
     * @param \Prediction\V1\ListAuditLogsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AuditLogPage>
     */
    public function ListAuditLogs(\Prediction\V1\ListAuditLogsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/ListAuditLogs',
        $argument,
        ['\Prediction\V1\AuditLogPage', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.config.edit。
     * @param \Prediction\V1\SavePlatformConfigRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SavePlatformConfigResponse>
     */
    public function SavePlatformConfig(\Prediction\V1\SavePlatformConfigRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/SavePlatformConfig',
        $argument,
        ['\Prediction\V1\SavePlatformConfigResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.config.edit。
     * @param \Prediction\V1\SaveRuleDraftRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveRuleDraftResponse>
     */
    public function SaveRuleDraft(\Prediction\V1\SaveRuleDraftRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/SaveRuleDraft',
        $argument,
        ['\Prediction\V1\SaveRuleDraftResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.config.publish。
     * @param \Prediction\V1\PublishRuleDraftRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\PublishRuleDraftResponse>
     */
    public function PublishRuleDraft(\Prediction\V1\PublishRuleDraftRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/PublishRuleDraft',
        $argument,
        ['\Prediction\V1\PublishRuleDraftResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.config.toggle。
     * @param \Prediction\V1\ToggleRuleVersionRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\ToggleRuleVersionResponse>
     */
    public function ToggleRuleVersion(\Prediction\V1\ToggleRuleVersionRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/ToggleRuleVersion',
        $argument,
        ['\Prediction\V1\ToggleRuleVersionResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * 需要 prediction.admin.funds.requeue。
     * @param \Prediction\V1\RequeueOutboxRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\RequeueOutboxResponse>
     */
    public function RequeueOutbox(\Prediction\V1\RequeueOutboxRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdmin/RequeueOutbox',
        $argument,
        ['\Prediction\V1\RequeueOutboxResponse', 'decode'],
        $metadata, $options);
    }

}
