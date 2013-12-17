<?php
/**
 * This file is part of the PrestaComposerPublicBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\ComposerPublicBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Description of BlendCommand
 *
 * @author David Epely
 */
class BlendCommand extends ContainerAwareCommand
{
    /**
     * @var string target location for bundles
     */
    protected $bundlePath;
    
    /**
     * @var array bundle configuration
     */
    protected $config;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->bundlePath   = realpath($this->getContainer()->get('kernel')->getBundle('PrestaComposerPublicBundle')->getPath() . DIRECTORY_SEPARATOR  . 'Resources' . DIRECTORY_SEPARATOR . 'public');
        $this->config       = $this->getContainer()->getParameter('presta_composer_public');
    }

    protected function configure()
    {
        $this
            ->setName('presta:composer-public')
            ->setDescription('Include library in public folder of PrestaComposerPublicBundle');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $output->writeln('Blend public libraries in <comment>Presta\ComposerPublicBundle</comment>');

        $toBlend = array();

        //check target folder
        foreach ($this->config['blend'] as $key => $params) {
            $vendor = isset($params['vendor']) ? $params['vendor'] : null;
            $name = isset($params['name']) ? $params['name'] : null;
            $path = isset($params['path']) ? $params['path'] : null;

            if (!$vendor && !$name) {
                list($vendor, $name) = explode('/', $key, 2);
            }

            if (!isset($toBlend[$vendor][$name])) {
                $toBlend[$vendor][$name] = $path;
            }
        }

        //include library
        $vendorDir = $this->getContainer()->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor';

        foreach ($toBlend as $vendor => $names) {
            foreach ($names as $name => $path) {
                $originPath = realpath($vendorDir . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
                $targetDir  = realpath($this->bundlePath) . DIRECTORY_SEPARATOR . $vendor;
                $targetPath = $targetDir . DIRECTORY_SEPARATOR . $name;

                if (!$originPath) {
                    throw new \InvalidArgumentException(sprintf('The origin path for "%s" does not exist : "%s"', "$vendor/$name", $originPath));
                }

                if (!$fs->exists($targetDir)) {
                    $fs->mkdir($targetDir);
                }

                if (isset($this->config['symlink']) && $this->config['symlink']) {
                    $fs->symlink($originPath, $targetPath);
                } else {
                    $fs->mirror($originPath, $targetPath, null, array('delete' => true, 'override' => true));
                }

                $output->writeln(sprintf('The library <info>%s/%s</info> has been added', $vendor, $name));
            }
        }
    }
}
