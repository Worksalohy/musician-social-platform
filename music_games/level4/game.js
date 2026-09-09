const pianoKeys = document.querySelectorAll(".key");
const userSequenceDisplay =
    document.getElementById("user-sequence");


// ============================================================
// NOTE FREQUENCIES
// ============================================================

const frequencies = {
    1: 261.63, // C4
    2: 293.66, // D4
    3: 329.63, // E4
    4: 349.23, // F4
    5: 392.00, // G4
    6: 440.00, // A4
    7: 493.88  // B4
};


// ============================================================
// AUDIO
// ============================================================

const audioContext = new (
    window.AudioContext ||
    window.webkitAudioContext
)();


// ============================================================
// USER SEQUENCE
// ============================================================

let userSequence = [];


// ============================================================
// PLAY NOTE
// ============================================================

function playNote(note) {

    const oscillator =
        audioContext.createOscillator();

    const gainNode =
        audioContext.createGain();

    oscillator.type = "sine";

    oscillator.frequency.value =
        frequencies[note];

    gainNode.gain.setValueAtTime(
        0.0001,
        audioContext.currentTime
    );

    gainNode.gain.exponentialRampToValueAtTime(
        0.4,
        audioContext.currentTime + 0.01
    );

    gainNode.gain.exponentialRampToValueAtTime(
        0.0001,
        audioContext.currentTime + 0.8
    );

    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    oscillator.start();

    oscillator.stop(
        audioContext.currentTime + 0.8
    );
}


// ============================================================
// PRESS KEY
// ============================================================

function pressKey(note) {

    if (audioContext.state === "suspended") {
        audioContext.resume();
    }

    playNote(note);

    userSequence.push(note);

    updateSequenceDisplay();

    animateKey(note);
}


// ============================================================
// DISPLAY SEQUENCE
// ============================================================

function updateSequenceDisplay() {

    if (userSequence.length === 0) {

        userSequenceDisplay.textContent = "—";

        return;
    }

    userSequenceDisplay.textContent =
        userSequence.join(" → ");
}


// ============================================================
// KEY ANIMATION
// ============================================================

function animateKey(note) {

    const key = document.querySelector(
        `.key[data-note="${note}"]`
    );

    if (!key) {
        return;
    }

    key.classList.add("active");

    setTimeout(() => {

        key.classList.remove("active");

    }, 150);
}


// ============================================================
// MOUSE INPUT
// ============================================================

pianoKeys.forEach(key => {

    key.addEventListener("click", () => {

        const note = key.dataset.note;

        pressKey(note);
    });

});


// ============================================================
// COMPUTER KEYBOARD
// ============================================================

document.addEventListener("keydown", event => {

    const allowedKeys = [
        "1",
        "2",
        "3",
        "4",
        "5",
        "6",
        "7"
    ];

    if (!allowedKeys.includes(event.key)) {
        return;
    }

    if (event.repeat) {
        return;
    }

    pressKey(event.key);
});


// ============================================================
// CLEAR SEQUENCE
// ============================================================

document
    .getElementById("clear-sequence")
    .addEventListener("click", () => {

        userSequence = [];

        updateSequenceDisplay();
    });


// ============================================================
// TARGET MELODY AUDIO
// ============================================================

const targetAudio = new Audio(
    "/music_games/assets/audio/" + gameAudio
);


// ============================================================
// PLAY MELODY
// ============================================================

document
    .getElementById("play-melody")
    .addEventListener("click", () => {

        targetAudio.pause();

        targetAudio.currentTime = 0;

        targetAudio.play();
    });


// ============================================================
// SUBMIT MELODY
// ============================================================

document
    .getElementById("submit-melody")
    .addEventListener("click", () => {

        if (userSequence.length === 0) {

            alert(
                "Please play the melody first."
            );

            return;
        }


        // Create form
        const form =
            document.createElement("form");

        form.method = "POST";
        form.action = "submit.php";


        // ----------------------------------------------------
        // User sequence
        // ----------------------------------------------------

        userSequence.forEach(note => {

            const input =
                document.createElement("input");

            input.type = "hidden";
            input.name = "user_sequence[]";
            input.value = note;

            form.appendChild(input);
        });


        // ----------------------------------------------------
        // Submit
        // ----------------------------------------------------

        document.body.appendChild(form);

        form.submit();
    });