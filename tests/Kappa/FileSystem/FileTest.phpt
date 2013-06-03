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
	private $path;

	/** @var \Kappa\FileSystem\File */
	private $file;

	protected function setUp()
	{
		$this->path = realpath(__DIR__ . '/../../data/file');
		Assert::false(file_exists($this->path . '/testFile.txt'));
		$this->restore();
		Assert::true(file_exists($this->path . '/testFile.txt'));
	}

	public function testGetInfo()
	{
		Assert::equal(new SplFileInfo($this->path), $this->file->getInfo());
	}

	public function testOverwrite()
	{
		Assert::same("", file_get_contents($this->file->getInfo()->getPathname()));
		Assert::type('\Kappa\FileSystem\File', $this->file->overwrite("Hello world!"));
		Assert::same("Hello world!", file_get_contents($this->file->getInfo()->getPathname()));

		Assert::throws(function () {
			$this->file->overwrite(array("some"));
		}, $this->exceptions['inv']);

		$this->restore();
	}

	public function testClean()
	{
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world!");
		Assert::same("Hello world!", file_get_contents($this->file->getInfo()->getPathname()));
		Assert::type('\Kappa\FileSystem\File', $this->file->clean());
		Assert::same("", file_get_contents($this->file->getInfo()->getPathname()));
	}

	public function testAppend()
	{
		Assert::same('', file_get_contents($this->file->getInfo()->getPathname()));
		Assert::type('\Kappa\FileSystem\File', $this->file->append("Hello world!"));
		Assert::same('Hello world!', file_get_contents($this->file->getInfo()->getPathname()));
		Assert::type('\Kappa\FileSystem\File', $this->file->append("Test write by"));
		Assert::same('Hello world!' . PHP_EOL . 'Test write by', file_get_contents($this->file->getInfo()->getPathname()));
		Assert::type('\Kappa\FileSystem\File', $this->file->append('Kappa\Tester', false));
		Assert::same('Hello world!' . PHP_EOL . 'Test write by Kappa\Tester', file_get_contents($this->file->getInfo()->getPathname()));

		Assert::throws(function () {
			$this->file->append(array('some'));
		}, $this->exceptions['inv']);
		Assert::throws(function () {
			$this->file->append('some', array('some'));
		}, $this->exceptions['inv']);

		$this->restore();
	}

	public function testRead()
	{
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world!");
		Assert::same("Hello world!", $this->file->read());

		$this->restore();
	}

	public function testGetHash()
	{
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world!");
		Assert::same(md5_file($this->path . '/testFile.txt'), $this->file->getHash());

		$this->restore();
	}

	public function testIsSame()
	{
		$_file = new File($this->path . '/newFile.txt');
		file_put_contents($_file->getInfo()->getPathname(), "Hello world!");
		Assert::false($this->file->isSame($_file));
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world!");
		Assert::true($this->file->isSame($_file));

		$this->restore();
	}

	public function testReplace()
	{
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world");
		Assert::type('\Kappa\FileSystem\File', $this->file->replace('#^Hello#', "My"));
		Assert::same('My world', file_get_contents($this->file->getInfo()->getPathname()));

		$this->restore();
	}

	public function testRemove()
	{
		Assert::false(file_exists($this->path . '/newFile.txt'));
		$file = new File($this->path . '/newFile.txt');
		Assert::true(file_exists($this->path . '/newFile.txt'));
		Assert::true($file->remove());
		Assert::false(file_exists($this->path . '/newFile.txt'));
	}

	public function testRename()
	{
		Assert::true(file_exists($this->file->getInfo()->getPathname()));
		Assert::false(file_exists($this->path . '/renamedFile.txt'));
		/** @var \Kappa\FileSystem\File $file */
		$file = $this->file->rename('renamedFile.txt');
		Assert::type('\Kappa\FileSystem\File', $file);

		new File($this->path . '/existFile.txt');
		Assert::throws(function () use ($file) {
			$file->rename('existFile.txt');
		}, $this->exceptions['io']);
		Assert::true(file_exists($this->path . '/renamedFile.txt'));
		Assert::type('\Kappa\FileSystem\File', $file = $file->rename('existFile.txt', true));
		Assert::false(file_exists($this->path . '/renamedFile.txt'));

		$this->restore();
	}

	public function testCopy()
	{
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world!");
		Assert::false(file_exists($this->path . '/copyFile.txt'));
		Assert::type('\Kappa\FileSystem\File', $file = $this->file->copy($this->path . '/copyFile.txt'));
		Assert::true(file_exists($this->path . '/copyFile.txt'));
		Assert::true($this->file->isSame($file));
		Assert::notSame($this->file, $file);
		unlink($file->getInfo()->getPathname());
		Assert::type('\Kappa\FileSystem\File', $file = $this->file->copy($this->path . '/copyFile.txt', false));
		Assert::same($this->file, $file);
		Assert::type('\Kappa\FileSystem\File', $file = $this->file->copy($this->path . '/copyFile.txt', false, true));
		Assert::throws(function () {
			$this->file->copy($this->path . '/copyFile.txt');
		}, $this->exceptions['io']);
		unlink($this->path . '/copyFile.txt');
	}

	public function testMove()
	{
		file_put_contents($this->file->getInfo()->getPathname(), "Hello world!");
		Assert::true(file_exists($this->path . '/testFile.txt'));
		Assert::false(file_exists($this->path . '/moveFile.txt'));
		Assert::type('\Kappa\FileSystem\File', $file = $this->file->move($this->path . '/moveFile.txt'));
		Assert::false(file_exists($this->path . '/testFile.txt'));
		Assert::true(file_exists($this->path . '/moveFile.txt'));
	}

	private function restore()
	{
		\Tester\Helpers::purge($this->path);
		$this->file = new File($this->path . '/testFile.txt');
	}

	protected function tearDown()
	{
		\Tester\Helpers::purge($this->path);
	}

	/** Providers */

}

\run(new FileTest());