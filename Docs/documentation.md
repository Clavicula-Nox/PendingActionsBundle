Documentation
-------------

### Pending Action Group
A Pending Action Group can be used to trigger a group of actions with the command when using the actionGroup argument.

### Register a pending action

To register a new PendingAction, you should use the following code : 

```php
<?php
    $params = []; // Parameters of the Handler
    $this
        ->get("cn_pending_actions.pending_actions_service")
        ->register(
            "my_handler_name",
            $params,
            "my_group_label" //optionnal
        );
```

### Process the Pending Actions

```cli
php bin/console cn:pending-actions:process --env=your_env 
```

See [States](states.md) for more details about the lifecycle of a PendingAction.
