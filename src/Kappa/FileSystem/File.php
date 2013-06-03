<?php
/**
 * File.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 1.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class File
 *
 * @package Kappa\FileSystem
 */
class File extends FileSystem
{
	/** @var string */
	private $path;

	/**
	 * @param string $path
	 * @throws InvalidArgumentException
	 */
	public function __construct($path)
	{
		if (!is_string($path)) {
			throw new InvalidArgumentException(__METHOD__ . " Argument must to be string, " . gettype($path) . " given");
		}
		$this->path = (is_file($path) && is_writable($path)) ? realpath($path) : $this->create($path);
		parent::__construct($this->path);
	}

	/**
	 * @param string|null $content
	 * @return $this
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function overwrite($content = null)
	{
		if ($content && !is_string($content) && !is_numeric($content)) {
			throw new InvalidArgumentException(__METHOD__ . " Argument must to be string or null, " . gettype($content) . " given");
		}
		$file = @fopen($this->path, 'w');
		@fwrite($file, $content);
		if (true === (bool)@fclose($file)) {
			return $this;
		} else {
			throw new IOException("Failed to overwrite file '$this->path'");
		}
	}

	/**
	 * @return $this
	 * @throws IOException
	 */
	public function clean()
	{
		if ($this->overwrite(null)) {
			return $this;
		} else {
			throw new IOException("Failed to clean file '$this->path'");
		}
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
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string or null, " . gettype($content) . " given");
		}
		if (!is_bool($newLine)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($newLine) . " given");
		}
		$actual = $this->read();
		if ($actual) {
			$_content = $actual;
			$_content .= ($newLine) ? PHP_EOL . $content : " " . $content;
		} else {
			$_content = $content;
		}
		if ($this->overwrite($_content)) {
			return $this;
		} else {
			throw new IOException("Failed to append to file '$this->path'");
		}
	}

	/**
	 * @return string
	 */
	public function read()
	{
		return file_get_contents($this->path);
	}

	/**
	 * @param string $newName
	 * @param bool $overwrite
	 * @return $this
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function rename($newName, $overwrite = false)
	{
		if (!is_string($newName)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($newName) . " given");
		}
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($overwrite) . " given");
		}
		$newPath = $this->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
		if (file_exists($newPath) && !$overwrite) {
			throw new IOException("Failed to rename to '$newPath', because file $newPath already exist");
		} else {
			if (true === @rename($this->path, $newPath)) {
				$this->path = realpath($newPath);
				return $this;
			} else {
				throw new IOException("Failed to rename from '$this->path' to '$newPath'");
			}
		}

	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		return md5_file($this->path);
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function compare(File $file)
	{
		if ($this->getHash() === $file->getHash()) {
			return true;
		} else {
			return false;
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
		if (!is_string($target)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
		}
		if (!is_bool($returnNew)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($overwrite) . " given");
		}
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Third argument must to be bool, " . gettype($overwrite) . " given");
		}
		if (is_file($target) && !$overwrite) {
			throw new IOException("Failed to copy file to '$target', because file already exist");
		} else {
			if (true === @copy($this->path, $target)) {
				return ($returnNew) ? new  File($target) : $this;
			} else {
				throw new IOException("Failed to copy file '$target'");
			}
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
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($overwrite) . " given");
		}
		if (is_file($target) && !$overwrite) {
			throw new IOException("Failed to move file to '$target'");
		} else {
			$file = $this->copy($target, true, $overwrite);
			if (true === $this->remove()) {
				return $file;
			} else {
				throw new IOException("Failed to move file '$this->path'");
			}
		}
	}

	/**
	 * @return bool
	 * @throws IOException
	 */
	public function remove()
	{
		if (true === @unlink($this->path)) {
			$this->path = null;
			return true;
		} else {
			throw new IOException("Failed to remove file '$this->path'");
		}
	}

	/**
	 * @param string $pattern
	 * @return bool
	 */
	public function isContained($pattern)
	{
		if((bool)preg_match($pattern, $this->read())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $pattern
	 * @param string|null $replace
	 * @return $this
	 */
	public function replace($pattern, $replace = null)
	{
		$content = preg_replace($pattern, $replace, $this->read());
		$this->overwrite($content);
		return $this;
	}

	/**
	 * @param string $path
	 * @return string
	 * @throws IOException
	 */
	private function create($path)
	{
		$file = @fopen($path, 'w+');
		@fclose($file);
		if (file_exists($path)) {
			return realpath($path);
		} else {
			throw new IOException("Failed to create file '$path'");
		}
	}
}
