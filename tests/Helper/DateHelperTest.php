<?php

use PHPUnit\Framework\TestCase;
use CLSystems\PhalCMS\Lib\Helper\Date;
use CLSystems\PhalCMS\Lib\Helper\User;

class DateHelperTest extends TestCase
{
	public function testGetInstance()
	{
		$this->assertInstanceOf(Date::class, Date::getInstance('now', 'Europe/Amsterdam'));
	}

	public function testConvertTimezone()
	{
		$dateInUTC = '2019-12-19 08:04:00';
		$dateInHCM = '2019-12-19 15:04:00';
		$date      = Date::getInstance($dateInUTC, 'CET');
		User::getInstance()->setParams(['timezone' => 'Europe/Amsterdam']);
		$this->assertEquals($dateInHCM, $date->toFormat('Y-m-d H:i:s'));
	}
}