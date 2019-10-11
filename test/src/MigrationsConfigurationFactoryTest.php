<?php

declare(strict_types=1);

namespace BluePsyduckTest\ContainerInteropDoctrineMigrations;

use BluePsyduck\ContainerInteropDoctrineMigrations\MigrationsConfigurationFactory;
use BluePsyduck\TestHelper\ReflectionTrait;
use ContainerInteropDoctrine\ConnectionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionException;

/**
 * The PHPUnit test of the MigrationsConfigurationFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\ContainerInteropDoctrineMigrations\MigrationsConfigurationFactory
 */
class MigrationsConfigurationFactoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the createWithConfig method.
     * @throws ReflectionException
     * @covers ::createWithConfig
     */
    public function testCreateWithConfig(): void
    {
        $configKey = 'orm_Default';
        $config = ['abc' => 'def'];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        /* @var Connection&MockObject $connection */
        $connection = $this->createMock(Connection::class);

        /* @var MigrationsConfigurationFactory&MockObject $factory */
        $factory = $this->getMockBuilder(MigrationsConfigurationFactory::class)
                        ->onlyMethods(['retrieveConfig', 'retrieveDependency', 'applyConfigValues'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $factory->expects($this->once())
                ->method('retrieveConfig')
                ->with(
                    $this->identicalTo($container),
                    $this->identicalTo($configKey),
                    $this->identicalTo('migrations_configuration')
                )
                ->willReturn($config);
        $factory->expects($this->once())
                ->method('retrieveDependency')
                ->with(
                    $this->identicalTo($container),
                    $this->identicalTo($configKey),
                    $this->identicalTo('connection'),
                    $this->identicalTo(ConnectionFactory::class)
                )
                ->willReturn($connection);
        $factory->expects($this->once())
                ->method('applyConfigValues')
                ->with($this->isInstanceOf(Configuration::class), $config);

        $result = $this->invokeMethod($factory, 'createWithConfig', $container, $configKey);
        $this->assertInstanceOf(Configuration::class, $result);
    }

    /**
     * Tests the applyConfigValues method.
     * @throws ReflectionException
     * @covers ::applyConfigValues
     */
    public function testApplyConfigValues(): void
    {
        $config = [
            'name' => 'abc',
            'namespace' => 'def',
            'directory' => 'ghi',
            'table' => 'jkl',
            'column' => 'mno',
        ];

        /* @var Configuration&MockObject $configuration */
        $configuration = $this->getMockBuilder(Configuration::class)
                              ->onlyMethods([
                                  'setName',
                                  'setMigrationsNamespace',
                                  'setMigrationsDirectory',
                                  'setMigrationsTableName',
                                  'setMigrationsColumnName',
                              ])
                              ->disableOriginalConstructor()
                              ->getMock();
        $configuration->expects($this->once())
                      ->method('setName')
                      ->with($this->identicalTo('abc'));
        $configuration->expects($this->once())
                      ->method('setMigrationsNamespace')
                      ->with($this->identicalTo('def'));
        $configuration->expects($this->once())
                      ->method('setMigrationsDirectory')
                      ->with($this->identicalTo('ghi'));
        $configuration->expects($this->once())
                      ->method('setMigrationsTableName')
                      ->with($this->identicalTo('jkl'));
        $configuration->expects($this->once())
                      ->method('setMigrationsColumnName')
                      ->with($this->identicalTo('mno'));

        $factory = new MigrationsConfigurationFactory('orm_default');
        $this->invokeMethod($factory, 'applyConfigValues', $configuration, $config);
    }

    /**
     * Tests the getDefaultConfig method.
     * @throws ReflectionException
     * @covers ::getDefaultConfig
     */
    public function testGetDefaultConfig(): void
    {
        $configKey = 'orm_default';
        $expectedResult = [
            'directory' => 'data/migrations',
            'name'      => 'Doctrine database migrations',
            'namespace' => 'Migrations',
            'table'     => 'migrations',
            'column'    => 'version',
        ];

        $factory = new MigrationsConfigurationFactory($configKey);
        $result = $this->invokeMethod($factory, 'getDefaultConfig', $configKey);

        $this->assertEquals($expectedResult, $result);
    }
}
