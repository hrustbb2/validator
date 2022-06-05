# Валидатор пользовательского ввода

По сути это обертка к пакету ```respect/validation```, которая позволяет задать правила валидации способом как в Laravel.

```
use hrustbb2\Validator\Validator;
use hrustbb2\Validator\Node;

$validator = new Validator();
// Задаем правила валидации
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
            // Так можно задать сообщение об
            // ошибке конкретно для данного поля
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
// Проверка
$r = $validator->validate($data);
// Сообщения об ошибках
$errors = $validator->getErrors();
// Чистые данные
$cd = $validator->getCleanData();

```