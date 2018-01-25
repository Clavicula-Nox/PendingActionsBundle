Service Handler
---------------

This handler can trigger a call to a method of any Symfony service.

Example in a controller : 

```php
<?php
    $params = ["serviceId" => "my_service.id",
                "method" => "my_serviceMethod",
                "args" => [
                    "myMethodArg" => $arg,
                    "myMethodArgB" => $argB,
                    "myMethodArgC" => $argC,
                    // ...
                ] //can be empty
            ];
    $this
        ->get("cn_pending_actions.pending_actions_service")
        ->register(
            "ServiceHandler",
            $params,
            "my_group_label"
        );
```

**Please note that you cannot use objects as parameters.**
