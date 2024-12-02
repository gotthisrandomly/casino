<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Casino Game'; ?></title>
    <style>
        /* Basic CSS for the game */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #1a1a1a;
            color: #fff;
        }
        .game-container {
            max-width: 800px;
            margin: 0 auto;
            background: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
        }
        .slot-machine {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .slot {
            width: 100px;
            height: 100px;
            background: #333;
            border: 2px solid #gold;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
        }
        .controls {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="game-container">
        <?php include($content); ?>
    </div>
</body>
</html>