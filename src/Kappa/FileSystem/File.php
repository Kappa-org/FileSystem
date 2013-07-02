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
class File extends FileSystem
{
	/**
	 * @param null $content
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function overwrite($content = null)
	{
		if($this->isCreated()) {
			if($content && !is_string($content) && !is_numeric($content)) {
				throw new InvalidArgumentException(__METHOD__ . " First argument expect to be string, null or number, " . gettype($content) . " given");
			}
			@file_put_contents($this->path, $content);
			if((string)$content === $this->read()) {
				return true;
			} else {
				return false;
			}
		} else {
			throw new IOException("File {$this->path} must be firstly created");
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
		if($this->isCreated()) {
			return file_get_contents($this->path);
		} else {
			throw new IOException("File {$this->path} must be firstly created");
		}
	}

	/**
	 * @return string
	 * @throws IOException
	 */
	public function getHash()
	{
		if($this->isCreated()) {
			return md5_file($this->path);
		} else {
			throw new IOException("File {$this->path} must be firstly created");
		}
	}

	/**
	 * @return bool
	 * @throws IOException
	 */
	public function remove()
	{
		if($this->isCreated()) {
			@unlink($this->path);
			return !$this->isCreated();
	 	} else {
			throw new IOException("File {$this->path} must be firstly created");
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
		if($this->isCreated()) {
			if (!is_string($target)) {
				throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
			}
			if (is_file($target) && !$overwrite) {
				throw new IOException("Failed to copy file to '$target', because file already exist");
			} else {
				if (@copy($this->path, $target) === true) {
					return ($returnNew) ? new File($target, File::INTUITIVE) : true;
				} else {
					return false;
				}
			}
		} else {
			throw new IOException("File {$this->path} must be firstly created");
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
				$this->path = realpath($file->getInfo()->getPathname());
				return true;
			} else {
				return false;
			}
		}
	}
}
