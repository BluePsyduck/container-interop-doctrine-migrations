# bluepsyduck/container-interop-doctrine-migrations

[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/BluePsyduck/container-interop-doctrine-migrations)](https://github.com/BluePsyduck/container-interop-doctrine-migrations/releases)
[![GitHub](https://img.shields.io/github/license/BluePsyduck/container-interop-doctrine-migrations)](LICENSE.md)
[![Codecov](https://img.shields.io/codecov/c/gh/BluePsyduck/container-interop-doctrine-migrations?logo=codecov)](https://codecov.io/gh/BluePsyduck/container-interop-doctrine-migrations)

### DEPRECATED: Use package [roave/psr-container-doctrine](https://github.com/Roave/psr-container-doctrine) instead.

[roave/psr-container-doctrine](https://github.com/Roave/psr-container-doctrine) replaces 
[dasprid/container-interop-doctrine](https://github.com/DASPRiD/container-interop-doctrine) with the addition of 
Doctrine Migrations, which makes this package obsolete.

---

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
