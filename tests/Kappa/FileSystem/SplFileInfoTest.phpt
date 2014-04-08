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

use Kappa\FileSystem\SplFileInfo;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class SplFileInfoTest
 * @package Kappa\FileSystem\Tests
 */
class SplFileInfoTest extends TestCase
{
	public function testIsImage()
	{
		$file = new SplFileInfo(__FILE__);
		$image = new SplFileInfo(__DIR__ . '/../../data/files/image.png');
		Assert::false($file->isImage());
		Assert::true($image->isImage());
	}

	public function testGetRelativePath()
	{
		$file = new SplFileInfo(__FILE__);
		Assert::same('/SplFileInfoTest.phpt', $file->getRelativePath(__DIR__));
		$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../';
		Assert::same('/FileSystem/SplFileInfoTest.phpt', $file->getRelativePath());
	}
}

\run(new SplFileInfoTest());