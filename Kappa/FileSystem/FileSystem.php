<?php
/**
 * FileSystem.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 1.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class FileSystem
 *
 * @package Kappa\FileSystem
 */
class FileSystem
{
	/** @var string */
	private $path;

	/**
	 * @param string $path
	 */
	protected function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * @return SplFileInfo
	 */
	public function getInfo()
	{
		return new SplFileInfo($this->path);
	}
}
