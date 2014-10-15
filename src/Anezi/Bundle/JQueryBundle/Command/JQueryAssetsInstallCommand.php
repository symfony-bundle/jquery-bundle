<?php

namespace Anezi\Bundle\JQueryBundle\Command;

use Composer\Package\Version\VersionParser;
use Curl\Curl;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use UnexpectedValueException;

class JQueryAssetsInstallCommand extends ContainerAwareCommand
{

    private $minimalSupportedVersion = '1.8.0';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jquery:assets:install')
            ->setDefinition(
                array(
                    new InputArgument(
                        'target',
                        InputArgument::OPTIONAL,
                        'The target directory',
                        'web'
                    ),
                    new InputOption(
                        'jquery-version',
                        'j',
                        InputOption::VALUE_OPTIONAL,
                        'The JQuery version',
                        $this->minimalSupportedVersion
                    ),
                )
            )
            ->setDescription('Installs JQuery bundle web assets under a public web directory')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs JQuery bundle assets into a given
directory (e.g. the web directory).

<info>php %command.full_name% web</info>

The JQuery files will be copied into the web bundles directory.

EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the target directory does not exist or symlink cannot be used
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        $wantedVersion = $input->getOption('jquery-version');

        $vParser = new VersionParser();

        try {
            $jQueryVersionConstraint = $vParser->parseConstraints($wantedVersion);
        } catch (UnexpectedValueException $e) {
            $output->writeln(
                '<error>Error:</error> <info>' .
                $wantedVersion . '</info> is not a valid version constraint.'
            );
            return;
        }

        $minimalVersionConstraint =
            $vParser->parseConstraints('>=' . $this->minimalSupportedVersion);

        if(!$minimalVersionConstraint->matches($jQueryVersionConstraint)) {
            $output->writeln(
                '<error>Error:</error> <info>' .
                $wantedVersion . '</info> is not supported. Minimal version is: ' .
                $this->minimalSupportedVersion
            );
            return;
        }

        $curl = new Curl();
        $curl->get(
            'https://api.github.com' .
            '/repos/jquery/jquery/tags?per_page=100'
        );

        $versionToInstall = $this->minimalSupportedVersion;

        foreach(json_decode($curl->response) as $release) {
            $tag = $release->name;
            if($vParser->parseStability($tag) == 'stable') {
                try {
                    $constraint = $vParser->parseConstraints($tag);
                    if($jQueryVersionConstraint->matches($constraint)) {
                        if(version_compare(
                                $vParser->normalize($tag),
                                $versionToInstall)
                            > 0) {
                            $versionToInstall = $tag;
                        }
                    } else {
                    }
                } catch (UnexpectedValueException $e) {
                }
            }
        }

        /** @var Filesystem $filesystem */
        $filesystem = $this->getContainer()->get('filesystem');

        // Create the bundles directory otherwise symlink will fail.
        $bundlesDir = $targetArg . '/bundles/';
        $filesystem->mkdir($bundlesDir, 0777);

        $output->writeln(
            'Installing JQuery assets.'
        );

        $targetDir  = $bundlesDir . 'jquery-extra';

        $namespaceParts = explode('\\', __NAMESPACE__);
        array_pop($namespaceParts);

        $output->writeln(
            sprintf(
                'Installing assets for <comment>%s</comment> into <comment>%s</comment>',
                implode('\\', $namespaceParts),
                $targetDir
            )
        );

        $filesystem->remove($targetDir);

        $filesystem->mkdir($targetDir, 0777);

        $filesystem->mkdir($targetDir . '/tmp');

        $zip = file_get_contents(
            'https://github.com/jquery/jquery/archive/' . $versionToInstall . '.zip'
        );

        file_put_contents($targetDir . '/tmp/jquery.zip', $zip);

        $zip = new \ZipArchive;

        if ($zip->open($targetDir . '/tmp/jquery.zip') === TRUE) {
            $zip->extractTo($targetDir . '/tmp');
            $zip->close();

            if(file_exists($targetDir . '/tmp/jquery-' . $versionToInstall . '/dist')) {
                $filesystem->mirror(
                    $targetDir . '/tmp/jquery-' . $versionToInstall . '/dist',
                    $targetDir
                );
            } else {
                foreach( array(
                    'jquery.js',
                    'jquery.min.js',
                    'jquery.min.map',
                    'jquery-migrate.js',
                    'jquery-migrate.min.js',
                ) as $file) {
                    if(
                        file_exists(
                            $targetDir .
                            '/tmp/jquery-' .
                            $versionToInstall .
                            '/' . $file)) {
                        $filesystem->copy(
                            $targetDir . '/tmp/jquery-' . $versionToInstall . '/' . $file,
                            $targetDir . '/' . $file
                        );
                    }
                }
            }
            $filesystem->remove($targetDir . '/tmp');
        } else {
            throw new FileException('File Error');
        }
    }
}
