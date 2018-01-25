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

The actions status will change depending on the result.

  * `STATE_WAITING` (0) : The action is waiting to be processed.
  * `STATE_PROCESSING` (1) : The action is being processed.
  * `STATE_PROCESSED` (2) : The action is processed.
  * `STATE_ERROR` (3) : An error occured during the process or during the check of the action.
  * `STATE_UNKNOWN_HANDLER` (4) : The handler is not registered or not found.
  * `STATE_HANDLER_ERROR` (5) : The handler does not implements the `HandlerInterface`.
