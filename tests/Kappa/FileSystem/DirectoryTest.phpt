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
use Tester\Helpers;

require_once __DIR__ . '/../bootstrap.php';

class DirectoryTest extends TestCase
{
	private $exceptions = array(
		'inv' => '\Kappa\FileSystem\InvalidArgumentException',
		'io' => '\Kappa\FileSystem\IOException',
	);

	/** @var \Kappa\FileSystem\Directory */
	private $directory;

	/** @var string */
	private $path;

	protected function setUp()
	{
		$this->path = __DIR__ . '/../../data/dir';
		Assert::false(file_exists($this->path . '/testDirectory'));
		$this->restore();
		Assert::true(file_exists($this->path . '/testDirectory'));
	}

	public function testRemove()
	{
		Assert::false(file_exists($this->path . '/forDelete'));
		$directory = new Directory($this->path . '/forDelete');
		Assert::true(file_exists($this->path . '/forDelete'));
		Assert::type('\Kappa\FileSystem\Directory', $directory->remove());
		Assert::false(file_exists($this->path . '/forDelete'));
	}

	public function testGetInfo()
	{
		Assert::equal(new SplFileInfo($this->path), $this->directory->getInfo());
	}

	public function testGetContent()
	{
		mkdir($this->directory->getInfo()->getPathname() . '/test');
		$file = new File($this->directory->getInfo()->getPathname() . '/test.txt');
		Assert::equal(array(
			realpath($this->directory->getInfo()->getPathname() . '/test') => new Directory($this->directory->getInfo()->getPathname() . '/test'),
			realpath($this->directory->getInfo()->getPathname() . '/test.txt') => $file
		), $this->directory->getContent());

		$this->restore();
	}

	public function testGetFiles()
	{
		new File($this->directory->getInfo()->getPathname() . '/test.txt');
		Assert::equal(array(realpath($this->directory->getInfo()->getPathname() . '/test.txt') => new File($this->directory->getInfo()->getPathname() . '/test.txt')), $this->directory->getFiles());

		$this->restore();
	}

	public function testGetDirectories()
	{
		mkdir($this->directory->getInfo()->getPathname() . '/test');
		Assert::equal(array(realpath($this->directory->getInfo()->getPathname() . '/test') => new Directory($this->directory->getInfo()->getPathname() . '/test')), $this->directory->getDirectories());
	}

	public function testRename()
	{
		Assert::true(file_exists($this->directory->getInfo()->getPathname()));
		Assert::false(file_exists($this->path . '/renamedDirectory'));
		Assert::type('\Kappa\FileSystem\Directory', $directory = $this->directory->rename('renamedDirectory'));
		Assert::false(file_exists($this->directory->getInfo()->getPathname()));
		Assert::true(file_exists($directory->getInfo()->getPathname()));

		$this->restore();

		mkdir($this->path . '/renamedDirectory');
		Assert::throws(function(){
			$this->directory->rename('renamedDirectory');
		}, $this->exceptions['io']);
		Assert::type('\Kappa\FileSystem\Directory', $this->directory->rename('renamedDirectory', true));
		Assert::false(file_exists($this->path . '/testDirectory'));

		$this->restore();

		Assert::throws(function () {
			$this->directory->rename(array('some'));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$this->directory->rename('some', array('some'));
		}, $this->exceptions['inv']);
	}

	public function testAppend()
	{
		$file = new File($this->path . '/append.txt');
		Assert::false(file_exists($this->directory->getInfo()->getPathname() . '/append.txt'));
		Assert::type('\Kappa\FileSystem\Directory', $this->directory->append($file));
		Assert::true(file_exists($this->directory->getInfo()->getPathname() . '/append.txt'));

		$this->restore();
	}

	public function testCopy()
	{
		new File($this->directory->getInfo()->getPathname() . '/test.txt');
		Assert::true(file_exists($this->directory->getInfo()->getPathname()));
		Assert::true(file_exists($this->directory->getInfo()->getPathname() . '/test.txt'));
		Assert::false(file_exists($this->path . '/copyDirectory'));
		Assert::false(file_exists($this->path . '/copyDirectory/test.txt'));
		Assert::type('\Kappa\FileSystem\Directory', $directory = $this->directory->copy($this->path . '/copyDirectory'));
		Assert::notSame($this->directory, $directory);
		Assert::true(file_exists($this->directory->getInfo()->getPathname()));
		Assert::true(file_exists($directory->getInfo()->getPathname()));
		Assert::true(file_exists($directory->getInfo()->getPathname() . '/test.txt'));

		$this->restore();

		Assert::type('\Kappa\FileSystem\Directory', $directory = $this->directory->copy($this->path . '/copyDirectory', array(), false));
		Assert::same($this->directory, $directory);

		$this->restore();

		$file = new File($this->directory->getInfo()->getPathname() . '/test.txt');
		Assert::type('\Kappa\FileSystem\Directory', $directory = $this->directory->copy($this->path . '/copyDirectory', array("test.txt")));
		Assert::true(file_exists($directory->getInfo()->getPathname()));
		Assert::false(file_exists($directory->getInfo()->getPathname() . '/test.txt'));

		$this->restore();
	}

	public function testMove()
	{
		new File($this->directory->getInfo()->getPathname() . '/test.txt');
		Assert::true(file_exists($this->directory->getInfo()->getPathname()));
		Assert::true(file_exists($this->directory->getInfo()->getPathname() . '/test.txt'));
		Assert::false(file_exists($this->path . '/moveDirectory'));
		Assert::false(file_exists($this->path . '/moveDirectory/test.txt'));
		Assert::type('\Kappa\FileSystem\Directory', $directory = $this->directory->move($this->path . '/moveDirectory'));
		Assert::false(file_exists($this->directory->getInfo()->getPathname()));
		Assert::false(file_exists($this->directory->getInfo()->getPathname() . '/test.txt'));
		Assert::true(file_exists($directory->getInfo()->getPathname()));
		Assert::true(file_exists($directory->getInfo()->getPathname() . '/test.txt'));

		$this->restore();

		// All test same as testCopy()
	}

	private function restore()
	{
		Helpers::purge($this->path);
		$this->directory = new Directory($this->path . '/testDirectory');
	}

	protected function tearDown()
	{
		\Tester\Helpers::purge($this->path);
	}
}

\run(new DirectoryTest());