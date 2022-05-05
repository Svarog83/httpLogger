<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {
	#[Route( '/', name: 'index' )]
	public function index(): Response {
		return $this->render( 'index.html.twig');
	}

	#[Route( '/admin/http-log', name: 'adminHttpLog' )]
	public function adminHttpLog(LogRepository $logRepository): Response {
		$logs = $logRepository->findBy([], ['id'=>'DESC']);

		return $this->render( 'adminHttpLog.html.twig' , [
			'logs' => $logs
		]);
	}
}
