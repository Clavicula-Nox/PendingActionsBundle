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
