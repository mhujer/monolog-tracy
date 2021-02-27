<?php
/**
 * This file is part of the Nella Project (https://monolog-tracy.nella.io).
 *
 * Copyright (c) Patrik Votoček (https://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.md that was distributed with this source code.
 */

namespace Nella\MonologTracy\Tracy;

class LoggerHelperTest extends \Nella\MonologTracy\TestCase
{

	/** @var LoggerHelper */
	private $loggerHelper;

	public function setUp(): void
	{
		parent::setUp();
		$logDirectory = sys_get_temp_dir() . '/' . getmypid() . microtime() . '-LoggerHelperTest';
		@rmdir($logDirectory); // directory may not exist
		if (@mkdir($logDirectory) === FALSE && !is_dir($logDirectory)) {
			$this->fail(sprintf('Temp directory "%s" could not be created.', $logDirectory));
		}

		$this->loggerHelper = new LoggerHelper($logDirectory, new \Tracy\BlueScreen());
	}

	public function testRenderToFile()
	{
		$file = $this->loggerHelper->renderToFile(new \Exception('Test exception'));
		$this->assertFileExists($file);
		$this->assertStringContainsString('Test exception', file_get_contents($file));
	}

	public function testLog()
	{
	    $this->expectException(\Nella\MonologTracy\Tracy\NotSupportedException::class);
	    $this->expectExceptionMessage('LoggerHelper::log is not supported.');

		$this->loggerHelper->log('Test');
	}

	public function testDefaultMailer()
	{
	    $this->expectException(\Nella\MonologTracy\Tracy\NotSupportedException::class);
	    $this->expectExceptionMessage('LoggerHelper::formatMessage is not supported.');

		$this->loggerHelper->defaultMailer('Test', 'email@example.com');
	}

	public function testLogDirectoryCannotBeCreatedIfThereIsFileWithSameName()
	{
		$logDirectoryParent = sys_get_temp_dir() . '/' . getmypid() . microtime() . '-LoggerHelperTest';
		$logDirectory = $logDirectoryParent . '/logdir';

		mkdir($logDirectoryParent);

		// create a dummy file with the same name as the log directory
		file_put_contents($logDirectoryParent . '/logdir', 'dummy');
		$this->assertFileExists($logDirectory);

		$this->expectException(\Nella\MonologTracy\Tracy\LogDirectoryCouldNotBeCreatedException::class);

		new LoggerHelper($logDirectory, new \Tracy\BlueScreen());
	}

}
