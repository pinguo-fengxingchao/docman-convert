<?php

/**
 * Created by shellvon.
 *
 * @author    : fengxingchao<fengxingchao@camera360.com>
 * @date      : 2017/8/18
 * @time      : 上午9:42
 * @version   1.0
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace DocMan\Command;

use DocMan\Model\ApiDoc;
use DocMan\Parser\PostmanParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DocManCommand extends Command
{
    /**
     * Configures the current command.
     */
    public function configure()
    {
        $this->setName('docman')
            ->setDescription('postman collection v2.0 to apidoc.js style comment')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'The Postman Collection v2.0 file name')
            ->addArgument('outputFile', InputArgument::OPTIONAL, 'The output file name.', 'STDOUT')
            ->setHelp(' <info>docman</info> written by <options=bold,underscore>fengxingchao@camera360.com</>.');
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collectionFilename = $input->getArgument('inputFile');
        $outputFilename = $input->getArgument('outputFile');
        $content = file_get_contents($collectionFilename);
        $parser = new PostmanParser($content);
        if (!$parser->isParsable()) {
            $output->writeln('Not valid postman collection v2 format.!');
            return 1;
        }
        $apiDoc = new ApiDoc($parser->parse());
        $apiDoc->export($outputFilename);
        $text = sprintf('Postman collection file:<info>%s</info> has been successfully parsed in file <info>%s</info>.',
            $collectionFilename, $outputFilename);

        $output->writeln($text);
        return 0;
    }
}
