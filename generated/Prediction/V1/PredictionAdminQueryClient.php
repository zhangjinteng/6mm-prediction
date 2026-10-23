<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionAdminQuery 仅供已认证的 platform_admin / agent_admin 服务调用。
 * platform_admin 可按 agent_id 查询；agent_admin 的商户范围始终由可信 principal 覆盖。
 */
class PredictionAdminQueryClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Prediction\V1\ListAdminRoundsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AdminRoundPage>
     */
    public function ListRounds(\Prediction\V1\ListAdminRoundsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdminQuery/ListRounds',
        $argument,
        ['\Prediction\V1\AdminRoundPage', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListAdminOrdersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AdminOrderPage>
     */
    public function ListOrders(\Prediction\V1\ListAdminOrdersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdminQuery/ListOrders',
        $argument,
        ['\Prediction\V1\AdminOrderPage', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetAdminOrderRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\AdminOrderDetail>
     */
    public function GetOrder(\Prediction\V1\GetAdminOrderRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionAdminQuery/GetOrder',
        $argument,
        ['\Prediction\V1\AdminOrderDetail', 'decode'],
        $metadata, $options);
    }

}
