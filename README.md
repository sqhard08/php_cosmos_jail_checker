# PHP_cosmos_jail_checker

PHP_cosmos_jail_checker is a script designed to monitor validators for a node on the Cosmos network. It checks if any validators are jailed and sends the relevant information to a specified Telegram chat. This tool is essential for keeping track of your Cosmos validators' status and ensuring they are operating smoothly.

## Features

- **Jail Status Check**: Automatically checks if any validators are currently jailed.
- **Telegram Notifications**: Sends validator status updates to a specified Telegram chat.
- **Logging**: Logs all activities and actions taken by the script for easy monitoring.

## Installation

1. **Clone the Repository**:

    ```sh
    git clone https://github.com/your-username/php_cosmos_jail_checker.git
    cd php_cosmos_jail_checker
    ```

2. **Create Configuration File**:

    Create a file named `config.php` and add your Telegram bot token and chat ID:

    ```php
    <?php
    $telegram_bot_token = "your-telegram-bot-token";
    $chat_id = "your-telegram-chat-id";
    ?>
    ```
