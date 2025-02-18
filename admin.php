<?php

$config = require 'config.php';
require 'api.php';

//if not authenticated, ask to log in
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Highscore API Connection"');
    http_response_code(401);
    echo 'Unauthorized access';
    exit;
}

// Validate credentials
if (
    $_SERVER['PHP_AUTH_USER'] !== $config['admin']['username'] ||
    $_SERVER['PHP_AUTH_PW'] !== $config['admin']['password']
) {
    http_response_code(401);
    echo 'Unauthorized access';
    exit;
}

// Handel delete post actions
if ($_POST && isset($_POST['delete'], $_POST['game-id'])) {
    $gameId = $_POST['game-id'];

    $response = apiDelete("games/{$gameId}");
    header("location: admin.php");
    exit;
}

// Handel create post actions
if ($_POST && isset($_POST['save'], $_POST['title'])) {
    $title = $_POST['title'];
    $response = apiPost('games', [
        'title' => $title,
    ]);
    header("location: admin.php");
    exit;
}

// Fetch all games
$response = apiGet('games');
$games = $response['data'];

foreach ($games as $index => $game) {
    $response = apiGet("games/{$game['id']}/highscores");
    $highscores = $response['data'];

    usort($highscores, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    $games[$index]['highscores'] = $highscores;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Highscore API Connection | Admin area</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="logout">
        <a data-button-logout href="#">Logout</a>
    </div>

    <div class="container">
        <div class="game-new">
            <div class="game titel">
                <input form="new-game-form" type="text" name="title" placeholder="Game title...">
            </div>
            <div class="game-actions">
                <form id="new-game-form" method="post" >
                    <button type="submit" name="save">
                        Save
                    </button>
                </form>
            </div>
        </div>
        <?php foreach ($games as $game): ?>
        <div class="game-listing">
            <div class="game-info">
                <div class="game-id">
                    <?php echo $game['id']; ?>
                </div>
                <div class="game-titel">
                    <?php echo $game['title']; ?>
                </div>
                <div class="game-actions">
                    <form method="post">
                        <input type="hidden" id="game-id" name="game-id" value="<?php echo $game['id']; ?>">
                        <button type="submit" name="delete">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="highscore-info">
                <?php foreach ($game['highscores'] as $highscore): ?>
                    <div class="highscore">
                        <div class="highscore-score">
                            <?php echo $highscore['score'] ?>
                        </div>
                        <div class="highscore-player">
                            <?php echo $highscore['player'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        const buttonLogout = document.querySelector('[data-button-logout]');

        buttonLogout.addEventListener('click', (event) => {
            event.preventDefault();
            fetch('logout.php')
                .then(function (respnse) {
                    console.log('Susessfully logged out', respnse);
                    if (respnse.status === 401) {
                        console.log('Successfully logged out', respnse);
                        window.location.reload();
                    } else {
                        console.log('Failed to log out', respnse);
                    }
                });
        });
    </script>
</body>
</html>
