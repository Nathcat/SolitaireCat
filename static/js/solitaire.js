const suits = [
    "hearts",
    "clubs",
    "diamonds",
    "spades"
];

var highlighted_card_click_handler;
var timer = 0;
var timerID;

function __inc_timer() {
    timer++;

    $("#timer").text(Math.floor(timer / 60) + ":" + ((timer % 60) < 10 ? ("0" + (timer % 60)) : (timer % 60)));
}

function start_timer() {
    timerID = setInterval(__inc_timer, 1000);
}

function highlight_card() {
    return $("<div class='highlight-card'></div>");
}


function card_num(n) {
    switch (n) {
        case 1: return "A";
        case 11: return "J";
        case 12: return "Q";
        case 13: return "K";
        default: return n;
    }
}

function card_name_to_num(name) {
    switch (name) {
        case "A": return 1;
        case "J": return 11;
        case "Q": return 12;
        case "K": return 13;
        default: return name;
    }
}

class CardStack {
    constructor(id, offset, is_ace_pile) {
        this.id = id;
        this.offset = this.card_height() * offset;
        this.cards = [];
        this.highlighted = false;

        if (is_ace_pile === undefined) {
            this.is_ace_pile = false;
        }
        else {
            this.is_ace_pile = is_ace_pile;
        }

        $(`#${this.id}`).height(this.calc_height());
    }

    card_height() {
        return $(`#${this.id}`).width() * 2;
    }

    append(card) {
        this.cards.push(card);
        this.update();
    }

    remove(card) {
        for (let i = 0; i < this.cards.length; i++) {
            if (this.cards[i].equals(card)) {
                return this.cards.splice(i, this.cards.length - i);
            }
        }
    }

    update(handler) {
        $(`#${this.id}`).html("");

        this.cards.forEach((v, index) => {
            let elem = $(v.toString());
            elem.css({
                top: (index * this.offset) + "px",
                position: "absolute"
            });

            $(`#${this.id}`).append(elem);
        });

        if (handler !== undefined) {
            this.set_card_click_handler(handler);
        }

        if (this.highlighted) $(`#${this.id}`).append(highlight_card().css({
            top: (this.cards.length * this.offset) + "px",
            position: "absolute"
        }).click(highlighted_card_click_handler));

        $(`#${this.id}`).height(this.calc_height());
    }

    pop() {
        let c = this.cards.pop();
        this.update();
        return c;
    }

    calc_height() {
        if (this.cards.length === 0) return this.card_height();

        return this.offset * (this.cards.length - 1) + this.card_height();
    }

    last() {
        return this.cards[this.cards.length - 1];
    }

    set_card_click_handler(handler) {
        $(`#${this.id}`).children().click(handler);
    }
}

class Card {
    constructor(suit, num) {
        this.suit = suit;
        this.num = num;
        this.revealed = false;
    }

    toString() {
        if (this.revealed) return "<div class=\"card " + this.suit + "\"><p class=\"numberL\">" + this.num + "</p><div class=\"suit\"><img></img></div><p class=\"numberR\">" + this.num + "</p></div>"
        else return "<div class=\"card\"><div class=\"suit\"><img src=\"https://cdn.nathcat.net/cloud/116bc634-b69a-11ef-9adc-067048c6a237.png\"></img></div></div>";
    }

    valid_next_card(card) {
        if (this.num === "A") {
            return false;
        }

        let mysuit_index = suits.findIndex((v) => v === this.suit);
        let valid_suit_indices = [(mysuit_index + 1) % suits.length, (mysuit_index + 3) % suits.length];

        if (valid_suit_indices.findIndex((v) => suits[v] === card.suit) === -1) {
            return false;
        }

        if (card_name_to_num(card.num) != (card_name_to_num(this.num) - 1)) {
            return false;
        }

        return true;
    }

    equals(card) {
        if (this.suit !== card.suit) {
            return false;
        }

        if (this.num !== card.num) {
            return false;
        }

        return true;
    }
}

function generate_deck() {
    let d = [];

    for (let s = 0; s < suits.length; s++) {
        for (let c = 1; c < 14; c++) {
            d.push(
                new Card(
                    suits[s],
                    card_num(c)
                )
            )
        }
    }

    return d;
}

function shuffle_deck(deck) {
    let d = [];

    while (deck.length != 0) {
        let i = Math.floor(Math.random() * (deck.length - 1));
        d.push(deck[i]);
        deck.splice(i, 1);
    }

    return d;
}

function submit_timer_score(userID, username) {
    fetch("https://data.nathcat.net/data/get-leaderboard-state.php", {
        method: "POST",
        body: JSON.stringify({
            "leaderboardId": 3,
            "orderBy": "ASC"
        })
    }).then((r) => r.json()).then((r) => {
        if (r.status === "success") {
            r.results.filter((record) => record.username === username);
            if (r.results.length !== 0 && r.results[0].value <= timer) {
                console.log("Already has a better time!");
                return;
            }

            fetch("https://data.nathcat.net/data/edit-leaderboard-record.php", {
                method: "POST",
                body: JSON.stringify({
                    "apiKey": "99658a71fda03267743da380aea25bc20f1af5a1dd7ba52ac9d692b22fc69c2f",
                    "leaderboardId": 2,
                    "user": userID,
                    "value": timer
                })
            }).then((r1) => r1.json()).then((r1) => {
                if (r1.status === "success") console.log("Time submitted to DataCat");
                else console.log("Failed to submit time: " + r1.message);
            });
        }
        else {
            console.log("Failed to get timer leaderboard state: " + r.message)
        }
    });
}

function submit_new_win(userID, username) {
    fetch("https://data.nathcat.net/data/get-leaderboard-state.php", {
        method: "POST",
        body: JSON.stringify({
            "leaderboardId": 3,
            "orderBy": "ASC",
        })
    }).then((r) => r.json()).then((r) => {
        if (r.status === "success") {
            r.results.filter((record) => record.username === username);

            let newValue = r.results.length === 0 ? 1 : parseInt(r.results[0].value) + 1;

            fetch("https://data.nathcat.net/data/edit-leaderboard-record.php", {
                method: "POST",
                body: JSON.stringify({
                    "apiKey": "99658a71fda03267743da380aea25bc20f1af5a1dd7ba52ac9d692b22fc69c2f",
                    "leaderboardId": 3,
                    "user": userID,
                    "value": newValue
                })
            }).then((r1) => r1.json()).then((r1) => {
                if (r1.status === "success") console.log("Win submitted to DataCat");
                else console.log("Failed to submit new win: " + r1.message);
            });
        }
        else {
            console.log("Failed to get total solved leaderboard state: " + r.message)
        }
    });
}