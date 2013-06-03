<?php
/**
 * DirectoryTest.phpt
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 7.5.13
 *
 * @package Kappa
 */
 
namespace Kappa\Tests\FileSystem\Directory;

use Kappa\FileSystem\Directory;
use Kappa\FileSystem\File;
use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class DirectoryTest extends TestCase
{
	private $exceptions = array(
		'inv' => '\Kappa\FileSystem\InvalidArgumentException',
		'io' => '\Kappa\FileSystem\IOException',
	);

	/** @var string */
	private $directory;

	/** @var string */
	private $path;

	/** @var \Kappa\FileSystem\Directory */
	private $dir;

	protected function setUp()
	{
		if(!is_dir(__DIR__ . '/../../data/dir'))
			@mkdir(__DIR__ . '/../../data/dir');
		$this->directory = __DIR__ . '/../../data/dir';
		$this->path = $this->directory . '/directory';
		$this->dir = new Directory($this->path);
	}

	public function testConstruct()
	{
		// Exist directory
		Assert::true(file_exists($this->directory));
		Assert::true(new Directory($this->directory) instanceof Directory);
		Assert::true(file_exists($this->directory));

		// Non-exist directory
		$path = $this->directory . '/non-exist';
		Assert::false(file_exists($path));
		Assert::true(new Directory($path) instanceof Directory);
		Assert::true(file_exists($path));

		Assert::throws(function () {
			new Directory(array("test"));
		}, $this->exceptions['inv']);
	}

	public function testRemove()
	{
		Assert::true(file_exists($this->path));
		Assert::true($this->dir->remove() instanceof Directory);
		Assert::false(file_exists($this->path));

	}

	public function testRename()
	{
		$rename = $this->directory . '/renamedDir';
		Assert::true(file_exists($this->path));
		Assert::false(file_exists($rename));
		Assert::true($this->dir->rename("renamedDir") instanceof Directory);
		Assert::false(file_exists($this->path));
		Assert::true(file_exists($rename));
	}

	public function testGetInfo()
	{
		Assert::equal(new SplFileInfo($this->path), $this->dir->getInfo());
	}

	public function testGetContent()
	{
		mkdir($this->path . '/test');
		Assert::equal(array(realpath($this->path . '/test') => new \SplFileInfo($this->path . '/test')), $this->dir->getContent());
	}

	public function testGetFiles()
	{
		$file = @fopen($this->path . '/test.txt', 'w+');
		@fwrite($file, "");
		@fclose($file);
		Assert::equal(array(realpath($this->path . '/test.txt') => new File($this->path . '/test.txt')), $this->dir->getFiles());
	}

	public function testGetDirectories()
	{
		mkdir($this->path . '/test');
		Assert::equal(array(realpath($this->path . '/test') => new Directory($this->path . '/test')), $this->dir->getDirectories());
	}

	public function testCopy()
	{
		$copyPath = $this->directory . '/afterCopy';
		$file = @fopen($this->path . '/test.txt', 'w+');
		@fwrite($file, "");
		@fclose($file);
		mkdir($this->path . '/test');
		Assert::false(file_exists($copyPath));
		Assert::true(file_exists($this->path));
		Assert::true($this->dir->copy($copyPath) instanceof Directory);
		Assert::true(file_exists($copyPath));
		Assert::true(file_exists($this->path));

		Assert::throws(function () {
			$directory = new Directory( __DIR__ . '/../../data/dir/directory');
			$directory->copy(array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$directory = new Directory( __DIR__ . '/../../data/dir/directory');
			$directory->copy("test", array(), array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$directory = new Directory( __DIR__ . '/../../data/dir/directory');
			$directory->copy("test", array(), true, array("test"));
		}, $this->exceptions['inv']);
	}

	public function testMove()
	{
		$copyPath = $this->directory . '/afterMove';
		$file = @fopen($this->path . '/test.txt', 'w+');
		@fwrite($file, "");
		@fclose($file);
		mkdir($this->path . '/test');
		Assert::false(file_exists($copyPath));
		Assert::true(file_exists($this->path));
		Assert::true($this->dir->move($copyPath) instanceof Directory);
		Assert::true(file_exists($copyPath));
		Assert::false(file_exists($this->path));

		Assert::throws(function () {
			$directory = new Directory( __DIR__ . '/../../data/dir/directory');
			$directory->move(array("test"));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$directory = new Directory( __DIR__ . '/../../data/dir/directory');
			$directory->move("test", array("test"));
		}, $this->exceptions['inv']);
	}

	public function testAppend()
	{
		$_file = new File($this->directory . '/../dir/test.txt');
		Assert::false(file_exists($this->path . '/test.txt'));
		Assert::true($this->dir->append($_file) instanceof Directory);
		Assert::true(file_exists($this->path . '/test.txt'));
	}

	private function prepare()
	{
		\Tester\Helpers::purge($this->directory);
	}

	protected function tearDown()
	{
		$this->prepare();
	}
}

\run(new DirectoryTest());