<html>
<head>
    <title>Chat NodeJS + Socket.io + PHP + Predis</title>
    <style>
    #comments {
        height: 300px;
        overflow: auto;
        width: 210px;
    }
    #comments .comment {
        border: 0px #001 solid;
        margin-bottom: 5px;
        width: 200px;
        padding: 5px;
    }
    #comments .comment .from {}
    #comments .comment .message {
        padding: 5px;
        border-top: 1px #ddd dashed;
    }
    </style>
</head>
<body>
    <form action="post.php" method="post">
        <label for="name">Tu nombre</label><br />
        <input type="name" id="name" name="name" /><br />

        <label for="message">Tu mensaje:</label><br />
        <textarea id="message" name="message"></textarea>
    </form>

    <div id="comments">
        <div class="comment">
            <div class="from">Victor San Martin @ 22:34</div>
            <div class="message">Hola Mundo!</div>
        </div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src='http://127.0.0.1:8888/socket.io/socket.io.js'></script>
    <script type="text/javascript">
    $(document).ready(function () {
        var channel = '<?php echo (!empty($_GET['c']) ? $_GET['c'] : 'default'); ?>';

        var socket = io.connect('http://127.0.0.1:8888');
        socket.on('connect', function () {
            socket.emit('channel', channel);
        });

        socket.on('message', function (json) {
            var m = $.parseJSON(json);
            message = '<div class="comment"> \
                <div class="from">' + m.name + ' @ ' + moment(m.created).format('h:mm') + '</div> \
                <div class="message">' + m.message + '</div> \
            </div>';
            $('#comments').prepend(message);
        });

        $('form').on('submit', function(event) {
            event.preventDefault();
            if ($('#name').val() == '') {
                alert('Ingresa tu nombre');
                $('#name').focus();
                return false;
            }
            if ($('#message').val() == '') {
                alert('Ingresa un mensaje');
                $('#message').focus();
                return false;
            }

            data = $(this).serialize() + '&channel=' + channel;
            $.post($(this).attr('action'), data, function(data) {
                $('#message').val('').focus();
            });
        });
        $('form textarea').keydown(function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $(this).parent('form').submit();
            }
        });
    });
    </script>
</body>
</html>