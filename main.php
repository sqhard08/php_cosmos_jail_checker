2<?php 
// Подключение файла конфигурации
require_once 'config.php';

// Функция для отправки сообщения в Telegram с обязательным звуковым сигналом
function send_telegram_message($message, $telegram_bot_token, $chat_id) {
    $url = "https://api.telegram.org/bot$telegram_bot_token/sendMessage";
    $data = array(
        'chat_id' => $chat_id,
        'text' => $message,
        'disable_notification' => false // Обязательный звуковой сигнал
    );
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

// Функция для редактирования сообщения в Telegram
function edit_telegram_message($message, $telegram_bot_token, $chat_id, $message_id) {
    $url = "https://api.telegram.org/bot$telegram_bot_token/editMessageText";
    $data = array(
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $message,
        'disable_notification' => false // Обязательный звуковой сигнал
    );
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Функция для закрепления сообщения в Telegram
function pin_telegram_message($telegram_bot_token, $chat_id, $message_id) {
    $url = "https://api.telegram.org/bot$telegram_bot_token/pinChatMessage";
    $data = array('chat_id' => $chat_id, 'message_id' => $message_id);
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Функция для открепления всех сообщений в Telegram
function unpin_all_telegram_messages($telegram_bot_token, $chat_id) {
    $url = "https://api.telegram.org/bot$telegram_bot_token/unpinAllChatMessages";
    $data = array('chat_id' => $chat_id);
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context = stream_context_create($options);
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
    'Terp' => "https://terp-mainnet-api.itrocket.net/cosmos/staking/v1beta1/validators/terpvaloper1un9ar4a0sqkcxwnxc43x3xq6yppc9d6n7gfyx9",
    'EntryPoint' => "https://testnet-rest.entrypoint.zone/cosmos/staking/v1beta1/validators/entrypointvaloper14fu3zvcfnhgdwgpxnaj9qxxk0p5uynn4xxruc4",
    'Empower_mainnet' => "https://empower-mainnet-api.itrocket.net/cosmos/staking/v1beta1/validators/empowervaloper1zayu3xf087rk5cca952jkenz8rz4rr9ea030fw",
    'Nibiru' => "https://nibiru.api.kjnodes.com/cosmos/staking/v1beta1/validators/nibivaloper1sgjxwpjjktqfz5dec8h9swvud00afdtvjdd2ha",
    'Nois' => "https://nois-testnet-api.itrocket.net/cosmos/staking/v1beta1/validators/noisvaloper1lsawseza2ctwv4scvf4kfarflcl9gq52cj63ft"
];

// Проверка статусов валидаторов
$jailed_validators = [];
foreach ($validators as $name => $url) {
    $jailed_validator = check_validator_status($url, $name);
    if ($jailed_validator) {
        $jailed_validators[] = $jailed_validator;
    }
}

// Имя файла для хранения ID закрепленного сообщения
$pinned_message_file = 'pinned_message_id.txt';
// Имя файла для хранения предыдущего статуса валидаторов
$previous_status_file = 'previous_status.txt';

// Получаем предыдущий статус валидаторов
$previous_status = file_exists($previous_status_file) ? file_get_contents($previous_status_file) : null;
// Текущий статус валидаторов
$current_status = implode(", ", $jailed_validators);

// Если статус изменился, выполняем действия
if ($current_status !== $previous_status) {
    if (!empty($jailed_validators)) {
        $message = "Следующие валидаторы находятся в тюрьме: " . implode(", ", $jailed_validators);

        // Отправляем новое сообщение с оповещением в чат
        $result = send_telegram_message($message, $telegram_bot_token, $chat_id);
        if (isset($result['result']['message_id'])) {
            $new_message_id = $result['result']['message_id'];
            // Если есть закрепленное сообщение, обновляем его, иначе закрепляем новое
            if (file_exists($pinned_message_file)) {
                $pinned_message_id = file_get_contents($pinned_message_file);
                // Редактируем существующее сообщение
                edit_telegram_message($message, $telegram_bot_token, $chat_id, $pinned_message_id);
            } else {
                // Закрепляем новое сообщение
                pin_telegram_message($telegram_bot_token, $chat_id, $new_message_id);
                file_put_contents($pinned_message_file, $new_message_id);
            }
        }
        echo "Sent new message and updated pin: $message";
    } else {
        if (file_exists($pinned_message_file)) {
            // Если нет валидаторов в тюрьме, но есть закрепленное сообщение, открепляем его
            unpin_all_telegram_messages($telegram_bot_token, $chat_id);
            unlink($pinned_message_file);
            echo "Все валидаторы вышли из тюрьмы. Сообщение откреплено.";
        } else {
            echo "Ни один из валидаторов не находится в тюрьме.";
        }
    }

    // Сохраняем текущий статус валидаторов
    file_put_contents($previous_status_file, $current_status);
} else {
    echo "Статус валидаторов не изменился. Сообщение не отправлено.";
}
?>
