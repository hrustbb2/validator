<?php

namespace hrustbb2\Validator;

class NodesArray extends AbstractNode {

    public function validate($values, string $key): bool
    {
        $result = true;
        foreach($values as $k=>$val){
            if(!$this->validateItem($val, $k)){
                $result = false;
            }
        }
        return $result;
    }

    protected function validateItem($val, string $key): bool
    {
        $result = true;
        foreach($this->rules as $rule){
            /** @var AbstractRule $rule */
            if(!$rule->validate($val)){
                $result = false;
                $report = $rule->reportError($val, ['value' => $val, 'name' => $key]);
                $this->errors[$key][] = $report->getMessage();
                continue;
            }
        }
        if(!$result){
            return $result;
        }
        foreach($this->nestedNodes as $k=>$node){
            $isOk = true;
            if($k == '*'){
                if(!$node->validate($val, $k)){
                    $result = false;
                    $this->errors[$key][$k] = $node->getErrors();
                    $isOk = false;
                }
                continue;
            }
            if(!$node->validate($val[$k] ?? null, $k)){
                $result = false;
                $this->errors[$key][$k] = $node->getErrors();
                $isOk = false;
            }
            if($isOk){
                $this->cleanData[$key][$k] = $node->getCleanData();
            }
        }
        if($result && !$this->nestedNodes){
            $this->cleanData[$key] = $val;
        }
        return $result;
    }

    public function getErrors(): array
    {
        $result = $this->errors;
        // foreach($this->nestedNodes as $k=>$node){
        //     $result[$k] = $node->getErrors();
        // }
        return $result;
    }

    public function getCleanData()
    {
        // if(!$this->nestedNodes){
        //     return $this->cleanData;
        // }
        // $result = [];
        // foreach($this->nestedNodes as $k=>$node){
        //     $result[$k] = $node->getCleanData();
        // }
        // return $result;
        return $this->cleanData;
    }

}