<?php

namespace App\Controller;

use App\Entity\Log;
use App\Form\LogFormType;
use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {
	#[Route( '/', name: 'index' )]
	public function index(): Response {
		return $this->render( 'index.html.twig' );
	}

	#[Route( '/admin/http-log', name: 'adminHttpLog' )]
	public function adminHttpLog( Request $request, LogRepository $logRepository ): Response {
		$log = new Log();
		$offset    = max( 0, $request->query->getInt( 'offset', 0 ) );
		$form = $this->createForm(LogFormType::class, $log);
		$form->handleRequest($request);

		$filterByIP = $request->query->get( 'filterByIP', 0 );
		if ($form->isSubmitted() && $form->isValid()) {
			$filterByIP = $form['ip']->getData();
			$offset = 0;
		}

		$filterByIPInt = null;
		if ($filterByIP) {
			$filterByIPInt = ip2long($filterByIP);
			if ($filterByIPInt === FALSE) {
				throw new \RuntimeException( 'Wrong IP provided');
			}
		}

		$paginator = $logRepository->getLogsPaginator( $offset, $filterByIPInt );

		$maxRowPerPage = $this->getParameter( 'max_rows_per_page' );
		$totalRows = count($paginator);

		return $this->render( 'adminHttpLog.html.twig', [ 'logs'     => $paginator,
														  'log_form' => $form->createView(),
														  'filterByIP' => $filterByIP,
														  'previous' => $offset - $maxRowPerPage,
														  'totalRows' => $totalRows,
														  'next'     => min( count( $paginator ),
																			 $offset + $maxRowPerPage ), ] );
	}
}
