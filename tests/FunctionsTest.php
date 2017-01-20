<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Tests\Toggler;

use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Storage\ArrayStorage;
use SolidWorx\Toggler\Storage\YamlFileStorage;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testToggleConfig()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertEquals(new ArrayStorage($features), $this->readAttribute(new Config($features), 'config'));
    }

    public function testToggleConfigWithFile()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $this->assertEquals(new ArrayStorage($features), $this->readAttribute(new Config(__DIR__.'/stubs/config.php'), 'config'));
    }

    public function testToggleConfigWithYamlFile()
    {
        $features = new YamlFileStorage(__DIR__.'/stubs/config.yml');

        $this->assertEquals($features, $this->readAttribute(new Config(__DIR__.'/stubs/config.yml'), 'config'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testToggle()
    {
        $callback = function (): string {
            return 'abcdef';
        };

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertSame('abcdef', toggle('foo', $callback));
    }

    /**
     * @runInSeparateProcess
     */
    public function testToggleReturn()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertTrue(toggle('foo'));
        $this->assertTrue(toggle('bar'));
        $this->assertFalse(toggle('baz'));
        $this->assertFalse(toggle('foobar'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testToggleFail()
    {
        $callback = function (): string {
            return 'abcdef';
        };

        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        toggleConfig($features);

        $this->assertEquals('abcdef', toggle('baz', function (): void { }, $callback));
        $this->assertNull(toggle('baz', function (): void { }));
    }
}