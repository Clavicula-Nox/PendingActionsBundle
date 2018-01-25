Custom Handler
---------------

You can add your own custom handlers using the following configuration : 

```yaml
    pending_actions:
        handlers:
            MyHandlerLabel: my_handler_service_id
            MySecondHandlerLabel: my_second_handler_service_id
```

You can then register a PendingAction with your custom handler using the following code : 

```php
<?php
    $params = [];
    $this
        ->get("cn_pending_actions.pending_actions_service")
        ->register(
            "MyHandlerLabel",
            $params,
            "my_group_label"
        );
```

**Requirements**

- The handler has to be registered as a service
- The service class has to implement the interface `\ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerInterface`

**Interface Documentation**

The Interface has 2 methods : 
- checkPendingAction : Used to check if the PendingAction can be processed. It is suggested to call this method in the process method of your handler.
- process : Used to process the PendingAction.
