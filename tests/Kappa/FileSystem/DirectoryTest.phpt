<?php
/**
 * This file is part of the Kappa/FileSystem package.
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

class DirectoryTest extends TestCase
{
	/** @var string */
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data/';
	}

	public function testCreate()
	{
		$path = $this->randomDirectory();
		Assert::false(is_dir($path));
		new Directory($path);
		Assert::true(is_dir($path));
		Assert::true(rmdir($path));

		Assert::throws(function () {
			new Directory(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
		Assert::throws(function () {
			new Directory(__DIR__, Directory::CREATE);
		}, 'Kappa\FileSystem\DirectoryAlreadyExistException');
	}

	public function testLoad()
	{
		new Directory(__DIR__, Directory::LOAD);
		Assert::throws(function () {
			new Directory('dir', Directory::LOAD);
		}, 'Kappa\FileSystem\DirectoryNotFoundException');
	}

	/**
	 * @return string
	 */
	private function randomDirectory()
	{
		return $path = $this->dataPath . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999);
	}
}

\run(new DirectoryTest());