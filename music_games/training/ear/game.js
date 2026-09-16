const playButton = document.getElementById("play-button");
const answerButtons = document.querySelectorAll(".interval-answer");
const answerFeedback = document.getElementById("answer-feedback");


// ------------------------------------------------------------
// Note frequencies
// ------------------------------------------------------------

const noteFrequencies = {
    C: 261.63,
    "C#": 277.18,
    Db: 277.18,
    D: 293.66,
    "D#": 311.13,
    Eb: 311.13,
    E: 329.63,
    F: 349.23,
    "F#": 369.99,
    Gb: 369.99,
    G: 392.00,
    "G#": 415.30,
    Ab: 415.30,
    A: 440.00,
    "A#": 466.16,
    Bb: 466.16,
    B: 493.88
};


// ------------------------------------------------------------
// Convert note name to frequency
// ------------------------------------------------------------

function noteToFrequency(note) {

    const match = note.match(/^([A-G](?:#|b)?)(\d)$/);

    if (!match) {
        return null;
    }

    const noteName = match[1];
    const octave = Number(match[2]);

    const baseFrequency = noteFrequencies[noteName];

    if (!baseFrequency) {
        return null;
    }

    return baseFrequency * Math.pow(2, octave - 4);
}


// ------------------------------------------------------------
// Play one note
// ------------------------------------------------------------

function playNote(audioContext, note, startTime, duration) {

    const frequency = noteToFrequency(note);

    if (!frequency) {
        return;
    }

    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();

    oscillator.type = "sine";
    oscillator.frequency.value = frequency;

    gainNode.gain.setValueAtTime(0, startTime);
    gainNode.gain.linearRampToValueAtTime(0.3, startTime + 0.02);
    gainNode.gain.linearRampToValueAtTime(0, startTime + duration);

    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    oscillator.start(startTime);
    oscillator.stop(startTime + duration);
}


// ------------------------------------------------------------
// Play challenge
// ------------------------------------------------------------

function playChallenge() {

    const firstNote = playButton.dataset.firstNote;
    const secondNote = playButton.dataset.secondNote;
    const type = playButton.dataset.type;

    const audioContext = new AudioContext();

    const now = audioContext.currentTime;

    if (type === "harmonic") {

        playNote(audioContext, firstNote, now, 1);

        playNote(audioContext, secondNote, now, 1);

    } else {

        playNote(audioContext, firstNote, now, 0.8);

        playNote(audioContext, secondNote, now + 1, 0.8);
    }
}


// ------------------------------------------------------------
// Play button
// ------------------------------------------------------------

playButton.addEventListener("click", playChallenge);


answerButtons.forEach((button) => {

    button.addEventListener("click", () => {

        const selectedInterval = Number(button.dataset.interval);
        const correctInterval = Number(playButton.dataset.semitones);

        if (selectedInterval === correctInterval) {
            answerFeedback.textContent = "Correct!";
            answerFeedback.className = "answer-feedback correct";
        } else {
            answerFeedback.textContent = "Incorrect!";
            answerFeedback.className = "answer-feedback incorrect";
        }
    });

});