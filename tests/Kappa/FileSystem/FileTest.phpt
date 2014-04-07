<?php
/**
 * This file is part of the filesystem package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 * 
 * @testCase
 */

namespace filesystem\Tests;

use Kappa\FileSystem\File;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class FileTest
 * @package filesystem\Tests
 */
class FileTest extends TestCase
{
	/** @var string */
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data/files';
	}

	public function testCreate()
	{
		$createFile = $this->dataPath . '/createFile';
		$createFileWithContent = $this->dataPath . '/createFileWithContent';
		Assert::false(is_file($createFile));
		Assert::false(is_file($createFileWithContent));
		Assert::type('Kappa\FileSystem\File', File::create($createFile));
		Assert::type('Kappa\FileSystem\File', File::create($createFileWithContent, 'Hello world!'));
		Assert::true(is_file($createFile));
		Assert::true(is_file($createFileWithContent));
		Assert::same('', file_get_contents($createFile));
		Assert::same('Hello world!', file_get_contents($createFileWithContent));
		unlink($createFileWithContent);
		unlink($createFile);

		Assert::throws(function () {
			File::create(__FILE__);
		}, 'Kappa\FileSystem\FileAlreadyExistException');
		Assert::throws(function () {
			File::open('noFile');
		}, 'Kappa\FileSystem\FileNotFoundException');
	}

	public function testOpen()
	{
		$openFile = File::open($this->dataPath . '/openFile');
		Assert::type('Kappa\FileSystem\File', $openFile);
		Assert::same('Hello world!', file_get_contents($openFile->getInfo()->getPathname()));
	}

	public function testGetInfo()
	{
		$file = File::open(__FILE__);
		Assert::type('Kappa\FileSystem\SplFileInfo', $file->getInfo());
		Assert::same(__FILE__, $file->getInfo()->getPathname());
	}

	public function testRead()
	{
		$path = $this->dataPath . '/openFile';
		$file = File::open($path);
		Assert::same(file_get_contents($path), $file->read());
	}

	public function testOverwrite()
	{
		$path = $this->dataPath . '/file';
		$file = File::create($path, 'Hello');
		Assert::same('Hello', $file->read());
		Assert::true($file->overwrite('Test'));
		Assert::same('Test', $file->read());
		Assert::true($file->overwrite());
		Assert::same('', $file->read());

		unlink($path);
	}
}

\run(new FileTest());