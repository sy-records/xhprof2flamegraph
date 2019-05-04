<?php

namespace Xhprof2Flamegraph\Command;

use Xhprof2Flamegraph\Profile\Analyzer;
use Xhprof2Flamegraph\Profile\Parser;
use Xhprof2Flamegraph\Xhprof2Flamegraph;

class Command
{
    protected $shortopts = "hf:";
    protected $longopts  = [
        "profile:",
        "metrics:",
        "help",
    ];

    protected $opt = [
        "f",
        "h",
        "help",
        "profile",
        "metrics",
    ];

    protected $metrics = ['ect', 'ewt', 'ecpu', 'emu', 'epmu'];

    public static function main()
    {
        $command = new static();

        $options = $command->parseOptions();

        if (isset($options['profile'])) {
            $data = file_get_contents($options['profile']);
        } else {
            $data = trim(fgets(STDIN));
        }

//        $data = unserialize($data);
        $data = json_decode($data, true);
        // decode error
        if (!$data) {
            throw new \Exception('xhprof profile data error, json_decode error code ' . json_last_error());
        }

        if (!isset($options["metrics"])) {
            $options["metrics"] = 'ewt';
        }
        $parser = new Parser();
        $analyzer = new Analyzer();
        $Xhprof2Flamegraph = new Xhprof2Flamegraph($data, $parser, $analyzer, $options["metrics"]);
        $Xhprof2Flamegraph->show();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function parseOptions()
    {
        $options = getopt($this->shortopts, $this->longopts);

        // options is empty
        if (!$options) {
            $this->showHelp();
            exit();
        }

        $result = [];
        foreach ($options as $option => $value) {
            switch ($option){
                case "help":
                case "h":
                    $this->showHelp();
                    exit();
                    break;
                case "profile":
                case "f":
                    if (!file_exists($value)) {
                        throw new \Exception("profile is not found.");
                    }
                    $result["profile"] = $value;
                    break;
                case "metrics":
                    if (in_array($value, $this->metrics) === false) {
                        throw new \Exception("metrics option is given invalid value. ".$value." is given.");
                    }
                    $result["metrics"] = $value;
                    break;
                default:
                    break;
            }
        }
        return $result;
    }

    protected function showHelp()
    {
        echo <<<HELP
usage: xhprof2flamegraph [-h, --help] [--f, --profile] [--metrics]
options:
    -h, --help      show help
    -f, --profile   file path of xhprof profile data
    --metrics       select target metrics (ect/ewt/ecpu/emu/epmu)

HELP;
    }
}
