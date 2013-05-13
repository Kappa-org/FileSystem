<?php
/**
 * Image.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 10.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class Image
 *
 * @package Kappa\FileSystem
 */
class Image extends File
{
	/** @var \Nette\Image */
	private $image;

	/**
	 * @param string $original
	 * @param string $target
	 * @param array $sizes
	 * @param string $flag
	 * @throws IOException
	 */
	public function __construct($original, $target, array $sizes = array(), $flag = "fit")
	{
		if(!file_exists($original)) {
			throw new IOException("File '$original' not heva been found");
		}
		$this->image = \Nette\Image::fromFile($original);
		if(count($sizes) > 0) {
			$this->resize($sizes, $this->getFlag($flag));
		}
		$this->save($target);
		return parent::__construct($target);
	}

	/**
	 * @param array $sizes
	 */
	private function resize(array $sizes)
	{
		list($width, $height) = $sizes;
		$this->image->resize($width, $height);
	}

	/**
	 * @param string $flag
	 * @return int
	 * @throws InvalidArgumentException
	 */
	private function getFlag($flag)
	{
		$flags = array(
			'exact' => \Nette\Image::EXACT,
			'fill' => \Nette\Image::FILL,
			'fit' => \Nette\Image::FIT,
			'shrink_only' => \Nette\Image::SHRINK_ONLY,
			'stretch' => \Nette\Image::STRETCH,
		);
		if(!array_key_exists($flag, $flags)) {
			throw new InvalidArgumentException("Unknown flag '$flag'");
		} else {
			return $flags[$flag];
		}
	}

	/**
	 * @param string $target
	 * @return bool
	 * @throws IOException
	 */
	private function save($target)
	{
		$this->image->save($target);
		if(file_exists($target)) {
			return true;
		} else {
			throw new IOException("Failed to save image '$target'");
		}
	}
}
