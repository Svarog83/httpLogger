<?php

namespace App\Controller;

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
		$offset    = max( 0, $request->query->getInt( 'offset', 0 ) );
		$paginator = $logRepository->getLogsPaginator( $offset );

		$maxRowPerPage = $this->getParameter( 'max_rows_per_page' );
		$totalRows = count($paginator);

		return $this->render( 'adminHttpLog.html.twig', [ 'logs'     => $paginator,
														  'previous' => $offset - $maxRowPerPage,
														  'totalRows' => $totalRows,
														  'next'     => min( count( $paginator ),
																			 $offset + $maxRowPerPage ), ] );
	}
}
