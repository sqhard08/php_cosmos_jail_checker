# PHP_cosmos_jail_checker

PHP_cosmos_jail_checker is a script designed for monitoring validators on a node in the Cosmos network. It checks if any validators are jailed and sends relevant information to a specified Telegram chat. This tool is essential for tracking the status of Cosmos validators and ensuring their proper operation.

## Features

- **Jail Status Check**: Automatically checks if any validators are currently jailed.
- **Telegram Notifications**: Sends validator status updates to a specified Telegram chat.
- **Pinned Message Update**: If the status of validators changes, the pinned message in the chat is updated.
- **Logging**: Maintains a log of all actions for easy monitoring.

## Installation

1. **Clone the Repository**:

    ```bash
    git clone https://github.com/sqhard08/php_cosmos_jail_checker.git
    cd php_cosmos_jail_checker
    ```

2. **Create a Configuration File**:

    Create a file named `config.php` and add your Telegram bot token and chat ID:

    ```php
    <?php
    $telegram_bot_token = "your-telegram-bot-token";
    $chat_id = "your-telegram-chat-id";
    ?>
    ```

## Usage

Run the script to check the status of validators:

```bash
php your_script.php
