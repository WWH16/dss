<?php

// ADDED: field names in Laravel's default validation messages, so program_id reads as "program" (e.g. "The program field is required.").
return [
    'attributes' => [
        // CHANGED: the program and track fields now post IDs, so the keys are program_id and track_id (were course and track).
        'program_id' => 'program',
        'track_id' => 'specialization track',
    ],
];
