<?php
/**
 * DirectoryTest.phpt
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 7.5.13
 *
 * @package Kappa
 * @testCase Kappa\Tests\FileSystem\Directory
 */

namespace Kappa\Tests\FileSystem\Directory;

use Kappa\FileSystem\Directory;
use Kappa\FileSystem\File;
use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;
use Tester\Helpers;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class DirectoryTest
 * @package Kappa\Tests\FileSystem\Directory
 * @testCase Kappa\Tests\FileSystem\Directory
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
		$directory = new Directory($this->generateDirName());
		Assert::false($directory->isCreated());
		Assert::true($directory->create());
		Assert::true($directory->isCreated());
		Assert::true($directory->remove());

		$directory = new Directory($this->generateDirName(), Directory::INTUITIVE);
		Assert::true($directory->isCreated());
		Assert::true($directory->remove());
	}

	public function testisCreated()
	{
		$directory = new Directory($this->generateDirName());
		Assert::same(file_exists($directory->getInfo()->getPathname()), $directory->isCreated());
		Assert::true($directory->create());
		Assert::same(file_exists($directory->getInfo()->getPathname()), $directory->isCreated());
		Assert::true($directory->remove());
	}

	public function testGetInfo()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$spl = new SplFileInfo($path);
		Assert::same(realpath($spl->getPathname()), $directory->getInfo()->getPathname());
		Assert::true($directory->remove());
	}

	public function testRename()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		Assert::true(file_exists($path));
		Assert::true($directory->rename('renamed'));
		Assert::false(file_exists($path));
		Assert::true(file_exists($this->dataPath . '/renamed'));
		Assert::true($directory->isCreated());
		Assert::true($directory->remove());
	}

	public function testGetContent()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$_file = new File($path . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999) . '.txt');
		Assert::true($_file->create());
		Assert::equal(array($_file->getInfo()->getPathname() => $_file), $directory->getContent());
		Assert::true($directory->remove());
	}

	public function testGetFiles()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$_file = new File($path . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999) . '.txt');
		Assert::true($_file->create());
		Assert::equal(array($_file->getInfo()->getPathname() => $_file), $directory->getFiles());
		Assert::true($directory->remove());
	}

	public function testGetDirectory()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$_file = new Directory($path . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999));
		Assert::true($_file->create());
		Assert::equal(array($_file->getInfo()->getPathname() => $_file), $directory->getDirectories());
		Assert::true($directory->remove());
	}

	public function testRemove()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$_file = new File($path . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999) . '.txt');
		Assert::true($_file->create());
		Assert::true($directory->isCreated());
		Assert::true($_file->isCreated());
		Assert::true($directory->remove());
		Assert::false($directory->isCreated());
		Assert::false($_file->isCreated());
	}

	public function testCopy()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$filePath = $path . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999) . '.txt';
		$_file = new File($filePath, File::INTUITIVE);
		Assert::true(file_exists($path));
		Assert::true(file_exists($filePath));
		$copyPath = $this->generateDirName();
		$_dir = $directory->copy($copyPath);
		Assert::type('\Kappa\FileSystem\Directory', $_dir);
		Assert::same(realpath($copyPath), $_dir->getInfo()->getPathname());
		Assert::true(file_exists($copyPath));
		Assert::true(file_exists($copyPath . DIRECTORY_SEPARATOR . $_file->getInfo()->getBasename()));
		Assert::true($directory->remove());
		Assert::true($_dir->remove());
	}

	public function testMove()
	{
		$path = $this->generateDirName();
		$directory = new Directory($path);
		Assert::true($directory->create());
		$filePath = $path . DIRECTORY_SEPARATOR;
		$fileName = time() . rand(1000000, 999999999) . '.txt';
		$_file = new File($filePath . $fileName);
		Assert::true($_file->create());
		Assert::true(file_exists($path));
		Assert::true(file_exists($filePath));
		$copyPath = $this->generateDirName();
		Assert::true($directory->move($copyPath));
		Assert::false(file_exists($path));
		Assert::false(file_exists($filePath));
		Assert::same(realpath($copyPath), $directory->getInfo()->getPathname());
		Assert::true(file_exists($copyPath));
		Assert::true(file_exists($copyPath . DIRECTORY_SEPARATOR . $fileName));
		@unlink($copyPath . DIRECTORY_SEPARATOR . $fileName);
		Assert::true($directory->remove());
	}

	/**
	 * @return string
	 */
	private function generateDirName()
	{
		return $this->dataPath . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999);
	}
}

\run(new DirectoryTest());