<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use hrustbb2\Validator\Validator;
use hrustbb2\Validator\Node;


class ValidatorTest extends TestCase {

    public function test1()
    {
        $validator = new Validator();
        $rules = [
            'name' => [
                Node::ARRAY_TYPE,
            ],
            'name.*.first' => [
                Node::LENGTH => [
                    'min' => 3,
                    'max' => 5,
                ],
            ],
            'name.*.two' => [
                Node::LENGTH => [
                    'min' => 3,
                    'max' => 5,
                    'message' => 'Минимум 3, максимум 4',
                ],
            ]
        ];
        $messages = [
            Node::LENGTH => '{{name}} {{value}}',
        ];
        $validator->parseRules($rules, $messages);
        $data = [
            'name' => [
                [
                    'first' => 'sdfs',
                    'two' => 'sfsdfsdfs',
                ]
            ]
        ];
        $r = $validator->validate($data);
        $this->assertFalse($r);
        $errors = $validator->getErrors();
        $this->assertEquals('Минимум 3, максимум 4', $errors['name'][0]['two'][0]);
        $cd = $validator->getCleanData();
        $this->assertEquals('sdfs', $cd['name'][0]['first']);
    }

    public function test2()
    {
        $validator = new Validator();
        $rules = [
            'name.*' => [
                Node::LENGTH => [
                    'min' => 3,
                    'max' => 5,
                ],
            ],
        ];
        $validator->parseRules($rules);
        $data = [
            'name' => [
                'qwe',
                'asd',
                'zxc',
            ],
        ];
        $r = $validator->validate($data);
        $this->assertTrue($r);
        $d = $validator->getCleanData();
        $this->assertEquals('asd', $d['name'][1]);
        $e = $validator->getErrors();
        $this->assertEquals([], $e['name']);
    }

    public function test3()
    {
        $validator = new Validator();
        $rules = [
            'name' => [
                Node::LENGTH => [
                    'min' => 3,
                    'max' => 5,
                ],
            ]
        ];
        $validator->parseRules($rules);
        $data = [
            'name' => 'qwe',
        ];
        $r = $validator->validate($data);
        $this->assertTrue($r);
        $d = $validator->getCleanData();
        $this->assertEquals('qwe', $d['name']);
        $e = $validator->getErrors();
        $this->assertEquals([], $e['name']);
    }

    public function test4()
    {
        $validator = new Validator();
        $rules = [
            'name.*.first.*' => [
                Node::LENGTH => [
                    'min' => 3,
                    'max' => 5,
                ],
            ]
        ];
        $validator->parseRules($rules);
        $data = [
            'name' => [
                [
                    'first' => [
                        'qwe',
                        'asd',
                        'zxc',
                    ],
                ],
            ],
        ];
        $r = $validator->validate($data);
        $this->assertTrue($r);
        $d = $validator->getCleanData();
        $this->assertEquals('qwe', $d['name'][0]['first'][0]);
        $e = $validator->getErrors();
        $this->assertEquals([], $e['name']);
    }

}