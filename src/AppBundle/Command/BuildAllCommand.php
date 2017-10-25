<?php


namespace AppBundle\Command;


use AppBundle\Service\SatisJsonBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class BuildAllCommand extends ContainerAwareCommand
{
    const WEB_DIR = 'web';
    const BUILDS_DIR = 'builds';
    const SATIS_BIN_RELATIVE_PATH = 'vendor/composer/satis/bin/satis';

    protected function configure()
    {
        $this->setName('app:build-all');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $this->getApplication()->getKernel()->getProjectDir();
        $baseBuildDir = $projectDir . DIRECTORY_SEPARATOR . self::WEB_DIR . DIRECTORY_SEPARATOR . self::BUILDS_DIR;

        $satisJsonBuilder = $this->getContainer()->get(SatisJsonBuilder::class);
        $repositories = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Repository')->findAll();
        foreach ($repositories as $repository) {
            $output->write("Building repository <info>{$repository->getId()} ({$repository->getName()})</info>... ");
            $satisJson = tempnam(sys_get_temp_dir(), 'satis-build-') . '.json';
            file_put_contents($satisJson, $satisJsonBuilder->build($repository));
            $outputDir = $baseBuildDir . DIRECTORY_SEPARATOR . $repository->getId();

            if (!is_dir($outputDir)) {
                if (!mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
                    throw new \RuntimeException(sprintf('Cannot create directory "%s"', $outputDir));
                }
            }

            $satisBin = $projectDir . DIRECTORY_SEPARATOR . self::SATIS_BIN_RELATIVE_PATH . '';
            $processBuilder = new ProcessBuilder(['php', $satisBin, 'build', $satisJson, $outputDir]);
            $process = $processBuilder->getProcess();
            $process->setTimeout(300);
            $process->run();

            unlink($satisJson);
            if (!$process->isSuccessful()) {
                $output->writeln("<error>An error occurred:</error>");
                $output->writeln("<error>{$process->getErrorOutput()}</error>");
                continue;
            }

            $htaccessFile = $outputDir . DIRECTORY_SEPARATOR . '.htaccess';
            $htpasswdFile = $outputDir . DIRECTORY_SEPARATOR . '.htpasswd';
            $htaccessContent = <<<HTACCESS
AuthType Basic
AuthName "{$repository->getName()}"
AuthUserFile {$htpasswdFile}
Require valid-user
HTACCESS;
            file_put_contents($htaccessFile, $htaccessContent);
            if (!file_exists($htpasswdFile)) {
                touch($htpasswdFile);
            }
            $htpasswd = new \Htpasswd($htpasswdFile);
            foreach ($repository->getUsers() as $user) {
                if ($htpasswd->userExists($user->getUsername())) {
                    $htpasswd->updateUser($user->getUsername(), $user->getPassword());
                } else {
                    $htpasswd->addUser($user->getUsername(), $user->getPassword());
                }
            }

            $output->writeln("Success!");
        }
    }
}
