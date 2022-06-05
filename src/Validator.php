<?php

namespace hrustbb2\Validator;

class Validator {

    protected Node $node;

    public function parseRules(array $rules, array $messages = []): void
    {
        $this->node = new Node();
        foreach($rules as $key=>$val){
            $this->node->parse($key, $val, $messages);
        }
    }

    public function validate(array $data): bool
    {
        return $this->node->validate($data);
    }

    public function getCleanData(): array
    {
        return $this->node->getCleanData();
    }

    public function getErrors(): array
    {
        return $this->node->getErrors();
    }

}