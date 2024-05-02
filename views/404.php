<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404</title>

        <style>

            * {
                margin: 0;
                padding: 0;
            }

            body {
                width: 100%;
                height: 100vh;

                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

        </style>

    </head>
    <body>
        <h1>404</h1>
        <h2>Unknown page: <?php echo $_SERVER["REQUEST_URI"] ?></h2>
    </body>
</html>