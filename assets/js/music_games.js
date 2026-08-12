const audioPlayers = document.querySelectorAll(".game-audio");

audioPlayers.forEach(player => {

    player.addEventListener("play", function () {

        audioPlayers.forEach(otherPlayer => {

            if (otherPlayer !== player) {
                otherPlayer.pause();
                otherPlayer.currentTime = 0;
            }

        });

    });

});