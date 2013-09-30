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

namespace filesystem\Tests;

use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class SplFileInfoTest
 * @package filesystem\Tests
 */
class SplFileInfoTest extends TestCase
{
	/** @var \Kappa\FileSystem\SplFileInfo */
	private $splFileInfo;

	protected function setUp()
	{
		$this->splFileInfo = new SplFileInfo(__FILE__);
	}

	public function testIsImage()
	{
		Assert::false($this->splFileInfo->isImage());
	}

	public function testGetFileExtension()
	{
		Assert::same('.phpt', $this->splFileInfo->getFileExtension());
	}

	public function getRelativePath()
	{
		Assert::same('SplFileInfoTest.phpt', $this->splFileInfo->getRelativePath(__DIR__));
	}
}

\run(new SplFileInfoTest());