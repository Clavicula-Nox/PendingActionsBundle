Event Handler
---------------

This handler can simulate the trigger of an event.

Example in a controller : 

```php
<?php
    $params = ["eventClassName" => "\My\Event\Class",
                "eventId" => "my_event.id",
                "args" => [
                    "myEventArg" => $arg,
                    "myEventArgB" => $argB,
                    "myEventArgC" => $argC,
                    // ...
                ]
            ];
    $this
        ->get("cn_pending_actions.pending_actions_service")
        ->register(
            "EventHandler",
            $params,
            "my_group_label"
        );
```

**Please note that you cannot use objects as parameters.**
