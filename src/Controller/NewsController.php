<?php

namespace App\Controller;


use App\Repository\PageRepository;
use App\Service\JournalSettingService;
use App\Service\MarkdownService;
use App\Service\ReviewManager;
use App\Service\PageHierarchyService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NewsController extends AbstractController
{

}
