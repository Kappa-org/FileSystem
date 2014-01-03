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

	public function testConstruct()
	{
		$path = $this->randomFile();
		Assert::false(is_file($path));
		new File($path);
		Assert::true(is_file($path));
		Assert::true(unlink($path));

		Assert::throws(function () {
			new File(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
	}

	public function testGetBaseName()
	{
		$file = new File(__FILE__, File::LOAD);
		Assert::same('FileTest.phpt', $file->getBaseName());
	}

	public function testOverwrite()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::same("", $file->read());
		Assert::true($file->overwrite("Hello"));
		Assert::same("Hello", $file->read());
		Assert::true(unlink($path));
	}

	public function testRead()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::same("", $file->read());
		Assert::true($file->overwrite("Hello"));
		Assert::same("Hello", $file->read());
		Assert::true(unlink($path));
	}

	public function testClan()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::true($file->overwrite("Hello"));
		Assert::same("Hello", $file->read());
		Assert::true($file->clean());
		Assert::same("", $file->read());
		Assert::true(unlink($path));
	}

	public function testIsCreated()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::same("", $file->read());
		Assert::true(unlink($path));
		Assert::throws(function () use ($file) {
			$file->read();
		}, 'Kappa\FileSystem\IOException');
	}

	public function testAppend()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::same("", $file->read());
		Assert::true($file->append("Hello"));
		Assert::same("Hello", $file->read());
		Assert::true($file->append("Budry"));
		Assert::same("Hello" . PHP_EOL . "Budry", $file->read());
		Assert::true($file->append("test", false));
		Assert::same("Hello" . PHP_EOL . "Budry test", $file->read());
		Assert::true(unlink($path));
	}

	public function testRemove()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::true(is_file($file->getPath()));
		Assert::true($file->remove());
		Assert::false(is_file($file->getPath()));
	}

	public function testRename()
	{
		$path = $this->randomFile();
		$newPath = $this->dataPath . DIRECTORY_SEPARATOR . 'renamed.txt';
		$file = new File($path);
		Assert::true(is_file($path));
		Assert::false(is_file($this->dataPath . DIRECTORY_SEPARATOR . 'renamed.txt'));
		Assert::true($file->rename('renamed.txt'));
		Assert::same(realpath($newPath), $file->getPath());
		Assert::true(is_file($newPath));
		Assert::false(is_file($path));

		Assert::throws(function () use ($file) {
			$file->rename('renamed.txt', false);
		}, 'Kappa\FileSystem\IOException');
		Assert::true(unlink($newPath));
	}

	public function testCopy()
	{
		$path = $this->randomFile();
		$file = new File($path);
		$dirCopy = $this->dataPath . DIRECTORY_SEPARATOR . 'forcopy';
		Assert::true(mkdir($dirCopy));
		$copyPath = $dirCopy . DIRECTORY_SEPARATOR . 'copy.txt';
		Assert::true($file->copy($copyPath, false));
		Assert::true(is_file($path));
		Assert::true(is_file($copyPath));
		Assert::true(unlink($path));
		Assert::true(unlink($copyPath));
		Assert::true(rmdir($dirCopy));

		$path = $this->randomFile();
		$file = new File($path);
		$dirCopy = $this->dataPath . DIRECTORY_SEPARATOR . 'forcopy';
		Assert::true(mkdir($dirCopy));
		$copyPath = $dirCopy . DIRECTORY_SEPARATOR . 'copy.txt';
		Assert::type('Kappa\FileSystem\File', $file->copy($copyPath));

		Assert::throws(function () use ($file) {
			$file->copy(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
		Assert::throws(function () use ($file, $copyPath) {
			new File($copyPath);
			$file->copy($copyPath);
		}, 'Kappa\FileSystem\IOException');

		Assert::true(unlink($path));
		Assert::true(unlink($copyPath));
		Assert::true(rmdir($dirCopy));
	}

	public function testMove()
	{
		$path = $this->randomFile();
		$file = new File($path);
		$dirMove = $this->dataPath . DIRECTORY_SEPARATOR . 'formove';
		Assert::true(mkdir($dirMove));
		$movePath = $dirMove . DIRECTORY_SEPARATOR . 'move.txt';
		$origPath = $file->getPath();
		Assert::true(is_file($origPath));
		Assert::true($file->move($movePath));
		Assert::false(is_file($origPath));
		Assert::same(realpath($movePath), $file->getPath());

		Assert::throws(function () use ($file) {
			$file->move(array());
		}, 'Kappa\FileSystem\InvalidArgumentException');
		Assert::throws(function () use ($file, $movePath) {
			new File($movePath);
			$file->move($movePath);
		}, 'Kappa\FileSystem\IOException');

		Assert::true(unlink($movePath));
		Assert::true(rmdir($dirMove));
	}

	public function testGetPath()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::same(realpath($path), $file->getPath());
		Assert::true(unlink($path));
	}

	public function testGetInfo()
	{
		$path = $this->randomFile();
		$file = new File($path);
		Assert::equal(new SplFileInfo($path), $file->getInfo());
		Assert::true(unlink($path));
	}

	/**
	 * @return string
	 */
	private function randomFile()
	{
		return $path = $this->dataPath . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999) . '.txt';
	}
}

\run(new FileTest());