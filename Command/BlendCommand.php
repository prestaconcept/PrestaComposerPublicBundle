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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Description of BlendCommand
 *
 * @author David Epely
 */
class BlendCommand extends ContainerAwareCommand
{
    protected $bundlePath;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->bundlePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'public');
    }
    protected function configure()
    {
        $this
            ->setName('presta:composer-public')
            ->setDescription('Include library in public folder of PrestaComposerPublicBundle')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force regeneration. Clean old libraries.')
            ->addOption('copy', 'c', InputOption::VALUE_NONE, 'Force the copy of libraries instead of symlink')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $output->writeln(sprintf('Blend public libraries in <comment>Presta\ComposerPublicBundle</comment>'));

        /**
         * @var array of [vendor[] => name] already present
         */
        $blended = array();
        //list library subdirectories
        $finder = Finder::create();
        $finder->directories()
                ->depth('> 0')
                ->depth('< 2')
                ->in($this->bundlePath);

        foreach ($finder as $directory) {
            if ($input->getOption('force')) {
                if ($fs->exists($directory->getPath())) {
                    $fs->remove($directory->getPath());
                }
                continue;
            }

            if (!isset($blended[$directory->getRelativePath()])) {
                $blended[$directory->getRelativePath()] = array();
            }

            $libName = substr($directory->getRelativePathName(), strpos($directory->getRelativePathName(), '/') + 1);

            $blended[$directory->getRelativePath()][$libName] = DIRECTORY_SEPARATOR;

        }

        //get configuration
        $toBlend = array();
        $config = $this->getContainer()->getParameter('presta_composer_public');

        //check target folder
        foreach ($config['blend'] as $key => $params) {
            $vendor = isset($params['vendor']) ? $params['vendor'] : null;
            $name = isset($params['name']) ? $params['name'] : null;
            $path = isset($params['path']) ? $params['path'] : null;

            if (!$vendor && !$name) {
                list($vendor, $name) = spliti('/', $key, 2);
            }

            if (!isset($toBlend[$vendor])) {
                $toBlend[$vendor] = array();
            }

            if (!isset($blended[$vendor]) || (
                    !isset($blended[$vendor][$name])&&
                    !isset($toBlend[$vendor][$name])))
            {
                $toBlend[$vendor][$name] = $path;
            }
        }

        //include library
        $vendorDir = $this->getContainer()->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor';

        foreach ($toBlend as $vendor => $names) {
            foreach ($names as $name => $path) {
                $originPath = realpath($vendorDir . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
                $targetDir = realpath($this->bundlePath) . DIRECTORY_SEPARATOR . $vendor;
                $targetPath = $targetDir . DIRECTORY_SEPARATOR . $name;

                if (!$originPath) {
                    throw new \InvalidArgumentException(sprintf('The origin path for "%s" does not exist : "%s"', "$vendor/$name", $originPath));
                }

                if (!$fs->exists($targetDir)) {
                    $fs->mkdir($targetDir);
                }

                if ($input->getOption('copy') == false && isset($config['symlink']) && $config['symlink']) {
                    $fs->symlink($originPath, $targetPath);
                } else {
                    $fs->copy($originPath, $targetPath);
                }

                $output->writeln(sprintf('The library <info>%s/%s</info> has been added', $vendor, $name));
            }
        }
    }
}
