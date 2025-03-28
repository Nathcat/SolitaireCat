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
            <div class="row align-center" style="justify-content: space-around;">
                <div id="draw-stack" class="card-stack"></div>
                <div id="revealed-stack" class="card-stack"></div>
                <button style="height: fit-content;" onclick="reset_draw_stack();">Reset draw pile</button>

                <p id="timer">0:00</p>

                <div id="ace-stack-0" class="card-stack"></div>
                <div id="ace-stack-1" class="card-stack"></div>
                <div id="ace-stack-2" class="card-stack"></div>
                <div id="ace-stack-3" class="card-stack"></div>
            </div>

            <div class="row" style="justify-content: space-around;">
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
                new CardStack("stack-0", 0.25),
                new CardStack("stack-1", 0.25),
                new CardStack("stack-2", 0.25),
                new CardStack("stack-3", 0.25),
                new CardStack("stack-4", 0.25),
                new CardStack("stack-5", 0.25),
                new CardStack("stack-6", 0.25)
            ];

            let ace_stacks = [
                new CardStack("ace-stack-0", 0, true),
                new CardStack("ace-stack-1", 0, true),
                new CardStack("ace-stack-2", 0, true),
                new CardStack("ace-stack-3", 0, true),
            ];

            let draw_stack = new CardStack("draw-stack", 0);
            let revealed_stack = new CardStack("revealed-stack", 0);

            let get_stack = function(id) {
                switch (id) {
                    case "draw-stack":
                        return draw_stack;
                    case "revealed-stack":
                        return revealed_stack;
                    case "ace-stack-0":
                        return ace_stacks[0];
                    case "ace-stack-1":
                        return ace_stacks[1];
                    case "ace-stack-2":
                        return ace_stacks[2];
                    case "ace-stack-3":
                        return ace_stacks[3];
                    default:
                        return stacks[parseInt(id.split("-")[1])];
                }
            };

            let deck = shuffle_deck(generate_deck());
            let selected_card;
            let selected_card_stack;

            let check_for_win = function() {
                let win = true;
                stacks.forEach((v) => {
                    v.cards.forEach((c) => {
                        if (!c.revealed) win = false;
                    });
                });

                if (win) {
                    clearInterval(timerID);
                    alert("You have won!");

                    // Update all stacks without click handlers so the game state cannot be changed!
                    stacks.forEach((v) => v.update());
                    ace_stacks.forEach((v) => v.update());
                    draw_stack.update();
                    revealed_stack.update();


                    submit_timer_score(<?php echo $_SESSION["user"]["id"] . ", " . $_SESSION["user"]["username"]; ?>);
                }
            }

            let card_click_handler = function() {
                for (let i = 0; i < stacks.length; i++) {
                    stacks[i].highlighted = false;
                }

                ace_stacks.forEach((v) => v.highlighted = false);

                let stack = get_stack($(this).parent().attr("id"));
                let card = stack.cards[$(this).index()];

                if (!card.revealed) {
                    console.log("Not revealed!");
                    stacks.forEach((v) => v.update(card_click_handler));
                    ace_stacks.forEach((v) => v.update(card_click_handler));
                    return;
                }

                selected_card_stack = stack;

                selected_card = card;

                for (let i = 0; i < stacks.length; i++) {
                    if (stacks[i].cards.length > 0) {
                        if (stacks[i].last().valid_next_card(card)) {
                            stacks[i].highlighted = true;
                        }
                    } else {
                        if (card.num === "K") {
                            stacks[i].highlighted = true;
                        }
                    }
                }

                for (let i = 0; i < ace_stacks.length; i++) {
                    if (ace_stacks[i].cards.length === 0 && selected_card.num === "A") {
                        ace_stacks[i].highlighted = true;
                    } else if (ace_stacks[i].cards.length !== 0) {
                        if (card_name_to_num(selected_card.num) === (card_name_to_num(ace_stacks[i].last().num) + 1) && selected_card.suit === ace_stacks[i].last().suit) {
                            ace_stacks[i].highlighted = true;
                        }
                    }
                }

                stacks.forEach((v) => v.update(card_click_handler));
                ace_stacks.forEach((v) => v.update(card_click_handler));
            };

            let unselect_card = function() {
                selected_card = undefined;
                selected_card_stack = undefined;
            };

            highlighted_card_click_handler = function() {
                let target_stack = get_stack($(this).parent().attr("id"));
                let cards = selected_card_stack.remove(selected_card);
                cards.forEach((v) => {
                    target_stack.append(v);
                });

                if (selected_card_stack.cards.length !== 0) {
                    selected_card_stack.last().revealed = true;
                }

                selected_card_stack.update(card_click_handler);

                unselect_card();

                stacks.forEach((v) => {
                    v.highlighted = false;
                    v.update(card_click_handler);
                });

                ace_stacks.forEach((v) => {
                    v.highlighted = false;
                    v.update(card_click_handler);
                });

                if (timerID === undefined) start_timer();
                check_for_win();
            };

            let reset_draw_stack = function() {
                for (let i = revealed_stack.cards.length - 1; i >= 0; i--) {
                    revealed_stack.last().revealed = false;
                    draw_stack.append(revealed_stack.last());
                    revealed_stack.remove(revealed_stack.last());
                }

                revealed_stack.update(card_click_handler);
                draw_stack.update(draw_card_handler);
            }

            let draw_card_handler = function() {
                let card = draw_stack.last();
                draw_stack.remove(card);
                card.revealed = true;
                revealed_stack.append(card);

                revealed_stack.update(card_click_handler);
                draw_stack.update(draw_card_handler);
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

            while (deck.length !== 0) {
                draw_stack.append(deck.pop());
            }

            draw_stack.update(draw_card_handler);
        </script>
        <?php include("../footer.php"); ?>
    </div>
</body>

</html>