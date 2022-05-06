<?php

namespace App\Tests\Controller;

use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RequestTerminateSubscriberTest extends WebTestCase {
	/**
	 * Tests that row is NOT added into log if the header_key_to_log is NOT set
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function testIndexPageWithoutSpecialHeaderKey(): void {
		$client          = static::createClient();
		$numberOfLogRows = self::getContainer()->get( LogRepository::class )->countRows();

		$client->request( 'GET', '/' );

		$newNumberOfLogRows = self::getContainer()->get( LogRepository::class )->countRows();

		self::assertResponseIsSuccessful();
		self::assertSelectorTextContains( 'a', 'Просмотр логов' );
		$this->assertEquals( $numberOfLogRows, $newNumberOfLogRows );
	}

	/**
	 * Tests that row is added into log if the header_key_to_log is set to 1 in request headers
	 *
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function testIndexPageWithSpecialHeaderKey(): void {
		$client          = static::createClient();
		$numberOfLogRows = self::getContainer()->get( LogRepository::class )->countRows();

		$containerBag   = self::getContainer()->get( ParameterBagInterface::class );
		$headerKeyToLog = $containerBag->get( 'header_key_to_log' );

		$client->request( 'GET', '/', [], [], [ 'HTTP_' . $headerKeyToLog => 1 ] );

		$newNumberOfLogRows = self::getContainer()->get( LogRepository::class )->countRows();

		self::assertResponseIsSuccessful();
		$this->assertEquals( $numberOfLogRows + 1, $newNumberOfLogRows );
	}

	/**
	 * Tests that row is added into log if the header_key_to_log is set to 1 in get parameter
	 *
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function testIndexPageWithSpecialGetParameter(): void {
		$client          = static::createClient();
		$numberOfLogRows = self::getContainer()->get( LogRepository::class )->countRows();

		$containerBag   = self::getContainer()->get( ParameterBagInterface::class );
		$keyToLog = $containerBag->get( 'header_key_to_log' );

		$client->request( 'GET', '/?' . $keyToLog . '=1' );

		$newNumberOfLogRows = self::getContainer()->get( LogRepository::class )->countRows();

		self::assertResponseIsSuccessful();
		$this->assertEquals( $numberOfLogRows + 1, $newNumberOfLogRows );
	}

	/**
	 * Tests that admin/http-log page is working
	 */
	public function testHttpLoggerPage(): void {
		$client          = static::createClient();

		$client->request( 'GET', '/admin/http-log' );

		self::assertResponseIsSuccessful();
		self::assertSelectorTextContains( 'h2', 'Всего строк' );
	}
}
