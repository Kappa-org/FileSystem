<?php
/**
 * FileTest.phpt
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 4.5.13
 *
 * @package Kappa\FileSystem
 * @testCase Kappa\Tests\FileSystem\FileTest
 */

namespace Kappa\Tests\FileSystem\FileTest;

use Kappa\FileSystem\Directory;
use Kappa\FileSystem\File;
use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class FileTest
 * @package Kappa\Tests\FileSystem\File
 */
class FileTest extends TestCase
{
	/** @var string */
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data/';
	}

	public function testCreate()
	{
		$file = new File($this->generateFileName());
		Assert::false($file->isUsable());
		Assert::true($file->create());
		Assert::true($file->isUsable());
		Assert::true($file->remove());

		$file = new File($this->generateFileName(), File::INTUITIVE);
		Assert::true($file->isUsable());
		Assert::true($file->remove());
	}

	public function testisUsable()
	{
		$path = $this->generateFileName();
		$file = new File($path);

		Assert::same(file_exists($path), $file->isUsable());
		Assert::true($file->create());
		Assert::same(file_exists($path), $file->isUsable());
		Assert::true($file->remove());
	}

	public function testOverwrite()
	{
		$file = new File($this->generateFileName());
		Assert::true($file->create());
		Assert::same("", $file->read());
		Assert::true($file->overwrite("Hello world!"));
		Assert::same("Hello world!", $file->read());
		Assert::true($file->remove());
	}

	public function testClean()
	{
		$file = new File($this->generateFileName());
		Assert::true($file->create());
		Assert::same("", $file->read());
		Assert::true($file->overwrite("Hello world!"));
		Assert::same("Hello world!", $file->read());
		Assert::true($file->clean());
		Assert::same("", $file->read());
		Assert::true($file->remove());
	}

	public function testAppend()
	{
		$file = new File($this->generateFileName());
		Assert::true($file->create());
		Assert::same("", $file->read());
		Assert::true($file->append("Hello"));
		Assert::true($file->append("world!", false));
		Assert::true($file->append("I'm test"));
		Assert::same("Hello world!" . PHP_EOL . "I'm test", $file->read());
		Assert::true($file->remove());
	}

	public function testRead()
	{
		$file = new File($this->generateFileName());
		Assert::true($file->create());
		Assert::same(file_get_contents($file->getInfo()->getPathname()), $file->read());
		Assert::true($file->overwrite("Hello world! I'm test"));
		Assert::same(file_get_contents($file->getInfo()->getPathname()), $file->read());
		Assert::true($file->remove());
	}

	public function testGetHash()
	{
		$file = new File($this->generateFileName());
		Assert::true($file->create());
		Assert::same(md5_file($file->getInfo()->getPathname()), $file->getHash());
		Assert::true($file->overwrite("Hello world! I'm test"));
		Assert::same(md5_file($file->getInfo()->getPathname()), $file->getHash());
		Assert::true($file->remove());
	}

	public function testGetInfo()
	{
		$path = $this->generateFileName();
		$file = new File($path);
		Assert::true($file->create());
		$spl = new SplFileInfo($path);
		Assert::same($spl->getRealPath(), $file->getInfo()->getRealPath());
		Assert::true($file->remove());
	}

	public function testRemove()
	{
		$file= new File($this->generateFileName());
		Assert::true($file->create());
		Assert::true($file->isUsable());
		Assert::true($file->remove());
		Assert::false($file->isUsable());
	}

	public function testRename()
	{
		$path = $this->generateFileName();
		$file = new File($path);
		Assert::true($file->create());
		Assert::same(realpath($path), $file->getInfo()->getPathname());
		Assert::true($file->rename('renamed.txt'));
		Assert::same(realpath($this->dataPath . '/renamed.txt'), $file->getInfo()->getPathname());
		Assert::true($file->remove());
	}

	public function testCopy()
	{
		$file = new File($this->generateFileName());
		$copyPath = $this->generateFileName();
		Assert::true($file->create());
		Assert::false(file_exists($copyPath));
		Assert::true($file->copy($copyPath, false));
		Assert::true(file_exists($copyPath));
		Assert::true($file->remove());
		@unlink($copyPath);

		$path = $this->generateFileName();
		$file = new File($path);
		$copyPath = $this->generateFileName();
		Assert::true($file->create());
		Assert::false(file_exists($copyPath));
		$copyFile = $file->copy($copyPath);
		Assert::same(realpath($copyPath), $copyFile->getInfo()->getPathname());
		Assert::same(realpath($path), $file->getInfo()->getPathname());
		Assert::same($copyFile->isUsable(), $file->isUsable());
		Assert::true($file->remove());
		Assert::true($copyFile->remove());
	}

	public function testMove()
	{
		$path = $this->generateFileName();
		$movePath = $this->generateFileName();
		$file = new File($path);
		Assert::true($file->create());
		Assert::true($file->move($movePath));
		Assert::false(file_exists($path));
		Assert::same(realpath($movePath), $file->getInfo()->getPathname());
		Assert::true($file->remove());
	}

	/**
	 * @return string
	 */
	private function generateFileName()
	{
		return $path = $this->dataPath . DIRECTORY_SEPARATOR . time() . rand(1000000,999999999) . '.txt';
	}
}

\run(new FileTest());