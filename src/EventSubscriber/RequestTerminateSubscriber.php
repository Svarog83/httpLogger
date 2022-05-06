<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class RequestTerminateSubscriber implements EventSubscriberInterface {
	private EntityManagerInterface $entityManager;
	private string                 $headerKeyToLog;
	private LoggerInterface        $logger;

	public function __construct( EntityManagerInterface $entityManager, LoggerInterface $logger, string $headerKeyToLog ) {
		$this->entityManager = $entityManager;
		$this->logger        = $logger;
		$this->headerKeyToLog = $headerKeyToLog;
	}

	public function onKernelTerminate( TerminateEvent $event ): void {
		$request        = $event->getRequest();
		$requestHeaders = $request->headers->all();

		$needToLog      = FALSE;
		if ( !empty( $requestHeaders[ $this->headerKeyToLog ] ) ) {
			if ( is_array( $requestHeaders[ $this->headerKeyToLog ] ) ) {
				$needToLog = !empty( $requestHeaders[ $this->headerKeyToLog ][0] );
			} else {
				$needToLog = filter_var( $requestHeaders[ $this->headerKeyToLog ], FILTER_VALIDATE_BOOL );
			}
		}

		if ( $needToLog ) {
			$response   = $event->getResponse();
			$requestURL = $request->getSchemeAndHttpHost() . $request->getRequestUri();

			$requestBody = $request->getContent();

			$responseHeaders = $response->headers->all();
			$responseBody    = $response->getContent();
			$responseCode    = $response->getStatusCode();

			$ip = $request->getClientIp();
			if ( !$ip || $ip === '::0' || $ip === '::1' ) {
				$ip = '127.0.0.1';
			}

			$ip = ip2long( $ip );

			$log = new Log();

			$log->setUrl( $requestURL );
			$log->setRequestHeaders( $requestHeaders );
			$log->setRequestBody( $requestBody );
			$log->setResponseHeaders( $responseHeaders );
			$log->setResponseBody( $responseBody );
			$log->setResponseCode( $responseCode );
			$log->setIp( $ip );
			$log->setProcessedAt( new \DateTimeImmutable() );

			try {
				$this->entityManager->persist( $log );
				$this->entityManager->flush();
			} catch ( \Exception $e ) {
				$this->logger->error( $e->getMessage() );
				throw $e;
			}
		}
	}

	public static function getSubscribedEvents(): array {
		return [ 'kernel.terminate' => 'onKernelTerminate', ];
	}
}