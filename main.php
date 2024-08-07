5<?php

// Подключение файла конфигурации
require_once 'config.php';

// Функция для отправки сообщения в Telegram
function send_telegram_message($message, $telegram_bot_token, $chat_id) {
    $url = "https://api.telegram.org/bot$telegram_bot_token/sendMessage";
    $data = array('chat_id' => $chat_id, 'text' => $message);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Функция для проверки статуса валидатора
function check_validator_status($url, $validator_name) {
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['validator']['jailed'] ? $validator_name : null;
}

// URL-ы для проверки валидаторов
$validators = [
    'Umee' => "https://umee-api.polkachu.com/cosmos/staking/v1beta1/validators/umeevaloper1as74ldxuqysp2w53hwxejgk25eav2gummryv6y",
    'Arkeo' => "https://arkeo-testnet.api.stakevillage.net/cosmos/staking/v1beta1/validators/tarkeovaloper18ad2hjs4792pkqyz2y8ef99cptweyj6r22n0p0",
    'Cardchain' => "https://cardchain-testnet-api.itrocket.net/cosmos/staking/v1beta1/validators/ccvaloper1k2h63lt9upzr40rsa74w6gz57v52qpew2zfnu8",
    'Nibiru' => "https://nibiru.api.kjnodes.com/cosmos/staking/v1beta1/validators/nibivaloper1sgjxwpjjktqfz5dec8h9swvud00afdtvjdd2ha"
];

// Проверка статусов валидаторов
$jailed_validators = [];
foreach ($validators as $name => $url) {
    $jailed_validator = check_validator_status($url, $name);
    if ($jailed_validator) {
        $jailed_validators[] = $jailed_validator;
    }
}

// Формирование и отправка сообщения в Telegram, если есть валидаторы в тюрьме
if (!empty($jailed_validators)) {
    $message = "Следующие валидаторы находятся в тюрьме: " . implode(", ", $jailed_validators);
    send_telegram_message($message, $telegram_bot_token, $chat_id);
    echo "Message sent to Telegram: $message";
} else {
    echo "Ни один из валидаторов, не находиться в тюрьме";
}

?>
