<?php
/**
 * This file is part of the Kappa\FileSystem package.
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
	/**
	 * @param File|Directory $object
	 * @throws InvalidArgumentException
	 */
	public static function remove($object)
	{
		if (!$object instanceof File && !$object instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . ": Argument must be instance of File or Directory");
		}
		\Nette\Utils\FileSystem::delete($object->getInfo()->getPathname());
	}
} 