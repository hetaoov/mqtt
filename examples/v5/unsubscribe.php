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
    $topics = ['simps-mqtt/user001/get'];
    $res = $client->unsubscribe($topics);
    var_dump($res);
});
