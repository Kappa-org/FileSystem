<?php
/**
 * This file is part of the ${PACKAGE} package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Kappa\FileSystem;

/**
 * Class FileSystem
 * @package Kappa\FileSystem
 */
class FileSystem
{
	const STRICT = 0;

	const INTUITIVE = 1;

	/** @var string */
	protected $path;

	/**
	 * @param string $path
	 * @param int $sensitivity
	 * @throws InvalidArgumentException
	 */
	public function __construct($path, $sensitivity = self::STRICT)
	{
		if (!is_string($path)) {
			throw new InvalidArgumentException(__METHOD__ . " Argument must to be string, " . gettype($path) . " given");
		}
		$this->path = $path;
		if($this->isUsable()) {
			$this->path = realpath($path);
		} else {
			if($sensitivity === self::INTUITIVE) {
				$this->create();
			}
		}

	}

	/**
	 * @return bool
	 * @throws IOException
	 */
	public function create()
	{
		if(!$this->isUsable()) {
			if($this instanceof File) {
				$file = @fopen($this->path, 'w+');
				@fclose($file);
			} elseif ($this instanceof Directory) {
				@mkdir($this->path, 0777);
			}
			$this->path = realpath($this->path);
			return $this->isUsable();
		} else {
			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function isUsable()
	{
		if (is_writable($this->path) && is_readable($this->path)) {
			if($this instanceof File) {
				return is_file($this->path);
			} elseif ($this instanceof Directory) {
				return is_dir($this->path);
			}
		} else {
			return false;
		}
	}

	/**
	 * @return SplFileInfo
	 */
	public function getInfo()
	{
		return new SplFileInfo($this->path);

	}

	/**
	 * @param string $newName
	 * @param bool $overwrite
	 * @return bool|File|Directory
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function rename($newName, $overwrite = false)
	{
		if (!is_string($newName)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($newName) . " given");
		}
		$newPath = $this->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
		if($this instanceof File) {
			if(is_file($newPath) && !$overwrite) {
				throw new IOException("Failed to rename to '$newPath', because file $newPath already exist");
			}
		}
		if($this instanceof Directory) {
			if (is_dir($newPath) && !$overwrite) {
				throw new IOException("Failed to rename to '$newPath', because file $newPath already exist");
			} else {
				if(is_dir($newPath)) {
					$directory = new Directory($newPath);
					$directory->remove();
				}
			}
		}
		if(@rename($this->path, $newPath) === true) {
			$this->path = $newPath;
			return true;
		} else {
			return false;
		}
	}
}