<?php
/**
 * FileTest.phpt
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 4.5.13
 *
 * @package Kappa
 */
 
namespace Kappa\Tests\FileSystem\File;

use Kappa\FileSystem\File;
use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\Helpers;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class FileTest extends TestCase
{
	private $exceptions = array(
		'inv' => '\Kappa\FileSystem\InvalidArgumentException',
		'io' => '\Kappa\FileSystem\IOException',
	);

	/** @var string */
	private $directory;

	/** @var string */
	private $path;

	/** @var \Kappa\FileSystem\File */
	private $file;

	protected function setUp()
	{
		$this->directory = realpath(__DIR__ . '/../../data/file');
		$this->path = $this->directory . '/test.txt';
		$this->file = new File($this->path);

		$this->prepare();
	}

	public function testConstruct()
	{
		$this->prepare();
		$existPath = realpath($this->directory . '/test.txt');
		$existFile = new File($existPath);
		$nonExistPath = $this->directory . '/noExist.txt';
		$nonExistFile = new File($nonExistPath);

		Assert::true(file_exists($existPath));
		Assert::true($existFile instanceof File);
		Assert::same($existPath, $this->getReflection()->invokeProperty($existFile, 'path'));
		Assert::true(file_exists($nonExistPath));
		Assert::true($nonExistFile instanceof File);
		Assert::same(Helpers::repairPathSeparators($nonExistPath), $this->getReflection()->invokeProperty($nonExistFile, 'path'));

		@unlink($this->directory . '/noExist.txt');
		Assert::throws(function () {
			new File(array('path'));
		}, $this->exceptions['inv']);
	}

	public function testGetInfo()
	{
		Assert::equal(new SplFileInfo($this->path), $this->file->getInfo());
	}

	public function testOverwrite()
	{
		$this->prepare();
		Assert::same("Hello world!", file_get_contents($this->path));
		Assert::true($this->file->overwrite("Good day!") instanceof File);
		Assert::same("Good day!", file_get_contents($this->path));
		Assert::true($this->file->overwrite(500) instanceof File);
		Assert::same("500", file_get_contents($this->path));
		Assert::throws(function () {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->overwrite(array("text"));
		}, $this->exceptions['inv']);
	}

	public function testClean()
	{
		$this->prepare();
		Assert::same("Hello world!", file_get_contents($this->path));
		Assert::true($this->file->clean() instanceof File);
		Assert::same("", file_get_contents($this->path));
	}

	public function testAppend()
	{
		$this->prepare();
		Assert::same("Hello world!", file_get_contents($this->path));
		Assert::true($this->file->append("I'm tester") instanceof File);
		Assert::same("Hello world!" . PHP_EOL . "I'm tester", file_get_contents($this->path));
		Assert::true($this->file->append("with name Tester", false) instanceof File);
		Assert::same("Hello world!" . PHP_EOL . "I'm tester with name Tester", file_get_contents($this->path));

		Assert::throws(function () {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->append(array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->append("Test", array("test"));
		}, $this->exceptions['inv']);
	}

	public function testRead()
	{
		Assert::same("Hello world!", $this->file->read());
	}

	public function testRename()
	{
		$this->prepare();
		Assert::true(file_exists($this->path));
		Assert::same("Hello world!", file_get_contents($this->path));
		Assert::false(file_exists($this->directory . '/newName.txt'));
		Assert::true($this->file->rename('newName.txt') instanceof File);
		Assert::true(file_exists($this->directory . '/newName.txt'));
		Assert::same("Hello world!", file_get_contents($this->directory . '/newName.txt'));
		Assert::true($this->file->rename("existFile.txt", true) instanceof File);
		Assert::false(file_exists($this->path));
		$this->prepare();

		Assert::throws(function() {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->rename("existFile.txt");
		}, $this->exceptions['io']);
		Assert::throws(function() {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->rename(array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function() {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->rename("/noExist/file.txt");
		}, $this->exceptions['io']);
	}

	public function testGetHash()
	{
		$this->prepare();
		Assert::same(md5_file($this->path), $this->file->getHash());
	}

	public function testCompare()
	{
		$this->prepare();
		Assert::true($this->file->compare($this->file));
		$this->prepare();
		$_file = new File($this->directory . '/test_copy.txt');
		$_file->overwrite("Hello world!");
		Assert::true($this->file->compare(new File($this->directory . '/test_copy.txt')));
		Assert::false($this->file->compare(new File($this->directory . '/existFile.txt')));
	}

	public function testCopy()
	{
		$this->prepare();
		$copyFile = $this->directory . '/copyFile.txt';
		Assert::false(file_exists($copyFile));
		Assert::same(Helpers::repairPathSeparators($copyFile), $this->getReflection()->invokeProperty($this->file->copy($copyFile), 'path'));
		$copyExistFile = $this->directory . '/existFile.txt';
		Assert::same(realpath($copyExistFile), $this->getReflection()->invokeProperty($this->file->copy($copyExistFile, true, true), 'path'));
		$this->prepare();
		Assert::same(Helpers::repairPathSeparators($this->path), $this->getReflection()->invokeProperty($this->file->copy($copyFile, false), 'path'));
		$this->prepare();
		Assert::throws(function () use($copyExistFile) {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->copy($copyExistFile);
		}, $this->exceptions['io']);
		Assert::throws(function () {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->copy(array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->copy("test", array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$file = new File(__DIR__ . '/../../data/file/test.txt');
			$file->copy("test", true, 5);
		}, $this->exceptions['inv']);
	}

	public function testRemove()
	{
		$this->prepare();
		$path = $this->directory . '/newFile';
		$file = new File($path);
		Assert::true(file_exists($path));
		Assert::true($file->remove());
		Assert::false(file_exists($path));
	}

	public function testMove()
	{
		$this->prepare();
		$path = $this->directory . '/newDir/newFile.txt';
		Assert::false(file_exists($path));
		Assert::same(Helpers::repairPathSeparators($path), $this->getReflection()->invokeProperty($this->file->move($path), 'path'));
		Assert::true(file_exists($path));
	}

	public function testCorresponds()
	{
		$this->prepare();
		Assert::true($this->file->corresponds("#Hello#"));
		Assert::false($this->file->corresponds("#\s{5}#"));
	}

	public function testReplace()
	{
		$this->prepare();
		Assert::same("Hello world!", $this->file->read());
		$this->file->replace("#Hello#", "Bay");
		Assert::same("Bay world!", $this->file->read());
	}

	private function prepare()
	{
		\Tester\Helpers::purge($this->directory);
		$this->file = new File($this->path);
		if(!file_exists($this->directory . '/newDir'))
			mkdir($this->directory . '/newDir');
		$file = @fopen($this->path, 'w+');
		@fwrite($file, "Hello world!");
		@fclose($file);
		$file = @fopen($this->directory . '/existFile.txt', 'w+');
		@fwrite($file, "Hello");
		@fclose($file);
	}

	protected function tearDown()
	{
		$this->prepare();
	}

	/** Providers */

}
\run(new FileTest());