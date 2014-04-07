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
 * Class FileNotFoundException
 * @package Kappa\FileSystem
 */
class FileNotFoundException extends IOException
{

}

/**
 * Class FileAlreadyExistException
 * @package Kappa\FileSystem
 */
class FileAlreadyExistException extends IOException
{

}

/**
 * Class DirectoryNotFoundException
 * @package Kappa\FileSystem
 */
class DirectoryNotFoundException extends IOException
{

}

/**
 * Class DirectoryAlreadyExistException
 * @package Kappa\FileSystem
 */
class DirectoryAlreadyExistException extends IOException
{

}

/**
 * Class IOException
 * @package Kappa\FileSystem
 */
class IOException extends \LogicException
{

}

/**
 * Class InvalidArgumentException
 * @package Kappa\FileSystem
 */
class InvalidArgumentException extends \LogicException
{

}