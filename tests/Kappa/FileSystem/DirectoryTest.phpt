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
use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class DirectoryTest
 * @package Kappa\FileSystem\Tests
 */
class DirectoryTest extends TestCase
{
	/** @var string */
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data';
	}

	public function testCreate()
	{
		$path = $this->dataPath . '/directory';
		Assert::false(is_dir($path));
		Directory::create($path);
		Assert::true(is_dir($path));

		rmdir($path);

		Assert::throws(function () {
			Directory::create(__DIR__);
		}, 'Kappa\FileSystem\DirectoryAlreadyExistException');
	}

	public function testOpen()
	{
		$directory = Directory::open(__DIR__);
		Assert::type('Kappa\FileSystem\Directory', $directory);

		Assert::throws(function () {
			Directory::open('noDirectory');
		}, 'Kappa\FileSystem\DirectoryNotFoundException');
	}

	public function testGetInfo()
	{
		$directory = Directory::open(__DIR__);
		Assert::type('Kappa\FileSystem\SplFileInfo', $directory->getInfo());
		Assert::same(__DIR__, $directory->getInfo()->getPathname());
	}

	public function testGetDirectory()
	{
		$directory = Directory::open($this->dataPath);
		Assert::equal(array(realpath($this->dataPath . '/files') => new SplFileInfo($this->dataPath . '/files')), $directory->getDirectories());
	}
}

\run(new DirectoryTest());