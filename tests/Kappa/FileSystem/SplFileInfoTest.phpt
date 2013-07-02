<?php
/**
 * DirectoryTest.phpt
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 7.5.13
 *
 * @package Kappa
 * @testCase Kappa\Tests\FileSystem\SplFileInfo
 */
 
namespace Kappa\Tests\FileSystem\SplFileInfo;

use Kappa\FileSystem\File;
use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class SplFileInfoTest
 * @package Kappa\Tests\FileSystem\SplFileInfo
 */
class SplFileInfoTest extends TestCase
{
	/** @var string */
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data';
	}

	public function testGetRelativePath()
	{
		$fileName = $this->generateFileName() ;
		$file = new File($this->dataPath . DIRECTORY_SEPARATOR . $fileName, File::INTUITIVE);
		Assert::same(DIRECTORY_SEPARATOR . $fileName, $file->getInfo()->getRelativePath($this->dataPath));
		Assert::true($file->remove());
	}

	public function testFileExtension()
	{
		$fileName = $this->generateFileName();
		$file = new File($this->dataPath . DIRECTORY_SEPARATOR . $fileName);
		Assert::true($file->create());
		Assert::same('.txt', $file->getInfo()->getFileExtension());
		Assert::true($file->remove());
	}

	public function testIsImage()
	{
		$fileName = $this->generateFileName();
		$file = new File($this->dataPath . DIRECTORY_SEPARATOR . $fileName);
		Assert::true($file->create());
		Assert::false($file->getInfo()->isImage());
		Assert::true($file->remove());
	}

	/**
	 * @return string
	 */
	private function generateFileName()
	{
		return time() . rand(1000000,999999999) . '.txt';
	}
}

\run(new SplFileInfoTest());