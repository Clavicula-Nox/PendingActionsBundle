<?php

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$autoload = require $autoloadFile;

$fs = new Filesystem();

// Remove build dir files
if (is_dir(__DIR__.'/../build')) {
    echo "Removing files in the build directory.\n".__DIR__."\n";

    try {
        $fs->remove(__DIR__.'/../build');
    } catch (Exception $e) {
        fwrite(STDERR, $e->getMessage());
        system('rm -rf '.__DIR__.'/../build');
    }
}

$fs->mkdir(__DIR__.'/../build');

AnnotationRegistry::registerLoader(function ($class) use ($autoload) {
    $autoload->loadClass($class);

    return class_exists($class, false);
});

include __DIR__.'/App/AppKernel.php';
$kernel = new AppKernel('test', true);
$kernel->boot();

$databaseFile = $kernel->getContainer()->getParameter('database_path');
$application = new Application($kernel);

if ($fs->exists($databaseFile)) {
    $fs->remove($databaseFile);
}

// Create database
$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(['command' => 'doctrine:database:create']);
$command->run($input, new ConsoleOutput());

// Create database schema
$command = new CreateSchemaDoctrineCommand();
$application->add($command);
$input = new ArrayInput(['command' => 'doctrine:schema:create']);
$command->run($input, new ConsoleOutput());

$kernel->shutdown();
