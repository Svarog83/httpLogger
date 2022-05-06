<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class RequestTerminateSubscriber implements EventSubscriberInterface {
	private array                  $enableForEnv;
	private EntityManagerInterface $entityManager;
	private string                 $env;
	private string                 $headerKeyToLog;
	private LoggerInterface        $logger;
	private TerminateEvent          $event;

	public function __construct( EntityManagerInterface $entityManager, LoggerInterface $logger, string $headerKeyToLog, array $enableForEnv, string $env ) {
		$this->entityManager  = $entityManager;
		$this->logger         = $logger;
		$this->headerKeyToLog = $headerKeyToLog;
		$this->enableForEnv = $enableForEnv;
		$this->env = $env;
	}

	/**
	 * On kernel terminate we need to save a log (if it's required)
	 *
	 * @param TerminateEvent $event
	 * @throws \Exception
	 */
	public function onKernelTerminate( TerminateEvent $event ): void {
		$this->event = $event;

		if ( !$this->checkIfNeedToLog() ) {
			return;
		}

		if ( !$this->checkIfNeedToLogInCurrentEnv() ) {
			return;
		}

		$log = $this->collectLog();

		try {
			$this->entityManager->persist( $log );
			$this->entityManager->flush();
		} catch ( \Exception $e ) {
			$this->logger->error( $e->getMessage() );
			throw $e;
		}
	}

	/**
	 * Determines if the logger should work in the curren environment
	 *
	 * @return bool|mixed
	 */
	private function checkIfNeedToLogInCurrentEnv() {
		return in_array($this->env, $this->enableForEnv, TRUE);
	}

	/**
	 * Determines if the agreed key presents in request's headers
	 *
	 * @return bool|mixed
	 */
	private function checkIfNeedToLog() {
		return $this->checkIfNeedToLogInRequestParameters() || $this->checkIfNeedToLogInHeaders();
	}

	private function checkIfNeedToLogInRequestParameters(): bool {
		$request = $this->event->getRequest();

		return filter_var( $request->query->get( $this->headerKeyToLog ),
						   FILTER_VALIDATE_BOOL ) || filter_var( $request->request->get( $this->headerKeyToLog ),
																 FILTER_VALIDATE_BOOL );
	}

	private function checkIfNeedToLogInHeaders() {
		$requestHeaders = $this->event->getRequest()->headers->all();
		$needToLog      = FALSE;
		if ( !empty( $requestHeaders[ $this->headerKeyToLog ] ) ) {
			if ( is_array( $requestHeaders[ $this->headerKeyToLog ] ) ) {
				$needToLog = !empty( $requestHeaders[ $this->headerKeyToLog ][0] );
			} else {
				$needToLog = filter_var( $requestHeaders[ $this->headerKeyToLog ], FILTER_VALIDATE_BOOL );
			}
		}

		return $needToLog;
	}

	/**
	 * Returns an integer representation of IP
	 *
	 * @return false|int
	 */
	private function getRequestIP( $ip ) {
		if ( !$ip || $ip === '::0' || $ip === '::1' ) {
			$ip = '127.0.0.1';
		}

		$ip = ip2long( $ip );

		return $ip;
	}

	/**
	 * Collects required data from request and sets it into a new Log record
	 *
	 * @return Log
	 */
	private function collectLog(): Log {
		$request        = $this->event->getRequest();
		$requestHeaders = $request->headers->all();
		$response       = $this->event->getResponse();
		$requestURL     = $request->getSchemeAndHttpHost() . $request->getRequestUri();

		$requestBody = $request->getContent();

		$responseHeaders = $response->headers->all();
		$responseBody    = $response->getContent();
		$responseCode    = $response->getStatusCode();

		$ip = $this->getRequestIP( $request->getClientIp() );

		$log = new Log();
		$log->setUrl( $requestURL );
		$log->setRequestHeaders( $requestHeaders );
		$log->setRequestBody( $requestBody );
		$log->setResponseHeaders( $responseHeaders );
		$log->setResponseBody( $responseBody );
		$log->setResponseCode( $responseCode );
		$log->setIp( $ip );
		$log->setProcessedAt( new \DateTimeImmutable() );

		return $log;
	}

	public static function getSubscribedEvents(): array {
		return [ 'kernel.terminate' => 'onKernelTerminate', ];
	}
}