const playerElement = document.querySelector('[data-player]');
const scoreElement = document.querySelector('[data-score]');
const sendButton = document.querySelector('[data-send-button]');
const responsePreviewElement = document.querySelector('[data-response-preview]');

const player = generateSpiritName();
const score = Math.round(Math.random() * 1000);

playerElement.textContent = player;
scoreElement.textContent = score.toString();

function generateSpiritName() {

    const firstNames = ["Blackbeard", "Salty", "One-Eyed", "Mad", "Captain", "Peg-Leg", "Red", "Stormy", "Jolly", "Barnacle"];
    const lastNames = ["McScurvy", "Silverhook", "Rumbelly", "Seadog", "Plankwalker", "Bones", "Squidbeard", "Driftwood", "Sharkbait", "Bootstraps"];

    const randomFirstName = firstNames[Math.floor(Math.random() * firstNames.length)];
    const randomLastName = lastNames[Math.floor(Math.random() * lastNames.length)];

    return `${randomFirstName} ${randomLastName}`;
}

sendButton.addEventListener('click', () => {
    fetch(
        'submit-highscore.php',
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                player: player,
                score: score,
            }),
        }
    )
        .then(function (response){
            return response.json();
        })

        .then(function (data){
            console.log(data);
            responsePreviewElement.textContent = JSON.stringify(data,null, 2);
        })

        .catch(function (error){
            console.error(error);
            responsePreviewElement.textContent = JSON.stringify(error, null, 2);
        });
});