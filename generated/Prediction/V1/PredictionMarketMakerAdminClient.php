<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionMarketMakerAdmin is the platform-only control plane for global
 * UP_DOWN liquidity strategies, verified robot bindings, and dynamic readiness.
 */
class PredictionMarketMakerAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Prediction\V1\GetMarketMakerSymbolConfigurationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetMarketMakerSymbolConfigurationResponse>
     */
    public function GetSymbolConfiguration(\Prediction\V1\GetMarketMakerSymbolConfigurationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/GetSymbolConfiguration',
        $argument,
        ['\Prediction\V1\GetMarketMakerSymbolConfigurationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SaveMarketMakerSymbolConfigurationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveMarketMakerSymbolConfigurationResponse>
     */
    public function SaveSymbolConfiguration(\Prediction\V1\SaveMarketMakerSymbolConfigurationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/SaveSymbolConfiguration',
        $argument,
        ['\Prediction\V1\SaveMarketMakerSymbolConfigurationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetMarketMakerStrategyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\GetMarketMakerStrategyResponse>
     */
    public function GetStrategy(\Prediction\V1\GetMarketMakerStrategyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/GetStrategy',
        $argument,
        ['\Prediction\V1\GetMarketMakerStrategyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SaveMarketMakerStrategyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\SaveMarketMakerStrategyResponse>
     */
    public function SaveStrategy(\Prediction\V1\SaveMarketMakerStrategyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/SaveStrategy',
        $argument,
        ['\Prediction\V1\SaveMarketMakerStrategyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListRobotBindingsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\RobotBindingPage>
     */
    public function ListRobotBindings(\Prediction\V1\ListRobotBindingsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/ListRobotBindings',
        $argument,
        ['\Prediction\V1\RobotBindingPage', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\UpsertRobotBindingRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\UpsertRobotBindingResponse>
     */
    public function UpsertRobotBinding(\Prediction\V1\UpsertRobotBindingRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/UpsertRobotBinding',
        $argument,
        ['\Prediction\V1\UpsertRobotBindingResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * DeleteRobotBinding soft-disables the binding and preserves its globally
     * unique user reservation and audit history.
     * @param \Prediction\V1\DeleteRobotBindingRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\DeleteRobotBindingResponse>
     */
    public function DeleteRobotBinding(\Prediction\V1\DeleteRobotBindingRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/DeleteRobotBinding',
        $argument,
        ['\Prediction\V1\DeleteRobotBindingResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetMarketMakerReadinessRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\MarketMakerReadiness>
     */
    public function GetReadiness(\Prediction\V1\GetMarketMakerReadinessRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/GetReadiness',
        $argument,
        ['\Prediction\V1\MarketMakerReadiness', 'decode'],
        $metadata, $options);
    }

    /**
     * Readonly operational facts; never schedules work or calls funding.
     * @param \Prediction\V1\ListMarketMakerRuntimeStatusesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Prediction\V1\MarketMakerRuntimeStatusPage>
     */
    public function ListRuntimeStatuses(\Prediction\V1\ListMarketMakerRuntimeStatusesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionMarketMakerAdmin/ListRuntimeStatuses',
        $argument,
        ['\Prediction\V1\MarketMakerRuntimeStatusPage', 'decode'],
        $metadata, $options);
    }

}
