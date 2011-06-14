<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\DoctrineBundle\Tests\Mapping\Driver;

use Symfony\Bundle\DoctrineBundle\Mapping\Driver\YamlDriver;

class YamlDriverTest extends AbstractDriverTest
{
    protected function getFileExtension()
    {
        return '.orm.yml';
    }

    protected function getDriver(array $paths = array())
    {
        return new YamlDriver($paths);
    }
}