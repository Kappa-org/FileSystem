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
use Kappa\FileSystem\File;
use Kappa\FileSystem\FileSystem;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class FileSystemTest
 * @package Kappa\FileSystem\Tests
 */
class FileSystemTest extends TestCase
{
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data';
	}

	public function testRemove()
	{
		$filePath = $this->dataPath . '/fileForDelete';
		$directoryPath = $this->dataPath . '/directoryForDelete';
		$file = File::create($filePath);
		$directory = Directory::create($directoryPath);
		Assert::true(is_file($filePath));
		Assert::true(is_dir($directoryPath));
		FileSystem::remove($file);
		FileSystem::remove($directory);
		Assert::false(is_file($filePath));
		Assert::false(is_dir($directoryPath));

		Assert::throws(function() use($directory) {
			FileSystem::remove(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
	}

	public function testRename()
	{
		$filePath = $this->dataPath . '/fileRename';
		$directoryPath = $this->dataPath . '/directoryRename';
		$newFile = 'fileNewName';
		$newDirectory = 'directoryNewName';
		$file = File::create($filePath);
		$directory = Directory::create($directoryPath);
		Assert::true(is_file($filePath));
		Assert::true(is_dir($directoryPath));
		Assert::false(is_file($this->dataPath . DIRECTORY_SEPARATOR . $newFile));
		Assert::false(is_dir($this->dataPath . DIRECTORY_SEPARATOR . $newDirectory));
		Assert::type('Kappa\FileSystem\File', FileSystem::rename($file, $newFile));
		Assert::type('Kappa\FileSystem\Directory', FileSystem::rename($directory, $newDirectory));
		Assert::true(is_file($this->dataPath . DIRECTORY_SEPARATOR . $newFile));
		Assert::true(is_dir($this->dataPath . DIRECTORY_SEPARATOR . $newDirectory));
		Assert::false(is_file($filePath));
		Assert::false(is_dir($directoryPath));

		unlink($this->dataPath . DIRECTORY_SEPARATOR . $newFile);
		rmdir($this->dataPath . DIRECTORY_SEPARATOR . $newDirectory);

		Assert::throws(function() use($directory) {
			FileSystem::rename(array(), array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
	}

	public function testCopy()
	{
		$filePath = $this->dataPath . '/fileCopy';
		$directoryPath = $this->dataPath . '/directoryCopy';
		$newFile = $this->dataPath . '/fileAfterCopy';
		$newDirectory = $this->dataPath . '/directoryAfterCopy';
		$file = File::create($filePath);
		$directory = Directory::create($directoryPath);
		Assert::true(is_file($filePath));
		Assert::true(is_dir($directoryPath));
		Assert::false(is_file($newFile));
		Assert::false(is_dir($newDirectory));
		$file2 = FileSystem::copy($file, $newFile);
		$directory2 = FileSystem::copy($file, $directory);
		$directory3 = FileSystem::copy($directory, $newDirectory);
		Assert::type('Kappa\FileSystem\File', $file2);
		Assert::type('Kappa\FileSystem\File', $directory2);
		Assert::type('Kappa\FileSystem\Directory', $directory3);
		Assert::true(is_file($newFile));
		Assert::true(is_dir($newDirectory));
		Assert::true(is_file($directoryPath . '/fileCopy'));

		Assert::throws(function() use($directory) {
			FileSystem::copy($directory, $directory);
		}, 'Kappa\FileSystem\InvalidArgumentException');
		Assert::throws(function() use($directory) {
			FileSystem::copy(array(), array());
		}, 'Kappa\FileSystem\InvalidArgumentException');

		FileSystem::remove($file);
		FileSystem::remove($file2);
		FileSystem::remove($directory);
		FileSystem::remove($directory2);
		FileSystem::remove($directory3);
	}
}

\run(new FileSystemTest());