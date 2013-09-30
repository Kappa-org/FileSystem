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

	/**
	 * @return string
	 */
	private function randomFile()
	{
		return $path = $this->dataPath . DIRECTORY_SEPARATOR . time() . rand(1000000, 999999999) . '.txt';
	}
}

\run(new FileTest());