<?php

declare(strict_types=1);

namespace App\Controller\Documentation;

use App\Controller\CodeReflectionTrait;
use App\Documentation\SlugGenerator;
use PackageVersions\Versions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;

final class CalendarClassController extends AbstractController implements ControllerWithDataProviderInterface
{
    use CodeReflectionTrait;

    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/docs/calendar/{classSlug}", name="docs_calendar_class")
     */
    public function calendarClass(string $classSlug) : Response
    {
        foreach ($this->codeClassesReflection($this->parameterBag->get('aeon_php_calendar_src')) as $phpClass) {
            if (SlugGenerator::forPHPClass($phpClass) === $classSlug) {
                return $this->render('documentation/class.html.twig', [
                    'class' => $phpClass,
                    'activeSection' => 'calendar',
                    'version' => Versions::getVersion('aeon-php/calendar'),
                ]);
            }
        }

        throw $this->createNotFoundException("Class ". $classSlug . " does not exists");
    }

    public function getControllerClass() : string
    {
        return __CLASS__;
    }

    public function getControllerMethod() : string
    {
        return 'calendarClass';
    }

    public function getArguments() : array
    {
        $arguments = [];
        foreach ($this->codeClassesReflection($this->parameterBag->get('aeon_php_calendar_src')) as $phpClass) {
            $arguments[] = [SlugGenerator::forPHPClass($phpClass)];
        }

        return $arguments;
    }
}
