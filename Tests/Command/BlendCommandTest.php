<?php
/**
 * This file is part of the PrestaComposerPublicBundle
 *
 * (c) PrestaConcept <www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\ComposerPublicBundle\Tests\Command;

require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Presta\ComposerPublicBundle\Command\BlendCommand;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Description of TestBlendCommand
 *
 * @author dey
 */
class BlendCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Filesystem
     */
    protected $fs;

    /**
     * @var array used tests paths
     */
    protected $paths;

    public function setUp()
    {
        $this->fs = new Filesystem();

        $this->paths['foobar'] = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
        $this->fs->mkdir($this->paths['foobar']);
        $this->fs->touch($this->paths['foobar'] . DIRECTORY_SEPARATOR . 'fooBar.file');

        $this->paths['fooinlinebar'] = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'inlineBar';
        $this->fs->mkdir($this->paths['fooinlinebar']);
        $this->fs->touch($this->paths['fooinlinebar'] . DIRECTORY_SEPARATOR . 'fooInlineBar.file');

        $this->paths['bundle'] = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources') . DIRECTORY_SEPARATOR . 'public';
    }

    public function testExecute()
    {
        $application = $this->getApplication();
        $application->add(new BlendCommand());

        $command = $application->find('presta:composer-public');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/The library foo\/bar has been added/', $commandTester->getDisplay());
        $this->assertRegExp('/The library foo\/inlineBar has been added/', $commandTester->getDisplay());

        $this->assertFileExists($this->paths['bundle'] . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'fooBar.file');
        $this->assertFileExists($this->paths['bundle'] . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'inlineBar' . DIRECTORY_SEPARATOR . 'fooInlineBar.file');
    }

    public function testUpdate()
    {
        $this->fs->remove($this->paths['foobar'] . DIRECTORY_SEPARATOR . 'fooBar.file');

        $application = $this->getApplication();
        $application->add(new BlendCommand());

        $command = $application->find('presta:composer-public');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertFileNotExists($this->paths['bundle'] . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'fooBar.file');
        $this->assertFileExists($this->paths['bundle'] . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'inlineBar' . DIRECTORY_SEPARATOR . 'fooInlineBar.file');
    }

    /**
     * @return \AppKernel
     */
    protected function getApplication()
    {
        $kernel = $this->getMockBuilder('Presta\ComposerPublicBundle\AppKernel')
                ->disableOriginalConstructor()
                ->setMethods(array('getRootDir'))
                ->getMock();
        //fix app path (/Tests/app, not just /app)
        $kernel->expects($this->any())
                ->method('getRootDir')
                ->will($this->returnValue(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..')));

        $kernel->__construct('test', true);

        $kernel->boot();

        return new Application($kernel);
    }
}
