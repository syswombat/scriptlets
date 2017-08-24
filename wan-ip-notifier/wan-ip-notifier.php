<?php

/**
 * (c) Armando 'noplanman' Lüscher <armando@noplanman.ch>
 *
 * scriptlet: wan-ip-notifier
 * version:   1.0.0
 *
 * This scriptlet notifies a change in your WAN IP via a Telegram Bot message.
 * Requires PHP 7+
 *
 * Usage: php wan-ip-notifier.php <bot_token> <chat_id> [<wan-ip-history.csv>]
 * cron job all 15 minutes
 * /15 * * * * /wan-ip-notifier.php 
 *
 *   bot_token:          Your bot's token as provided by @BotFather
 *   chat_id:            Your Telegram user id. (Send `/whoami` to @PHP_Telegram_Bot)
 *   wan-ip-history.csv: File to log all IP changes to.
 */
$telegram_bot_token = 
$telegram_chat_id   = 
$bot_token = $argv[1] ?? $_SERVER['HTTP_X_BOT_TOKEN'] ?? null;
$chat_id   = $argv[2] ?? $_SERVER['HTTP_X_CHAT_ID'] ?? null;
$csv_path  = ($argv[3] ?? null) ?: __DIR__ . '/wan-ip-history.csv';

if (empty($bot_token) || empty($chat_id)) {
    die('Bot Token and Chat ID required.' . PHP_EOL);
}

// Open CSV, create if it doesn't exist.
$csv_file = fopen($csv_path, 'ab+');

// Get all IPs from CSV.
$csv = array_map('str_getcsv', file($csv_path));

// Set CSV headers.
empty($csv) && fputcsv($csv_file, ['datetime', 'ip']);

// Get latest entry.
list($last_datetime, $last_ip) = end($csv);

// Get current WAN IP.
$ip = file_get_contents('http://ipecho.net/plain');

// Check if we have a new IP.
if ($ip !== $last_ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    // Add new IP to CSV.
    fputcsv($csv_file, [$datetime = date('d.m.Y H:i:s'), $ip]);

    // Send message to Telegram chat.
    $text = "New IP: {$ip} ({$datetime})";

    // Only add the old IP to the message if there is one.
    if (filter_var($last_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $text .= "\nOld IP: {$last_ip} ({$last_datetime})";
    }

    // Send message via Telegram Bot.
    file_get_contents("https://api.telegram.org/bot{$bot_token}/sendMessage?chat_id={$chat_id}&text=" . urlencode($text));
}

fclose($csv_file);
