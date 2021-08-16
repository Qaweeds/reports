<?php
/*
 * This file is part of the bitrix24-php-sdk package.
 *
 * © Mesilov Maxim <mesilov.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bitrix24;

use \Bitrix24\Im\Attach\Item\User;
use \Psr\Log\NullLogger;

/**
 * Class UserTest
 * @package Bitrix24
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Bitrix24\Im\Attach\Item\User::getAttachTypeCode
	 */
	public function testUserTypeCode()
	{
		$obItem = new User(null);
		$this->assertSame($obItem->getAttachTypeCode(), 'USER');
	}
}