<?php

namespace Presta\AnyPublicBlendBundle\Tests\Command;

require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Presta\AnyPublicBlendBundle\Command\BlendCommand;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Description of TestBlendCommand
 *
 * @author dey
 */
class BlendCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $fs = new Filesystem();
        $foobarPath = realpath(__DIR__.DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'bar';
        $fs->mkdir($foobarPath);
    }

    public function testExecute()
    {
        $application = $this->getApplication();
        $application->add(new BlendCommand());

        $command = $application->find('presta:any-public-blend');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--force' => true));

        $this->assertRegExp('/The library foo\/bar has been added/', $commandTester->getDisplay());
    }

    /**
     * @return \AppKernel
     */
    protected function getApplication()
    {
        $kernel = $this->getMockBuilder('\AppKernel')
                ->disableOriginalConstructor()
                ->setMethods(array('getRootDir'))
                ->getMock();
        //fix app path (/Tests/app, not just /app)
        $kernel->expects($this->any())
                ->method('getRootDir')
                ->will($this->returnValue(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..')))
                ;

        $kernel->__construct('test', true);

        $kernel->boot();

        return new Application($kernel);
    }
}
