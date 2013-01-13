<?php

namespace SMTC\MainBundle\Service;

use CG\Core\ClassUtils;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Config\FileLocatorInterface;

class GitHubLocator
{
    private $repository;
    private $rootDir;
    private $parser;
    private $locator;
    private $branch;

    public function __construct(TemplateNameParserInterface $parser, FileLocatorInterface $locator, $repository, $rootDir)
    {
        $this->parser = $parser;
        $this->locator = $locator;
        $this->repository = $repository;
        $this->rootDir = $rootDir;
        $this->branch = "master";
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function getMethodControllerLink($controllerClass, $methodName)
    {
        $reflectionClass = new \ReflectionClass($controllerClass);
        $method = $reflectionClass->getMethod($methodName);

        $relativeClassDir = str_replace(dirname($this->rootDir)."/", "", $reflectionClass->getFilename());
        $anchor = sprintf("#L%d-%d", $method->getStartline(), $method->getEndline());

        return $this->getBaseFileLink() . "/" . $relativeClassDir . $anchor;
    }

    public function getTemplateLink($templateName)
    {
        $templateDir = $this->locator->locate($this->parser->parse($templateName));
        $templateRelativeDir = str_replace(dirname($this->rootDir)."/", "", $templateDir);

        return $this->getBaseFileLink() . "/" . $templateRelativeDir;
    }

    private function getBaseFileLink()
    {
        return $this->repository . "/" . $this->getBlobDir() . "/" . $this->branch;
    }

    private function getBlobDir()
    {
        return "blob";
    }
}
