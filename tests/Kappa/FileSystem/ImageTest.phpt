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

use Kappa\FileSystem\Image;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class ImageTest extends TestCase
{
	private $dataPath;

	protected function setUp()
	{
		$this->dataPath = __DIR__ . '/../../data';
	}
	
	public function testSave()
	{
		$image = Image::fromFile($this->dataPath . '/files/image.png');
		$newImage = $image->save($this->dataPath . '/files/newImage.png');
		Assert::type('Kappa\FileSystem\File', $newImage);
		Assert::same(realpath($this->dataPath . '/files/newImage.png'), $newImage->getInfo()->getPathname());

		unlink($this->dataPath . '/files/newImage.png');
	}
}

\run(new ImageTest());