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
            $res = file_get_contents($options['profile']);
        } else {
			$res = trim(fgets(STDIN));
        }

        // json decode data
        $data = json_decode($res, true);
        // decode error
        if (is_null($data)) {
            // unserialize data again
			$data = unserialize($res);
            if (!is_array($data)) {
                throw new \Exception('xhprof profile data error');
            }
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
            switch ($option) {
                case "help":
                case "h":
                    $this->showHelp();
                    exit();
                    break;
                case "profile":
                case "f":
                    if (!file_exists($value)) {
                        throw new \Exception("xhprof profile is error file");
                    }
                    $result["profile"] = $value;
                    break;
                case "metrics":
                    if (in_array($value, $this->metrics) === false) {
                        throw new \Exception("metrics option is given invalid value. ".$value." is given");
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
         _                               __   ____     __ 
 __  __ | |__    _ __    _ __    ___    / _| |___ \   / _|
 \ \/ / | '_ \  | '_ \  | '__|  / _ \  | |_    __) | | |_ 
  >  <  | | | | | |_) | | |    | (_) | |  _|  / __/  |  _|
 /_/\_\ |_| |_| | .__/  |_|     \___/  |_|   |_____| |_|  
                |_|                                       
                
\e[33mUsage:\e[0m
\e[31m    xhprof2flamegraph [-h, --help] [-f, --profile] [--metrics] \e[0m
\e[33mOptions:\e[0m
\e[36m    -h, --help      show help
    -f, --profile   file path of xhprof profile data
    --metrics       select target metrics (ect/ewt/ecpu/emu/epmu) \e[0m

HELP;
    }
}
