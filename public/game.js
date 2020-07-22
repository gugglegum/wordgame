class Game {
    config;
    players = [];
    lastEventId = 0;

    constructor(config) {
        this.config = config;
    }

    init() {
        let _this = this;
        $('#game_submit_button').click($.proxy(this.onWordSendAttempt, this));
        // $('#game_text_input').bind('enterKey', $.proxy(this.onWordSendAttempt, this));
        $('#game_text_input').keyup('enterKey', function(e) {
            if (e.keyCode == 13) {
                _this.onWordSendAttempt();
            }
        });

        $.getJSON(this.config.eventsUrl).done($.proxy(this.onAjaxGetEvents, this));
    }

    onWordSendAttempt() {
        let input = $('#game_text_input');
        let word = input.val();
        this.disableWordInput(-1);
        $.post(this.config.submitWordUrl, {"word": word}).done($.proxy(this.onAjaxWordSubmit, this));
    }

    onAjaxWordSubmit(data) {
        if ('error' in data) {
            alert(data.error);
            this.enableWordInput();
        } else {
            let input = $('#game_text_input');
            input.val('');
        }
    }

    onAjaxGetEvents(data) {
        this.players = data.players;
        for (let i = 0; i < data.events.length; i++) {
            this.handleGameEvent(data.events[i]);
        }
        if (data.currentPlayerId === this.config.loggedUserId) {
            this.enableWordInput(true);
        } else {
            this.disableWordInput(data.currentPlayerId);
        }

        let playersList = $('#joined-players');
        playersList.find('li > span').removeClass('current');
        playersList.find('li#player-id-' + data.currentPlayerId + ' > span').addClass('current');

        let _this = this;
        setTimeout(function() {
            $.getJSON(_this.config.eventsUrl, {"after": _this.lastEventId}).done($.proxy(_this.onAjaxGetEvents, _this));
        }, 0);
    }

    handleGameEvent(event) {
        if (event.type === 'move') {
            let gameLog = $('#game-log');
            gameLog.prepend("<p>Пользователь <span class=\"player\">" + this.players[event.playerId].username + "</span> ввёл слово <span class=\"word\">" + event.word + "</span></p>");
        }
        if (event.type === 'join') {
            let playersList = $('#joined-players');
            let item = playersList.find('li#player-id-' + event.playerId);
            if (item.length === 0) {
                item = $('<li>').attr('id', 'player-id-' + event.playerId).append($('<span>').text(this.players[event.playerId].username));
                playersList.append(item);
            } else {
                playersList.find('li#player-id-' + event.playerId).removeClass('leaved');
            }
        }
        if (event.type === 'leave') {
            let playersList = $('#joined-players');
            playersList.find('li#player-id-' + event.playerId).addClass('leaved');
        }
        this.lastEventId = event.id;
    }

    enableWordInput(clear = false) {
        let input = $('#game_text_input');
        input.prop('disabled', false).focus();
        if (clear) {
            input.val('');
        }
        $('#game_submit_button').prop('disabled', false);
    }

    disableWordInput(currentPlayerId) {
        // let text = currentPlayerId !== -1 ? "Ход " + this.players[currentPlayerId].username + '...' : 'Отправка...';
        let input = $('#game_text_input');
        input.prop('disabled', true);
        if (currentPlayerId !== -1) {
            input.val('Ход ' + this.players[currentPlayerId].username + '...')
        }
        $('#game_submit_button').prop('disabled', true);
    }
}
