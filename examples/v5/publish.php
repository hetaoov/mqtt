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
    $client = new Client(getTestMQTT5ConnectConfig(), SWOOLE_MQTT_CONFIG);
    while (!$client->connect()) {
        Coroutine::sleep(3);
        $client->connect();
    }
    while (true) {
        $response = $client->publish(
            'simps-mqtt/user001/update',
            '{"time":' . time() . '}',
            1,
            0,
            0,
            [
                'topic_alias' => 1,
                'message_expiry_interval' => 12,
            ]
        );
        var_dump($response);
        Coroutine::sleep(3);
    }
});
