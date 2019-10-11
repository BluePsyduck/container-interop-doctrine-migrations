<?php

declare(strict_types=1);

namespace BluePsyduck\ContainerInteropDoctrineMigrations;

use ContainerInteropDoctrine\AbstractFactory;
use ContainerInteropDoctrine\ConnectionFactory;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Psr\Container\ContainerInterface;

/**
 * The factory for the doctrine migrations configuration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MigrationsConfigurationFactory extends AbstractFactory
{
    /**
     * Creates a new instance from a specified config.
     *
     * @param ContainerInterface $container
     * @param string             $configKey
     *
     * @return mixed
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'migrations_configuration');

        $result = new Configuration(
            $this->retrieveDependency(
                $container,
                $configKey,
                'connection',
                ConnectionFactory::class
            )
        );
        $this->applyConfigValues($result, $config);

        return $result;
    }

    /**
     * Applies the config values to the Configuration instance.
     *
     * @param Configuration $configuration
     * @param array         $config
     */
    protected function applyConfigValues(Configuration $configuration, array $config): void
    {
        $configuration->setName($config['name']);
        $configuration->setMigrationsNamespace($config['namespace']);
        $configuration->setMigrationsDirectory($config['directory']);
        $configuration->setMigrationsTableName($config['table']);
        $configuration->setMigrationsColumnName($config['column']);
    }

    /**
     * Returns the default config.
     *
     * @param string $configKey
     *
     * @return array
     */
    protected function getDefaultConfig($configKey)
    {
        return [
            'directory' => 'data/migrations',
            'name'      => 'Doctrine database migrations',
            'namespace' => 'Migrations',
            'table'     => 'migrations',
            'column'    => 'version',
        ];
    }
}
