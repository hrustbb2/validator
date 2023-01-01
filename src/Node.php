<?php

namespace hrustbb2\Validator;

use Respect\Validation\Rules\AbstractRule;

class Node extends AbstractNode {

    public function validate($val, string $key): bool
    {
        $result = true;
        foreach($this->rules as $rule){
            /** @var AbstractRule $rule */
            if(!$rule->validate($val)){
                $result = false;
                $report = $rule->reportError($val, ['value' => $val, 'name' => $key]);
                $this->errors[] = $report->getMessage();
                continue;
            }
        }
        if(!$result){
            return $result;
        }
        foreach($this->nestedNodes as $k=>$node){
            if($k == '*'){
                if(!$node->validate($val, $k)){
                    $result = false;
                }
                continue;
            }
            if(!$node->validate($val[$k] ?? null, $k)){
                $result = false;
            }
        }
        if($result && !$this->nestedNodes){
            $this->cleanData = $val;
        }
        return $result;
    }

    public function getErrors(): array
    {
        $result = $this->errors;
        foreach($this->nestedNodes as $k=>$node){
            if($k == '*'){
                $result = $node->getErrors();
                continue;
            }
            $result[$k] = $node->getErrors();
        }
        return $result;
    }

    public function getCleanData()
    {
        if(!$this->nestedNodes){
            return $this->cleanData;
        }
        $result = [];
        foreach($this->nestedNodes as $k=>$node){
            if($k == '*'){
                $result = $node->getCleanData();
                continue;
            }
            $result[$k] = $node->getCleanData();
        }
        return $result;
    }

}