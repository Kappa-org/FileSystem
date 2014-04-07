<?php
/**
 * This file is part of the Kappa\FileSystem package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 * 
 * @testCase
 */

namespace Kappa\FileSystem\Tests;

use Kappa\FileSystem\Directory;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class DirectoryTest
 * @package Kappa\FileSystem\Tests
 */
class DirectoryTest extends TestCase
{
	public function testOpen()
	{
		$directory = Directory::open(__DIR__);
		Assert::type('Kappa\FileSystem\Directory', $directory);
	}

	public function testGetInfo()
	{
		$directory = Directory::open(__DIR__);
		Assert::type('Kappa\FileSystem\SplFileInfo', $directory->getInfo());
		Assert::same(__DIR__, $directory->getInfo()->getPathname());
	}
}

\run(new DirectoryTest());