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

use Kappa\FileSystem\FileUpload;
use Kappa\Tester\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class FileUploadTest extends TestCase
{
	private $dataDir;

	protected function setUp()
	{
		$this->dataDir = __DIR__ . '/../../data';
	}

	public function testMove()
	{
		$originalFile = $this->dataDir . '/files/uploadFile';
		$afterUpload = $this->dataDir . '/files/afterUpload';
		$uploaded = $this->dataDir . '/files/uploaded';
		copy($originalFile, $uploaded);
		$fileUpload = new FileUpload(array(
			'name' => 'uploadFile',
			'type' => 'text/plain',
			'tmp_name' => $uploaded,
			'error' => 0,
			'size' => 100
		));
		Assert::false(is_file($afterUpload));
		$newFile = $fileUpload->move($afterUpload);
		Assert::type('Kappa\FileSystem\File', $newFile);
		Assert::true(is_file($afterUpload));
		Assert::same('Content', $newFile->read());

		unlink($afterUpload);
	}
}

\run(new FileUploadTest());