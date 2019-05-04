<?php

namespace Xhprof2Flamegraph;

use Xhprof2Flamegraph\Profile\Analyzer;
use Xhprof2Flamegraph\Profile\Parser;
use Xhprof2Flamegraph\Profile\Record;

class Xhprof2Flamegraph
{
    public $data;

    /**
     * @var Parser
     */
    public $parser;

    /**
     * @var Analyzer
     */
    public $analyzer;

    /**
     *  metrics
     *
     * @var string
     */
    public $metrics;

    /**
     * @param Parser $parser
     * @param Analyzer $analyzer
     */
    public function __construct($data, Parser $parser, Analyzer $analyzer, $metrics)
    {
        $this->data = $data;
        $this->parser = $parser;
        $this->analyzer = $analyzer;
        $this->metrics = $metrics;
    }

    public function show()
    {
        $this->parser->parse($this->data);
        $records = $this->parser->getRecords();

        $this->analyzer->analyze($records, $this->parser->getIndices());

        foreach ($records as $record){
            printf("%s %d" . PHP_EOL, $this->getFullEntryName($record), (int)$record->{$this->metrics});
        }
    }

    /**
     * @param Record $record
     * @return string
     */
    protected function getFullEntryName(Record $record)
    {
        $entry = "";
        $parents = $this->analyzer->getParents($record,  $this->parser->getRecords());
        foreach ($parents as $parent){
            $entry .= $parent->current_function . ";";
        }
        $entry .= $record->current_function;
        return $entry;
    }
}
