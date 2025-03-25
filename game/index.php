<!DOCTYPE html>
<html>

<head>
    <title>SolitaireCat</title>

    <link rel="stylesheet" href="https://nathcat.net/static/css/new-common.css">
    <link rel="stylesheet" href="/static/css/game.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/static/js/solitaire.js"></script>
</head>

<body>
    <div id="page-content" class="content">
        <?php include("../header.php"); ?>

        <div class="main">
            <div class="row" style="justify-content: space-around">
                <div id="stack-0" class="card-stack"></div>
                <div id="stack-1" class="card-stack"></div>
                <div id="stack-2" class="card-stack"></div>
                <div id="stack-3" class="card-stack"></div>
                <div id="stack-4" class="card-stack"></div>
                <div id="stack-5" class="card-stack"></div>
                <div id="stack-6" class="card-stack"></div>
            </div>
        </div>

        <script>
            let stacks = [
                new CardStack("stack-0", 75),
                new CardStack("stack-1", 75),
                new CardStack("stack-2", 75),
                new CardStack("stack-3", 75),
                new CardStack("stack-4", 75),
                new CardStack("stack-5", 75),
                new CardStack("stack-6", 75)
            ];

            let deck = generate_deck();

            let N = stacks.length;
            let di = 0;
            for (let i = 0; i < stacks.length; i++) {
                stacks[i].highlighted = true;
                
                for (let c = 0; c < N; c++) {
                    stacks[i].append(deck[di++]);
                }

                N--;
            }
        </script>
        <?php include("../footer.php"); ?>
    </div>
</body>

</html>