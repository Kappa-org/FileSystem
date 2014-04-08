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
use Kappa\Tester\TestCase;
use Nette\Http\FileUpload;
use Nette\Utils\Image;
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

	public function testUpload()
	{
		$originalFile = $this->dataPath . '/uploadFile';
		$afterUpload = $this->dataPath . '/afterUpload';
		$uploaded = $this->dataPath . '/uploaded';
		$uploaded2 = $this->dataPath . '/uploaded2';
		copy($originalFile, $uploaded);
		copy($originalFile, $uploaded2);
		$fileUpload = new FileUpload(array(
			'name' => 'uploadFile',
			'type' => 'text/plain',
			'tmp_name' => $uploaded,
			'error' => 0,
			'size' => 100
		));
		$fileUpload2 = new FileUpload(array(
			'name' => 'uploadFile',
			'type' => 'text/plain',
			'tmp_name' => $uploaded2,
			'error' => 0,
			'size' => 100
		));
		$directory = Directory::open($this->dataPath . '/directory');
		Assert::false(is_file($afterUpload));
		Assert::false(is_file($directory->getInfo()->getPathname() . '/uploadFile'));
		$file = File::upload($fileUpload, $afterUpload);
		$file2 = File::upload($fileUpload2, $directory);
		Assert::type('Kappa\FileSystem\File', $file);
		Assert::type('Kappa\FileSystem\File', $file2);
		Assert::true(is_file($afterUpload));
		Assert::true(is_file($directory->getInfo()->getPathname() . '/uploadFile'));
		Assert::same('Content', $file->read());
		Assert::same('Content', $file2->read());

		unlink($afterUpload);
		unlink($directory->getInfo()->getPathname() . '/uploadFile');
	}

	public function testFromImage()
	{
		$image = Image::fromFile($this->dataPath . '/image.png');
		$file = File::fromImage($image, $this->dataPath . '/newImage.png');
		Assert::type('Kappa\FileSystem\File', $file);
		Assert::same('newImage.png', $file->getInfo()->getBasename());

		unlink($this->dataPath . '/newImage.png');
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

	public function testClear()
	{
		$path = $this->dataPath . '/file';
		$file = File::create($path, 'Hello');
		Assert::same('Hello', $file->read());
		Assert::true($file->clear());
		Assert::same('', $file->read());

		unlink($path);
	}

	public function testAppend()
	{
		$path = $this->dataPath . '/file';
		$file = File::create($path, 'Hello');
		Assert::same('Hello', $file->read());
		Assert::true($file->append(' world', false));
		Assert::same('Hello world', $file->read());
		Assert::true($file->append('Hello guys!'));
		Assert::same('Hello world' . PHP_EOL . 'Hello guys!', $file->read());

		unlink($path);
	}

	public function testToImage()
	{
		$file = File::open($this->dataPath . '/image.png');
		Assert::type('Nette\Utils\Image', $file->toImage());
	}
}

\run(new FileTest());