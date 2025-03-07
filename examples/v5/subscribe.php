<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

include __DIR__ . '/../bootstrap.php';

use Simps\MQTT\Client;
use Swoole\Coroutine;

Coroutine\run(function () {
    $config = getTestMQTT5ConnectConfig();
    $client = new Client($config, SWOOLE_MQTT_CONFIG);
    $will = [
        'topic' => 'simps-mqtt/user001/update',
        'qos' => 1,
        'retain' => 0,
        'message' => 'byebye',
        'properties' => [
            'will_delay_interval' => 60,
            'message_expiry_interval' => 60,
            'content_type' => 'test',
            'payload_format_indicator' => true, // false 0 1
        ],
    ];
    while (!$data = $client->connect(false, $will)) {
        Coroutine::sleep(3);
        $client->connect(true, $will);
    }
//    $topics['simps-mqtt/user001/get'] = 0;
//    $topics['simps-mqtt/user001/update'] = 2;
    $topics['simps-mqtt/user001/get'] = [
        'qos' => 1,
        'no_local' => true,
        'retain_as_published' => true,
        'retain_handling' => 2,
    ];
    $topics['simps-mqtt/user001/update'] = [
        'qos' => 2,
        'no_local' => false,
        'retain_as_published' => true,
        'retain_handling' => 2,
    ];
    $timeSincePing = time();
    $res = $client->subscribe($topics);
    var_dump($res);
    while (true) {
        $buffer = $client->recv();
        var_dump($buffer);
        if ($buffer && $buffer !== true) {
            $timeSincePing = time();
        }
        if (isset($config['keep_alive']) && $timeSincePing < (time() - $config['keep_alive'])) {
            $buffer = $client->ping();
            if ($buffer) {
                echo 'send ping success' . PHP_EOL;
                $timeSincePing = time();
            } else {
                $client->close();
                break;
            }
        }
    }
});
