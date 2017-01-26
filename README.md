PendingActionsBundle
===================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c607d9d8-329b-461a-82f8-8ad30be60be8/mini.png)](https://insight.sensiolabs.com/projects/c607d9d8-329b-461a-82f8-8ad30be60be8)
[![Latest Stable Version](https://poser.pugx.org/clavicula-nox/pendingactions-bundle/v/stable)](https://packagist.org/packages/clavicula-nox/pendingactions-bundle)
[![License](https://poser.pugx.org/clavicula-nox/pendingactions-bundle/license)](https://packagist.org/packages/clavicula-nox/pendingactions-bundle)
[![Total Downloads](https://poser.pugx.org/clavicula-nox/pendingactions-bundle/downloads)](https://packagist.org/packages/clavicula-nox/pendingactions-bundle)
[![Symfony](https://img.shields.io/badge/Symfony-%202.7%20and%203.x-green.svg "Supports Symfony 2.7 and 3.x")](https://symfony.com/)

**Requirements**

  * Symfony 2.7+ or 3.x applications
  * Doctrine ORM entities

**Reporting an issue or a feature request**

Issues and feature requests are tracked in the Github issue tracker.

Installation
------------

### Step 1: Download the Bundle

```bash
$ composer require clavicula-nox/pendingactions-bundle
```

This command requires you to have Composer installed globally, as explained
in the [Composer documentation](https://getcomposer.org/doc/00-intro.md).

### Step 2: Enable the Bundle

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new ClaviculaNox\PendingActionsBundle\PendingActionsBundle(),
        );
    }

    // ...
}
```

### Step 3: Initiate the table

You have to update your database schema to add the table **pending_actions**.

Documentation
-------------

### Pending Action Group
A Pending Action Group can be used to trigger a group of actions with the command filtered by the actionGroup parameter.

### Validating a Pending Action
You can check if a Pending Action is valid and will be triggered with the following code : 

### Register a pending action

#### Service Trigger

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
                    PendingAction::TYPE_SERVICE,
                    $params,
                    "actionGroupLabel"
                );
```

#### Event Trigger

Example in a controller : 

```php
<?php
    $params = ["eventId" => "my_event.id", //Works only for Generic Event
                "subject" => $mySubject, //value can be null but key have to be defined
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
            PendingAction::TYPE_EVENT,
            $params,
            "actionGroupLabel"
        );
```

#### Command Trigger

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
                    PendingAction::TYPE_COMMAND,
                    $params,
                    "actionGroupLabel"
                );
```

### Process the Pending Actions

```cli
# Symfony 2
php app/console cn:pending-actions:process --env=your_env

# Symfony 3
php bin/console cn:pending-actions:process --env=your_env
```

The actions status will change depending on the result.

  * `STATE_WAITING` (0) : The action is waiting to be processed.
  * `STATE_PROCESSING` (1) : The action is being processed.
  * `STATE_PROCESSED` (2) : The action is processed.
  * `STATE_ERROR` (3) : An error occured during the process or during the check of the action.
