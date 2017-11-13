Command Handler
---------------

This handler can launch a Symfony command.

Example in a controller : 

```php
<?php
    $params = ["command" => "my:command",
                "arguments" => [
                    "firstArg" => $firstArgValue,
                    "secondArg" => $secondArgValue,
                    // ...
                ], //can be empty
                "options" => [
                    "firstOption" => $firstOptionValue,
                    "secondOption" => $secondOptionValue,
                    "thirdOption" => $thirdOptionValue,
                    // ...
                ] //can be empty
            ];
            $this
                ->get("cn_pending_actions.pending_actions_service")
                ->register(
                    "CommandHandler",
                    $params,
                    "my_group_label"
                );
```
