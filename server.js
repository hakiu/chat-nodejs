var RedisStore = require('socket.io/lib/stores/redis')
  , redis  = require('socket.io/node_modules/redis')
  //, redis       = require('redis')
  //, express     = require('express')
  //, store       = new express.session.MemoryStore()
  //, app         = express(
  //     express.bodyParser(),
  //      express.static(__dirname + '/public'),
  //      express.cookieParser(),
  //      express.session({ secret: 'htuayreve', store: store}))
  , http        = require('http')
  //, server      = http.createServer(app)
  , server      = http.createServer()
  , sio         = require('socket.io');

var subscriber  = []
  , pub    = redis.createClient()
  , sub    = redis.createClient()
  , client = redis.createClient();


server.listen(8888, '127.0.0.1',  function () {
    var addr = server.address();
    console.log('app escuchando en http://' + addr.address + ':' + addr.port);
});

var io = sio.listen(server);
io.configure(function () {
    io.set('log level', 1);
    io.set('store', new RedisStore({
          redis    : redis
        , redisPub : pub
        , redisSub : sub
        , redisClient : client
    }));
});

io.sockets.on('connection', function (socket) {
    socket.on('channel', function (channel_name, fn) {
        socket.join(channel_name);

        if (typeof subscriber[channel_name] === 'undefined') {
            //console.log('join to ' + channel_name);

            subscriber[channel_name]    = redis.createClient();
            subscriber[channel_name].on('message', function (channel, json) {
                io.sockets.in(channel).json.send(json);
            });
            subscriber[channel_name].subscribe(channel_name, function (channel) {
                //subscriber[channel_name].publish(channel, 'Hola');
            });
        }

        //fn('joining to ' + channel_name);
    });
    socket.on('disconnect', function () {

    });
});