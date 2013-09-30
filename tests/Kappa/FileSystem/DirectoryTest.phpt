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
use Kappa\FileSystem\File;
use Kappa\FileSystem\SplFileInfo;
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

	public function testRemove()
	{
		$path = $this->randomDirectory();
		$dir = new Directory($path);
		Assert::true(is_dir($path));
		Assert::true($dir->remove());
		Assert::false(is_dir($path));
	}

	public function testRename()
	{
		$path = $this->randomDirectory();
		$dir = new Directory($path);
		$renamed = $this->dataPath . DIRECTORY_SEPARATOR . 'renamed';
		Assert::false(is_dir($renamed));
		Assert::true($dir->rename('renamed'));
		Assert::true(is_dir($renamed));
		Assert::false(is_dir($path));
		Assert::same(realpath($renamed), $dir->getPath());

		Assert::throws(function () use ($dir) {
			$dir->rename(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
		Assert::throws(function () use ($dir, $renamed) {
			new Directory($renamed);
			$dir->rename('renamed');
		}, 'Kappa\FileSystem\DirectoryAlreadyExistException');

		Assert::true(rmdir($renamed));
	}

	public function testGetContent()
	{
		$path = $this->randomDirectory();
		$_filePath = $path . DIRECTORY_SEPARATOR . 'test.txt';
		$_dirPath = $path . DIRECTORY_SEPARATOR . 'test';
		$dir = new Directory($path);
		$_file = new File($_filePath);
		$_dir = new Directory($_dirPath);
		Assert::equal(array($_file->getPath() => $_file, $_dir->getPath() => $_dir), $dir->getContent());
		Assert::true(unlink($_filePath));
		Assert::true(rmdir($_dirPath));
		Assert::true(rmdir($path));
	}

	public function testGetFiles()
	{
		$path = $this->randomDirectory();
		$_filePath = $path . DIRECTORY_SEPARATOR . 'test.txt';
		$_dirPath = $path . DIRECTORY_SEPARATOR . 'test';
		$dir = new Directory($path);
		$_file = new File($_filePath);
		new Directory($_dirPath);
		Assert::equal(array($_file->getPath() => $_file), $dir->getFiles());
		Assert::true(unlink($_filePath));
		Assert::true(rmdir($_dirPath));
		Assert::true(rmdir($path));
	}

	public function testGetDirectories()
	{
		$path = $this->randomDirectory();
		$_filePath = $path . DIRECTORY_SEPARATOR . 'test.txt';
		$_dirPath = $path . DIRECTORY_SEPARATOR . 'test';
		$dir = new Directory($path);
		new File($_filePath);
		$_dir = new Directory($_dirPath);
		Assert::equal(array($_dir->getPath() => $_dir), $dir->getDirectories());
		Assert::true(unlink($_filePath));
		Assert::true(rmdir($_dirPath));
		Assert::true(rmdir($path));
	}

	public function testGetBaseName()
	{
		$dir = new Directory(__DIR__, Directory::LOAD);
		Assert::same('FileSystem', $dir->getBaseName());
	}

	public function testGetPath()
	{
		$path = $this->randomDirectory();
		$dir = new Directory($path);
		Assert::same(realpath($path), $dir->getPath());
		Assert::true(rmdir($path));
	}

	public function testGetInfo()
	{
		$path = $this->randomDirectory();
		$dir = new Directory($path);
		Assert::equal(new SplFileInfo($path), $dir->getInfo());
		Assert::true(rmdir($path));
	}

	public function testCopy()
	{
		$path = $this->randomDirectory();
		$dir = new Directory($path);
		$_filePath = $path . DIRECTORY_SEPARATOR . 'in.txt';
		$_file = new File($_filePath);
		$copyPath = $this->dataPath . DIRECTORY_SEPARATOR . 'copy';
		Assert::false(is_dir($copyPath));
		Assert::true(is_dir($path));
		Assert::true($dir->copy($copyPath, false));
		Assert::true(is_dir($copyPath));
		Assert::true(is_file($_filePath));

		Assert::true(unlink($_filePath));
		Assert::true(unlink($copyPath . DIRECTORY_SEPARATOR . 'in.txt'));
		Assert::true(rmdir($copyPath));
		Assert::true(rmdir($path));

		$path = $this->randomDirectory();
		$dir = new Directory($path);
		$_filePath = $path . DIRECTORY_SEPARATOR . 'in.txt';
		$_file = new File($_filePath);
		$copyPath = $this->dataPath . DIRECTORY_SEPARATOR . 'copy';
		Assert::false(is_dir($copyPath));
		Assert::true(is_dir($path));
		$afterCopy = $dir->copy($copyPath);
		Assert::type('Kappa\FileSystem\Directory', $afterCopy);
		Assert::same(realpath($copyPath), $afterCopy->getPath());
		Assert::true(is_dir($copyPath));
		Assert::true(is_file($_filePath));

		Assert::true(unlink($_filePath));
		Assert::true(unlink($copyPath . DIRECTORY_SEPARATOR . 'in.txt'));
		Assert::true(rmdir($copyPath));
		Assert::true(rmdir($path));

		$path = $this->randomDirectory();
		$dir = new Directory($path);
		$_filePath = $path . DIRECTORY_SEPARATOR . 'in.txt';
		$_file = new File($_filePath);
		$copyPath = $this->dataPath . DIRECTORY_SEPARATOR . 'copy';
		Assert::false(is_dir($copyPath));
		Assert::true(is_dir($path));
		$afterCopy = $dir->copy($copyPath, true, false, array('in.txt'));
		Assert::type('Kappa\FileSystem\Directory', $afterCopy);
		Assert::same(realpath($copyPath), $afterCopy->getPath());
		Assert::true(is_dir($copyPath));
		Assert::false(is_file($copyPath . DIRECTORY_SEPARATOR . 'in.txt'));

		Assert::true(unlink($_filePath));
		Assert::true(rmdir($copyPath));
		Assert::true(rmdir($path));
	}

	public function testMove()
	{
		$path = $this->randomDirectory();
		$dir = new Directory($path);
		$movePath = $this->dataPath . DIRECTORY_SEPARATOR . 'move';
		Assert::true($dir->move($movePath));
		Assert::false(is_dir($path));
		Assert::true(is_dir($movePath));
		Assert::same(realpath($movePath), $dir->getPath());
		Assert::true(rmdir($movePath));

		Assert::throws(function () use ($dir) {
			$dir->move(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
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