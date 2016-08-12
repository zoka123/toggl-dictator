<?php

use GuzzleHttp\Client;

require 'vendor/autoload.php';

$token = '53adff7aee6660bd6d0b3c5d02ae329e';
$url = 'https://www.toggl.com/api/v8/time_entries/current';
$client = new Client();


$seconds = 60;
$notTrackingCount = 5;
$notTrackingLockLimit = 1;
$lockFlag = true;

$doNotify = function ($text) {
    $notifyCommand = 'notify-send "' . $text . '"';
    echo shell_exec($notifyCommand);
};

while (true) {

    $data = $client->get($url,
        ['auth' => [$token, 'api_token']]
    );

    $data = json_decode($data->getBody(), true);

//    var_dump($data);Start tracking your time, PC Will be locked

    echo shell_exec('gnome-screensaver-command -q') . date('d.m.y. H:i', time());

    if (is_null($data['data'])) {
        $doNotify('Track your time!');
        $notTrackingCount++;

        if ($notTrackingCount == $notTrackingLockLimit) {
            $doNotify('PC is locking!');
            sleep(5);
            $notTrackingCount = 0;
            $lockCommand = 'gnome-screensaver-command -l';
            echo shell_exec($lockCommand);
        }
    } else {
        $notTrackingCount = 0;
    }

    sleep($seconds);
}

