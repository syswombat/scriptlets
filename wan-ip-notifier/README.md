# WAN IP Notifier

Notify when the WAN IP has changed and send the new IP via a Telegram Bot.

## Usage

Via PHP CLI:
```bash
$ php wan-ip-notifier.php <bot_token> <chat_id> [<wan-ip-history.csv>]
```

Or, when calling via HTTP, set the appropriate headers:
```bash
$ curl --request GET \
  --url http://localhost/wan-ip-notifier.php \
  --header 'x-bot-token: <bot_token>' \
  --header 'x-chat-id: <chat_id>'
``` 
