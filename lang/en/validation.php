<?php

// ADDED: field names in Laravel's default validation messages, so the users.course column reads as "program" (e.g. "The program field is required.").
return [
    'attributes' => [
        'course' => 'program',
        'track' => 'specialization track',
    ],
];
