const suits = [
    "hearts",
    "clubs",
    "diamonds",
    "spades"
];

var highlighted_card_click_handler;

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
    constructor(id, offset) {
        this.id = id;
        this.offset = offset;
        this.cards = [];
        this.highlighted = false;
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
        if (this.cards.length === 0) return 0;

        return (this.offset * (this.cards.length - 1)) + $(`#${this.id}`).children().last().height();
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