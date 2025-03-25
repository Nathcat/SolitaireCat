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

            let deck = shuffle_deck(generate_deck());
            let selected_card;
            let selected_card_stack;

            let card_click_handler = function() {
                for (let i = 0; i < stacks.length; i++) {
                    stacks[i].highlighted = false;
                }

                let card = stacks[$(this).parent().index()].cards[$(this).index()];
                if (!card.revealed) {
                    console.log("Not revealed!");
                    stacks.forEach((v) => v.update(card_click_handler));
                    return;
                }

                selected_card_stack = stacks[$(this).parent().index()];
                selected_card = card;

                for (let i = 0; i < stacks.length; i++) {
                    if (stacks[i].cards.length > 0) {
                        if (stacks[i].last().valid_next_card(card)) {
                            stacks[i].highlighted = true;
                        }
                    }
                    else {
                        if (card.num === "K") {
                            stacks[i].highlighted = true;
                        }
                    }
                }

                stacks.forEach((v) => v.update(card_click_handler));
            };

            let unselect_card = function() {
                selected_card = undefined;
                selected_card_stack = undefined;
            }

            highlighted_card_click_handler = function() {
                let target_stack = stacks[$(this).parent().index()];
                let cards = selected_card_stack.remove(selected_card);
                cards.forEach((v) => {
                    target_stack.append(v);
                });

                if (selected_card_stack.cards.length !== 0) {
                    selected_card_stack.last().revealed = true;
                }

                unselect_card();

                stacks.forEach((v) => {
                    v.highlighted = false;
                    v.update(card_click_handler);
                })
            };

            let N = stacks.length;
            for (let i = 0; i < stacks.length; i++) {
                for (let c = 0; c < N; c++) {
                    stacks[i].append(deck.pop());
                }

                stacks[i].last().revealed = true;
                stacks[i].update(card_click_handler);

                N--;
            }
        </script>
        <?php include("../footer.php"); ?>
    </div>
</body>

</html>