# bluepsyduck/container-interop-doctrine-migrations

[![Latest Stable Version](https://poser.pugx.org/bluepsyduck/container-interop-doctrine-migrations/v/stable)](https://packagist.org/packages/bluepsyduck/container-interop-doctrine-migrations) 
[![License](https://poser.pugx.org/bluepsyduck/container-interop-doctrine-migrations/license)](https://packagist.org/packages/bluepsyduck/container-interop-doctrine-migrations) 
[![Build Status](https://travis-ci.com/BluePsyduck/container-interop-doctrine-migrations.svg?branch=master)](https://travis-ci.com/BluePsyduck/container-interop-doctrine-migrations) 
[![codecov](https://codecov.io/gh/BluePsyduck/container-interop-doctrine-migrations/branch/master/graph/badge.svg)](https://codecov.io/gh/BluePsyduck/container-interop-doctrine-migrations)

This library is an extension to 
[dasprid/container-interop-doctrine](https://github.com/DASPRiD/container-interop-doctrine) 
to support Doctrine Migrations.

## Configuration

Extend your Doctrine configuration with the following values:

```php
<?php

use BluePsyduck\ContainerInteropDoctrineMigrations\MigrationsConfigurationFactory;

return [
    'dependencies' => [
        'factories' => [
            'doctrine.migrations.orm_default' => MigrationsConfigurationFactory::class,
        ],
    ],
    
    'doctrine' => [
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => __DIR__ . '/../../data/database/migrations',
                'name'      => 'Fancy Service Database Migrations',
                'namespace' => 'FancyService\Migrations',
                'table'     => '_Migrations',
            ],
        ],
    ],
];
```

Place the following content into a file `config/cli-config.php` to use the Doctrine CLI tools:

```php
<?php

declare(strict_types=1);

namespace BluePsyduck\FancyService;

use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

/* @var ContainerInterface $container */
$container = require(__DIR__  . '/container.php');
/* @var EntityManager $entityManager */
$entityManager = $container->get(EntityManager::class);

return new HelperSet([
    'em' => new EntityManagerHelper($entityManager),
    'question' => new QuestionHelper(),
    'configuration' => new ConfigurationHelper(
        $entityManager->getConnection(),
        $container->get('doctrine.migrations.orm_default')
    ),
]);
```

From now on you can use the Doctrine CLI tools by calling their scripts, e.g.:
* `vendor/bin/doctrine-migrations migrations:status` 
