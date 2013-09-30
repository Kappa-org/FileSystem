<?php
/**
 * This file is part of the Kappa package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Kappa\FileSystem;

/**
 * Class File
 * @package Kappa\FileSystem
 */
class File extends FileStorage
{
	/**
	 * @param string $path
	 * @param int $action
	 * @throws FileNotFoundException
	 * @throws InvalidArgumentException
	 * @throws IOException
	 * @throws FileAlreadyExistException
	 */
	public function __construct($path, $action = self::CREATE)
	{
		if (!is_string($path)) {
			throw new InvalidArgumentException("Path must be string, " . gettype($path) . " given");
		}
		if ($action === self::CREATE) {
			if (!is_file($path)) {
				if ($this->create($path)) {
					$this->setPath($path);
				} else {
					throw new IOException("Unable to create file '{$path}'");
				}
			} else {
				throw new FileAlreadyExistException("File '{$path}' already exist. You must use LOAD constant");
			}
		}
		if ($action === self::LOAD) {
			if (is_file($path) && is_writable($path) && is_readable($path)) {
				$this->setPath($path);
			} else {
				throw new FileNotFoundException("File '{$path}' has not been found");
			}
		}
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	private function create($path)
	{
		$file = @fopen($path, 'w+');
		@fclose($file);

		return is_file($path);
	}

	/**
	 * @param null $content
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function overwrite($content = null)
	{
		$content = (string)$content;
		if ($this->isCreated()) {
			@file_put_contents($this->getPath(), $content);
			if ($content === $this->read()) {
				return true;
			} else {
				return false;
			}
		} else {
			throw new IOException("File {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @return bool
	 */
	public function clean()
	{
		return $this->overwrite(null);
	}

	/**
	 * @param string|null $content
	 * @param bool $newLine
	 * @return $this
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function append($content = null, $newLine = true)
	{
		if ($content && !is_string($content) && !is_numeric($content)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument expect to string, null or number, " . gettype($content) . " given");
		}
		$actual = $this->read();
		if ($actual) {
			$_content = $actual;
			$_content .= ($newLine) ? PHP_EOL . $content : " " . $content;
		} else {
			$_content = $content;
		}

		return $this->overwrite($_content);
	}

	/**
	 * @return string
	 * @throws IOException
	 */
	public function read()
	{
		if ($this->isCreated()) {
			return file_get_contents($this->getPath());
		} else {
			throw new IOException("File {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @return string
	 * @throws IOException
	 */
	public function getHash()
	{
		if ($this->isCreated()) {
			return md5_file($this->getPath());
		} else {
			throw new IOException("File {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @return bool
	 * @throws IOException
	 */
	public function remove()
	{
		if ($this->isCreated()) {
			@unlink($this->getPath());

			return !$this->isCreated();
		} else {
			throw new IOException("File {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @param string $newName
	 * @param bool $overwrite
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function rename($newName, $overwrite = false)
	{
		if ($this->isCreated()) {
			if (!is_string($newName)) {
				throw new InvalidArgumentException("New name must to be string, " . gettype($newName) . " given");
			}
			$newPath = $this->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
			if (is_file($newPath) && !$overwrite) {
				throw new IOException("Unable to overwrite file '{$newPath}'");
			}
			if (@rename($this->getPath(), $newPath)) {
				$this->setPath($newPath);

				return true;
			} else {
				return false;
			}
		} else {
			throw new IOException("Directory {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @param string $target
	 * @param bool $returnNew
	 * @param bool $overwrite
	 * @return File
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function copy($target, $returnNew = true, $overwrite = false)
	{
		if ($this->isCreated()) {
			if (!is_string($target)) {
				throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
			}
			if (is_file($target) && !$overwrite) {
				throw new IOException("Failed to copy file to '$target', because file already exist");
			} else {
				if (@copy($this->getPath(), $target) === true) {
					return ($returnNew) ? new File($target, File::INTUITIVE) : true;
				} else {
					return false;
				}
			}
		} else {
			throw new IOException("File {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @param string $target
	 * @param bool $overwrite
	 * @return File
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function move($target, $overwrite = false)
	{
		if (!is_string($target)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
		}
		if (is_file($target) && !$overwrite) {
			throw new IOException("Failed to move file to {$target}");
		} else {
			$file = $this->copy($target, true, $overwrite);
			if (true === $this->remove() && $file->isCreated()) {
				$this->setPath($file->getInfo()->getPathname());

				return true;
			} else {
				return false;
			}
		}
	}
}
