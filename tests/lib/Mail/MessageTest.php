<?php
/**
 * Copyright (c) 2014 Lukas Reschke <lukas@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace Test\Mail;

use OC\Mail\Message;
use OCP\Mail\IEMailTemplate;
use Symfony\Component\Mime\Email;
use Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MessageTest extends TestCase {
	/** @var Email */
	private $symfonyEmail;
	/** @var Message */
	private $message;

	/**
	 * @return array
	 */
	public function mailAddressProvider() {
		return [
			[['lukas@owncloud.com' => 'Lukas Reschke'], ['lukas@owncloud.com' => 'Lukas Reschke']],
			[['lukas@owncloud.com' => 'Lukas Reschke', 'lukas@öwnclöüd.com', 'lukäs@owncloud.örg' => 'Lükäs Réschke'],
				['lukas@owncloud.com' => 'Lukas Reschke', 'lukas@xn--wncld-iuae2c.com', 'lukäs@owncloud.xn--rg-eka' => 'Lükäs Réschke']],
			[['lukas@öwnclöüd.com'], ['lukas@xn--wncld-iuae2c.com']],
		];
	}

	/**
	 * @return array
	 */
	public function getMailAddressProvider() {
		return [
			[[], []],
			[['lukas@owncloud.com' => 'Lukas Reschke'], ['lukas@owncloud.com' => 'Lukas Reschke']],
		];
	}

	protected function setUp(): void {
		parent::setUp();

		$this->symfonyEmail = $this->getMockBuilder(Email::class)
			->disableOriginalConstructor()->getMock();

		$this->message = new Message($this->symfonyEmail, false);
	}

	/**
	 * @requires function idn_to_ascii
	 * @dataProvider mailAddressProvider
	 *
	 * @param string $unconverted
	 * @param string $expected
	 */
	public function testConvertAddresses($unconverted, $expected) {
		$this->assertSame($expected, self::invokePrivate($this->message, 'convertAddresses', [$unconverted]));
	}

	public function testSetFrom() {
		$this->symfonyEmail
			->expects($this->once())
			->method('from')
			->with(['lukas@owncloud.com']);
		$this->message->setFrom(['lukas@owncloud.com']);
	}


	/**
	 * @dataProvider getMailAddressProvider
	 *
	 * @param $swiftresult
	 * @param $return
	 */
	public function testGetFrom($swiftresult, $return) {
		$this->symfonyEmail
			->expects($this->once())
			->method('getFrom')
			->willReturn($swiftresult);

		$this->assertSame($return, $this->message->getFrom());
	}

	public function testSetReplyTo() {
		$this->symfonyEmail
			->expects($this->once())
			->method('replyTo')
			->with(['lukas@owncloud.com']);
		$this->message->setReplyTo(['lukas@owncloud.com']);
	}

	public function testGetReplyTo() {
		$this->symfonyEmail
			->expects($this->once())
			->method('getReplyTo')
			->willReturn(['lukas@owncloud.com']);

		$this->assertSame(['lukas@owncloud.com'], $this->message->getReplyTo());
	}

	public function testSetTo() {
		$this->symfonyEmail
			->expects($this->once())
			->method('to')
			->with(['lukas@owncloud.com']);
		$this->message->setTo(['lukas@owncloud.com']);
	}

	/**
	 * @dataProvider  getMailAddressProvider
	 */
	public function testGetTo($swiftresult, $return) {
		$this->symfonyEmail
			->expects($this->once())
			->method('getTo')
			->willReturn($swiftresult);

		$this->assertSame($return, $this->message->getTo());
	}

	public function testSetCc() {
		$this->symfonyEmail
			->expects($this->once())
			->method('cc')
			->with(['lukas@owncloud.com']);
		$this->message->setCc(['lukas@owncloud.com']);
	}

	/**
	 * @dataProvider  getMailAddressProvider
	 */
	public function testGetCc($swiftresult, $return) {
		$this->symfonyEmail
			->expects($this->once())
			->method('getCc')
			->willReturn($swiftresult);

		$this->assertSame($return, $this->message->getCc());
	}

	public function testSetBcc() {
		$this->symfonyEmail
			->expects($this->once())
			->method('bcc')
			->with(['lukas@owncloud.com']);
		$this->message->setBcc(['lukas@owncloud.com']);
	}

	/**
	 * @dataProvider  getMailAddressProvider
	 */
	public function testGetBcc($swiftresult, $return) {
		$this->symfonyEmail
			->expects($this->once())
			->method('getBcc')
			->willReturn($swiftresult);

		$this->assertSame($return, $this->message->getBcc());
	}

	public function testSetSubject() {
		$this->symfonyEmail
			->expects($this->once())
			->method('subject')
			->with('Fancy Subject');

		$this->message->setSubject('Fancy Subject');
	}

	public function testGetSubject() {
		$this->symfonyEmail
			->expects($this->once())
			->method('getSubject')
			->willReturn('Fancy Subject');

		$this->assertSame('Fancy Subject', $this->message->getSubject());
	}

	public function testSetPlainBody() {
		$this->symfonyEmail
			->expects($this->once())
			->method('text')
			->with('Fancy Body');

		$this->message->setPlainBody('Fancy Body');
	}

	public function testGetPlainBody() {
		$this->symfonyEmail
			->expects($this->once())
			->method('getTextBody')
			->willReturn('Fancy Body');

		$this->assertSame('Fancy Body', $this->message->getPlainBody());
	}

	public function testSetHtmlBody() {
		$this->symfonyEmail
			->expects($this->once())
			->method('html')
			->with('<blink>Fancy Body</blink>', 'utf-8');

		$this->message->setHtmlBody('<blink>Fancy Body</blink>');
	}

	public function testPlainTextRenderOption() {
		/** @var MockObject|Email $symfonyEmail */
		$symfonyEmail = $this->getMockBuilder(Email::class)
			->disableOriginalConstructor()->getMock();
		/** @var MockObject|IEMailTemplate $template */
		$template = $this->getMockBuilder(IEMailTemplate::class)
			->disableOriginalConstructor()->getMock();

		$message = new Message($symfonyEmail, true);

		$template
			->expects($this->never())
			->method('renderHTML');
		$template
			->expects($this->once())
			->method('renderText');
		$template
			->expects($this->once())
			->method('renderSubject');

		$message->useTemplate($template);
	}

	public function testBothRenderingOptions() {
		/** @var MockObject|Email $symfonyEmail */
		$symfonyEmail = $this->getMockBuilder(Email::class)
			->disableOriginalConstructor()->getMock();
		/** @var MockObject|IEMailTemplate $template */
		$template = $this->getMockBuilder(IEMailTemplate::class)
			->disableOriginalConstructor()->getMock();

		$message = new Message($symfonyEmail, false);

		$template
			->expects($this->once())
			->method('renderHTML');
		$template
			->expects($this->once())
			->method('renderText');
		$template
			->expects($this->once())
			->method('renderSubject');

		$message->useTemplate($template);
	}
}
