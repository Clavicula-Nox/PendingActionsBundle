Custom Handler Overrides
------------------------

A [custom handler](Docs/custom-handler.md) has to implement the interface `\ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerInterface`

You can also implement the `\ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerRegisterInterface` to allow you to override the Register() method of your handler.
This can allow you for custom registration of your handler.

It is recommended to set the method as private so it can't be used outside of the PendingActionsService service.

Basic registration looks like : 

```php
<?php
    $PendingAction = new PendingAction();
    $PendingAction->setHandler($handler);
    $PendingAction->setActionParams(json_encode($params));
    $PendingAction->setActionGroup($group);
    $PendingAction->setCreated(new \DateTime());
    $PendingAction->setUpdated(new \DateTime());
    $PendingAction->setState(PendingAction::STATE_WAITING);
```

**Interface Documentation**

The Interface has 1 method : 
- register : Used to register a pending action. Returns the pending action.
