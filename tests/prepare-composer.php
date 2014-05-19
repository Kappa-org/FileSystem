<?php
/**
 * This file is part of the Kappa\FileSystem package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

$rootDir = __DIR__ . '/..';
$testsDir = __DIR__;

if (getenv('NETTE') !== 'default') {
	$composerFile = $testsDir . '/composer-' . getenv('NETTE') . '.json';

	unlink($rootDir . '/composer.json');
	copy($composerFile, $rootDir . '/composer.json');

	echo "Using tests/", basename($composerFile) . PHP_EOL;

} else {
	echo "Using default composer.json" . PHP_EOL;
}