<?php

require_once __DIR__ . '/config.php';

// ------------------------------------------------------------
// Difficulty interval ranges
// ------------------------------------------------------------

$difficultyRanges = [

    'beginner' => [
        'allowed' => [
            2, 3, 4, 5, 7, 9, 11
        ],
        'directions' => [
            'ascending'
        ],
        'types' => [
            'melodic'
        ]
    ],

    'intermediate' => [
        'allowed' => [
            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
        ],
        'directions' => [
            'ascending',
            'descending'
        ],
        'types' => [
            'melodic'
        ]
    ],

    'advanced' => [
        'allowed' => [
            0, 1, 2, 3, 4, 5, 6,
            7, 8, 9, 10, 11, 12
        ],
        'directions' => [
            'ascending',
            'descending'
        ],
        'types' => [
            'melodic',
            'harmonic'
        ]
    ]

];

// ------------------------------------------------------------
// Generate first note
// ------------------------------------------------------------

function generateFirstNote($mode, $key, $register)
{
    global $chromaticNotes, $majorScales;

    if ($mode === 'diatonic') {

        if (!isset($majorScales[$key])) {
            die('Invalid key.');
        }

        $scale = $majorScales[$key];

        $note = $scale[array_rand($scale)];

    } else {

        $note = $chromaticNotes[array_rand($chromaticNotes)];
    }

    return [
        'note' => $note,
        'octave' => $register
    ];
}


// ------------------------------------------------------------
// Generate challenge type
// ------------------------------------------------------------

function generateChallengeType($difficulty)
{
    global $difficultyRanges;

    $types = $difficultyRanges[$difficulty]['types'];

    return $types[array_rand($types)];
}


// ------------------------------------------------------------
// Generate challenge direction
// ------------------------------------------------------------

function generateChallengeDirection($difficulty, $type)
{
    global $difficultyRanges;

    // Harmonic intervals do not have a melodic direction.
    if ($type === 'harmonic') {
        return null;
    }

    $directions = $difficultyRanges[$difficulty]['directions'];

    return $directions[array_rand($directions)];
}


// ------------------------------------------------------------
// Generate second note
// ------------------------------------------------------------

