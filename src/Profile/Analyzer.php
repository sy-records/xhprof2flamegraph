<?php

namespace Xhprof2Flamegraph\Profile;

class Analyzer
{
    /**
     * @param  $records
     * @param  $indices
     * @return array
     */
    public function analyze($records, $indices)
    {
        foreach ($records as $record) {
            $record->epmu = $record->pmu;
            $record->ecpu = $record->cpu;
            $record->ewt = $record->wt;
            $record->ect = $record->ct;
            $record->emu = $record->mu;
        }

        $result = [];
        foreach ($records as $record) {
            $children = $this->getChildren($record, $indices);
            foreach ($children as $child) {
                $record->ect -= $child["ct"];
                $record->ewt -= $child["wt"];
                $record->ecpu -= isset($child["cpu"]) ? $child["cpu"] : 0;
                $record->emu -=  isset($child["mu"]) ? $child["mu"] : 0;
                $record->epmu -= isset($child["pmu"]) ? $child["pmu"] : 0;
            }
            $result[] = $record;
        }
        return $result;
    }


    /**
     * @param  Record  $record
     * @param  $indices
     * @return Record[]
     */
    public function getChildren($record, $indices)
    {
        $children = array();
        if (!isset($indices[$record->current_function])) {
            return $children;
        }

        foreach ($indices[$record->current_function] as $name => $values) {
            $children[] = $values;
        }
        return $children;
    }

    /**
     * @param  Record   $record
     * @param  Record[] $records
     * @return Record[]
     */
    public function getParents($record, $records)
    {
        $parents = [];
        if ($record->hasParent() === false) {
            return $parents;
        }

        $parent = $records[$record->parent_function];

        if (isset($records[$record->parent_function])) {
            // fix allowed memory size of
            unset($records[$record->parent_function]);
            $tmp = $this->getParents($parent, $records);
            if (is_array($tmp)) {
                $parents =  array_merge($tmp, [$parent]);
            }
        }
        return $parents;
    }
}
