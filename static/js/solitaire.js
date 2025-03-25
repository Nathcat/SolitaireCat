function highlight_card() {
    return $("<div class='highlight-card'></div>");
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

    update() {
        $(`#${this.id}`).html("");

        this.cards.forEach((v, index) => {
            let elem = $(v.toString());
            elem.css({
                top: (index * this.offset) + "px",
                position: "absolute"
            });

            $(`#${this.id}`).append(elem);
        });

        if (this.highlighted) $(`#${this.id}`).append(highlight_card().css({
            top: (this.cards.length * this.offset) + "px",
            position: "absolute"
        }));

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
}

class Card {
    constructor(suit, num) {
        this.suit = suit;
        this.num = num;
    }

    toString() {
        return "<div class=\"card " + this.suit + "\"><p class=\"numberL\">" + this.num + "</p><div class=\"suit\"><img></img></div><p class=\"numberR\">" + this.num + "</p></div>"
    }
}

const suits = [
    "hearts",
    "clubs",
    "diamonds",
    "spades"
];

function card_num(n) {
    switch (n) {
        case 1: return "A";
        case 11: return "J";
        case 12: return "Q";
        case 13: return "K";
        default: return n;
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