function generateSecondNote($firstNote, $mode, $key, $range, $direction, $type)
{
    global $majorScales, $chromaticNotes;

    $firstName = $firstNote['note'];
    $firstOctave = $firstNote['octave'];

        // --------------------------------------------------------
    // Harmonic intervals
    // --------------------------------------------------------

    if ($type === 'harmonic') {

        $firstMidi = noteToMidi(
            $firstName,
            $firstOctave
        );

        $possibleNotes = [];

        $availableNotes = ($mode === 'diatonic')
    ? $majorScales[$key]
    : $chromaticNotes;

        foreach ($availableNotes as $note) {

            for (
                $octave = $firstOctave - 1;
                $octave <= $firstOctave + 1;
                $octave++
            ) {

                $secondMidi = noteToMidi(
                    $note,
                    $octave
                );

                // ------------------------------------------------
                // Respect melodic direction using actual pitch
                // ------------------------------------------------

                if (
                    $direction === 'ascending' &&
                    $secondMidi <= $firstMidi
                ) {
                    continue;
                }

                if (
                    $direction === 'descending' &&
                    $secondMidi >= $firstMidi
                ) {
                    continue;
                }

                if ($secondMidi === $firstMidi) {
                    continue;
                }

                $semitones = calculateSemitones(
                    $firstMidi,
                    $secondMidi
                );

                if (
                    in_array($semitones, $range['allowed']) &&
                    $secondMidi > $firstMidi
                ) {

                    $possibleNotes[] = [
                        'note' => $note,
                        'octave' => $octave,
                        'semitones' => $semitones
                    ];
                }
            }
        }

        if (empty($possibleNotes)) {
            die('No valid harmonic interval found.');
        }

        return $possibleNotes[array_rand($possibleNotes)];
    }

    if ($mode === 'diatonic') {

    if (!isset($majorScales[$key])) {
        die('Invalid key.');
    }

    $scale = $majorScales[$key];

    $firstIndex = array_search($firstName, $scale);

    if ($firstIndex === false) {
        die('First note is not in selected scale.');
    }

    $possibleNotes = [];

    foreach ($scale as $index => $note) {

        // ----------------------------------------------------
        // Determine octave based on direction
        // ----------------------------------------------------

        $octave = $firstOctave;

        if ($direction === 'ascending' && $index <= $firstIndex) {
            $octave++;
        }

        if ($direction === 'descending' && $index >= $firstIndex) {
            $octave--;
        }

        // Skip the first note itself.
        if (
    $note === $firstName &&
    $octave === $firstOctave &&
    !in_array(0, $range['allowed'])
) {
    continue;
}

        $firstMidi = noteToMidi(
            $firstName,
            $firstOctave
        );

        $secondMidi = noteToMidi(
            $note,
            $octave
        );

        $semitones = calculateSemitones(
            $firstMidi,
            $secondMidi
        );

        if (in_array($semitones, $range['allowed'])) {

            $possibleNotes[] = [
                'note' => $note,
                'octave' => $octave,
                'semitones' => $semitones
            ];
        }
    }

    if (empty($possibleNotes)) {
        die('No valid second note found.');
    }

    return $possibleNotes[array_rand($possibleNotes)];
}

    // --------------------------------------------------------
    // Chromatic mode
    // --------------------------------------------------------

    if ($mode === 'chromatic') {

                $firstMidi = noteToMidi(
            $firstName,
            $firstOctave
        );

        $possibleNotes = [];

        foreach ($chromaticNotes as $note) {

            for (
                $octave = $firstOctave - 1;
                $octave <= $firstOctave + 1;
                $octave++
            ) {

                $secondMidi = noteToMidi(
                    $note,
                    $octave
                );

                if ($secondMidi === $firstMidi) {
                    continue;
                }

                // ------------------------------------------------
                // Respect melodic direction
                // ------------------------------------------------

                if (
                    $type === 'melodic' &&
                    $direction === 'ascending' &&
                    $secondMidi <= $firstMidi
                ) {
                    continue;
                }

                if (
                    $type === 'melodic' &&
                    $direction === 'descending' &&
                    $secondMidi >= $firstMidi
                ) {
                    continue;
                }

                $semitones = calculateSemitones(
                    $firstMidi,
                    $secondMidi
                );

                if (in_array($semitones, $range['allowed'])) {

                    $possibleNotes[] = [
                        'note' => $note,
                        'octave' => $octave,
                        'semitones' => $semitones
                    ];
                }
            }
        }

        if (empty($possibleNotes)) {
            die('No valid chromatic second note found.');
        }

        return $possibleNotes[array_rand($possibleNotes)];
}
    die('Invalid mode.');
}

// ------------------------------------------------------------
// Generate complete challenge
// ------------------------------------------------------------

function generateChallenge($mode, $key, $difficulty, $register)
{
    global $difficultyRanges;

    if (!isset($difficultyRanges[$difficulty])) {
        die('Invalid difficulty.');
    }

    $range = $difficultyRanges[$difficulty];

    $firstNote = generateFirstNote(
        $mode,
        $key,
        $register
    );

    $type = generateChallengeType($difficulty);

    $direction = generateChallengeDirection(
        $difficulty,
        $type
    );

    $secondNote = generateSecondNote(
    $firstNote,
    $mode,
    $key,
    $range,
    $direction,
    $type
);

    return [
        'mode' => $mode,
        'key' => $key,
        'difficulty' => $difficulty,

        'first_note' =>
            $firstNote['note'] . $firstNote['octave'],

        'second_note' =>
            $secondNote['note'] . $secondNote['octave'],

        'semitones' =>
            $secondNote['semitones'],

        'interval' =>
            getIntervalName($secondNote['semitones']),

        'direction' => $direction,

        'type' => $type
    ];
}