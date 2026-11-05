<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Prediction\V1;

/**
 * PredictionPlatformRobotAdmin manages the platform-only robot registry and
 * audited operations. Registry registration alone never deposits or enables
 * an account; funds operations retain their immutable original command IDs.
 */
class PredictionPlatformRobotAdminClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Soft removal: disable new work and hide from the roster; keep history.
     * @param \Prediction\V1\RemovePlatformRobotRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function RemovePlatformRobot(\Prediction\V1\RemovePlatformRobotRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/RemovePlatformRobot',
        $argument,
        ['\Prediction\V1\RemovePlatformRobotResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetPlatformRobotRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPlatformRobot(\Prediction\V1\GetPlatformRobotRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/GetPlatformRobot',
        $argument,
        ['\Prediction\V1\GetPlatformRobotResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListPlatformRobotsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ListPlatformRobots(\Prediction\V1\ListPlatformRobotsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/ListPlatformRobots',
        $argument,
        ['\Prediction\V1\ListPlatformRobotsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\RegisterPlatformRobotRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function RegisterPlatformRobot(\Prediction\V1\RegisterPlatformRobotRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/RegisterPlatformRobot',
        $argument,
        ['\Prediction\V1\RegisterPlatformRobotResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SetPlatformRobotEnabledRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function SetPlatformRobotEnabled(\Prediction\V1\SetPlatformRobotEnabledRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/SetPlatformRobotEnabled',
        $argument,
        ['\Prediction\V1\SetPlatformRobotEnabledResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetPlatformRobotBalancesRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPlatformRobotBalances(\Prediction\V1\GetPlatformRobotBalancesRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/GetPlatformRobotBalances',
        $argument,
        ['\Prediction\V1\GetPlatformRobotBalancesResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\CreatePlatformRobotOperationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreatePlatformRobotOperation(\Prediction\V1\CreatePlatformRobotOperationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/CreatePlatformRobotOperation',
        $argument,
        ['\Prediction\V1\CreatePlatformRobotOperationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetPlatformRobotOperationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPlatformRobotOperation(\Prediction\V1\GetPlatformRobotOperationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/GetPlatformRobotOperation',
        $argument,
        ['\Prediction\V1\GetPlatformRobotOperationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\ListPlatformRobotOperationsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ListPlatformRobotOperations(\Prediction\V1\ListPlatformRobotOperationsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/ListPlatformRobotOperations',
        $argument,
        ['\Prediction\V1\ListPlatformRobotOperationsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\CreatePlatformRobotUserRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreatePlatformRobotUser(\Prediction\V1\CreatePlatformRobotUserRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/CreatePlatformRobotUser',
        $argument,
        ['\Prediction\V1\CreatePlatformRobotOperationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\GetPlatformRobotAutoDepositPolicyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetPlatformRobotAutoDepositPolicy(\Prediction\V1\GetPlatformRobotAutoDepositPolicyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/GetPlatformRobotAutoDepositPolicy',
        $argument,
        ['\Prediction\V1\GetPlatformRobotAutoDepositPolicyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Prediction\V1\SavePlatformRobotAutoDepositPolicyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function SavePlatformRobotAutoDepositPolicy(\Prediction\V1\SavePlatformRobotAutoDepositPolicyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/prediction.v1.PredictionPlatformRobotAdmin/SavePlatformRobotAutoDepositPolicy',
        $argument,
        ['\Prediction\V1\SavePlatformRobotAutoDepositPolicyResponse', 'decode'],
        $metadata, $options);
    }

}
