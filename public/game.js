class Game {
    config;
    users = [];
    lastEventId = 0;

    constructor(config) {
        this.config = config;
    }

    init() {
        let _this = this;
        $('#game_submit_button').click($.proxy(this.onWordSendAttempt, this));
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
        this.users = data.users;
        for (let i = 0; i < data.events.length; i++) {
            this.handleGameEvent(data.events[i]);
        }
        if (data.currentUserId === this.config.loggedUserId) {
            this.enableWordInput(true);
        } else {
            this.disableWordInput(data.currentUserId);
        }

        let usersList = $('#joined-users');
        usersList.find('li > span').removeClass('current');
        usersList.find('li#user-id-' + data.currentUserId + ' > span').addClass('current');

        let _this = this;
        setTimeout(function() {
            $.getJSON(_this.config.eventsUrl, {"after": _this.lastEventId}).done($.proxy(_this.onAjaxGetEvents, _this));
        }, 0);
    }

    handleGameEvent(event) {
        if (event.type === 'move') {
            let gameLog = $('#game-log');
            gameLog.prepend("<p>Пользователь <span class=\"user\">" + this.users[event.userId].username + "</span> ввёл слово <span class=\"word\">" + event.word + "</span></p>");
        }
        if (event.type === 'join') {
            let usersList = $('#joined-users');
            let item = usersList.find('li#user-id-' + event.userId);
            if (item.length === 0) {
                item = $('<li>').attr('id', 'user-id-' + event.userId).append($('<span>').text(this.users[event.userId].username));
                usersList.append(item);
            } else {
                usersList.find('li#user-id-' + event.userId).removeClass('leaved');
            }
        }
        if (event.type === 'leave') {
            let usersList = $('#joined-users');
            usersList.find('li#user-id-' + event.userId).addClass('leaved');
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

    disableWordInput(currentUserId) {
        let input = $('#game_text_input');
        input.prop('disabled', true);
        if (currentUserId !== -1) {
            input.val('Ход ' + this.users[currentUserId].username + '...')
        }
        $('#game_submit_button').prop('disabled', true);
    }
}
