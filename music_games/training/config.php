<?php

// ------------------------------------------------------------
// Chromatic notes
// ------------------------------------------------------------

$chromaticNotes = [
    'C',
    'C#',
    'D',
    'D#',
    'E',
    'F',
    'F#',
    'G',
    'G#',
    'A',
    'A#',
    'B'
];


// ------------------------------------------------------------
// Major scales
// ------------------------------------------------------------

$majorScales = [

    'C' => [
        'C', 'D', 'E', 'F', 'G', 'A', 'B'
    ],

    'G' => [
        'G', 'A', 'B', 'C', 'D', 'E', 'F#'
    ],

    'D' => [
        'D', 'E', 'F#', 'G', 'A', 'B', 'C#'
    ],

    'A' => [
        'A', 'B', 'C#', 'D', 'E', 'F#', 'G#'
    ],

    'E' => [
        'E', 'F#', 'G#', 'A', 'B', 'C#', 'D#'
    ],

    'B' => [
        'B', 'C#', 'D#', 'E', 'F#', 'G#', 'A#'
    ],

    'F#' => [
        'F#', 'G#', 'A#', 'B', 'C#', 'D#', 'E#'
    ],

    'C#' => [
        'C#', 'D#', 'E#', 'F#', 'G#', 'A#', 'B#'
    ],

    'F' => [
        'F', 'G', 'A', 'Bb', 'C', 'D', 'E'
    ],

    'Bb' => [
        'Bb', 'C', 'D', 'Eb', 'F', 'G', 'A'
    ],

    'Eb' => [
        'Eb', 'F', 'G', 'Ab', 'Bb', 'C', 'D'
    ],

    'Ab' => [
        'Ab', 'Bb', 'C', 'Db', 'Eb', 'F', 'G'
    ]

];


// ------------------------------------------------------------
// MIDI note numbers
// ------------------------------------------------------------

$noteMidi = [

    'C'  => 0,
    'C#' => 1,
    'Db' => 1,

    'D'  => 2,
    'D#' => 3,
    'Eb' => 3,

    'E'=>4, 
    
    'E#'=>5,

    'F'  => 5,
    'F#' => 6,
    'Gb' => 6,

    'G'  => 7,
    'G#' => 8,
    'Ab' => 8,

    'A'  => 9,
    'A#' => 10,
    'Bb' => 10,

    'B'  => 11,

    'Cb' => 11,
    'B#' => 0,

];


// ------------------------------------------------------------
// Convert note + octave to MIDI number
// ------------------------------------------------------------

function noteToMidi($note, $octave)
{
    global $noteMidi;

    if (!isset($noteMidi[$note])) {
        return null;
    }

    return (($octave + 1) * 12) + $noteMidi[$note];
}


// ------------------------------------------------------------
// Calculate interval in semitones
// ------------------------------------------------------------

function calculateSemitones($firstMidi, $secondMidi)
{
    return abs($secondMidi - $firstMidi);
}


// ------------------------------------------------------------
// Get interval name from semitone distance
// ------------------------------------------------------------

function getIntervalName($semitones)
{
    $intervals = [
        0  => 'Unison',
        1  => 'Minor 2nd',
        2  => 'Major 2nd',
        3  => 'Minor 3rd',
        4  => 'Major 3rd',
        5  => 'Perfect 4th',
        6  => 'Tritone',
        7  => 'Perfect 5th',
        8  => 'Minor 6th',
        9  => 'Major 6th',
        10 => 'Minor 7th',
        11 => 'Major 7th',
        12 => 'Octave'
    ];

    return $intervals[$semitones] ?? 'Unknown';
}