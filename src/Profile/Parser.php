<?php

namespace Xhprof2Flamegraph\Profile;

class Parser
{
    public $indices = [];
    public $records = [];

    static $xhprof_profile_keys = ['ct', 'wt', 'cpu', 'mu', 'pmu'];

    const XHPROF_FUNCTION_ARROW = "==>";
    const TOP_LEVEL = "__TOP_LEVEL__";

    /**
     * @param $data
     */
    public function parse($data)
    {
        foreach ($data as $name => $values) {
            $record = static::parseProfileRecord($name, $values);

            if (!isset($this->records[$record->current_function])) {
                $this->records[$record->current_function] = $record;
            } else {
                $this->records[$record->current_function] = $this->sum($this->records[$record->current_function], $record);
            }

            if ($record->hasParent() === false) {
                $parent_function = self::TOP_LEVEL;
            } else {
                $parent_function = $record->parent_function;
            }

            if (!isset($this->indices[$parent_function])) {
                $this->indices[$parent_function] = [];
            }
            $this->indices[$parent_function][$record->current_function] = $values;

        }
    }

    protected static function parseFunctionName($name)
    {
        $parsedName = explode(self::XHPROF_FUNCTION_ARROW, $name);
        if (count($parsedName) === 2) {
            return $parsedName;
        } elseif (count($parsedName) === 1) {
            return array(null, $parsedName[0]);
        }
        throw new \Exception();
    }

    /**
     * @param $name
     * @param $values
     * @return Record
     */
    protected static function parseProfileRecord($name, $values)
    {
        $record = new Record();
        foreach (self::$xhprof_profile_keys as $key) {
            $record->{$key} = isset($values[$key]) ? $values[$key] : 0 ;
        }
        list($parent_function, $current_function) = static::parseFunctionName($name);
        $record->parent_function = $parent_function;
        $record->current_function = $current_function;
        return $record;
    }

    /**
     * @param $record
     * @param Record $new_record
     * @return mixed
     */
    protected function sum(Record $record, Record $new_record)
    {
        foreach (self::$xhprof_profile_keys as $key) {
            $record->{$key} += $new_record->{$key};
        }
        return $record;
    }

    public function getIndices()
    {
        return $this->indices;
    }

    /**
     * @return Record[]
     */
    public function getRecords()
    {
        return $this->records;
    }
}
