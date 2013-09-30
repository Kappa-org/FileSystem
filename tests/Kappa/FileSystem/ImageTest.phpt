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

namespace Kappa\FileSystem\Tests;

use Kappa\FileSystem\Image;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ImageTest
 * @package Kappa\FileSystem\Tests
 */
class ImageTest extends TestCase
{
	/** @var string */
	private $dataPath;

	/** @var string */
	private $image;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data';
		$this->image = realpath($this->dataPath . DIRECTORY_SEPARATOR . 'php-logo.png');
	}

	public function testSave()
	{
		$newImage = $this->dataPath . DIRECTORY_SEPARATOR . 'newImage.png';
		$image = Image::fromFile($this->image);
		$file =  $image->save($newImage);
		Assert::type('Kappa\FileSystem\File', $file);
		Assert::same($file->getPath(), realpath($newImage));
		Assert::true(unlink($newImage));
	}
}

\run(new ImageTest());