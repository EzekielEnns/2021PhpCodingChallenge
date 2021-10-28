/*
holds graphical ui elements 
  making use of Stage framework
initializes business logic 
  sockets, game logic....
*/
const URL = 'ws://localhost:8080'
var game; //out game logic
var conn; //socket connection 

/*
  sets up graphics and runs the app
*/
Stage(function(stage){
  //initializing Stage
  stage.viewbox(50,50).pin('handle',-0.5)
  
  //initializing board
  var board = Stage.image('board')
  .pin('handle',0.5)
  .appendTo(stage);
  

  
  //Initialize game
  game = new Game({'board':board,'stage':stage})
  
  //setup socket after name is gotten
  AskForName(function (name) {
    game.player.name = name;
    //TODO ERROR send happens quicker then connection
    conn = new Connection(URL,function () {
      conn.send(game.player);
    }); 
  });
  game.LobbyLogic();
});

//textures/assets
Stage({
  textures:{
    'X':Stage.canvas(function(ctx){
      var ratio = 20; 
      //TODO add loop here
      this.size(10, 10, ratio);
      ctx.scale(ratio,ratio);
      ctx.moveTo(2, 2);
      ctx.lineTo(8, 8);
      ctx.moveTo(2, 8);
      ctx.lineTo(8, 2);
      ctx.lineWidth = 0.5;
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#000';
      ctx.stroke();
    }),
    'O':Stage.canvas(function(ctx){
      //TODO fix this
      var ratio = 20;
      this.size(10, 10, ratio);
      ctx.scale(ratio, ratio);
      ctx.arc(5, 5, 2.4, 0, 2 * Math.PI);
      ctx.lineWidth = 0.5;
      ctx.strokeStyle = '#000';
      ctx.stroke();
    }),
    'board' : Stage.canvas(function(ctx){
      var res = 40;
      this.size(30, 30, res);
      ctx.scale(res, res);
      
      ctx.moveTo(0, 0);
      ctx.lineTo(30, 0);
      ctx.lineTo(30, 30);
      ctx.lineTo(0, 30);
      ctx.lineTo(0, 0);
      ctx.lineTo(30, 0);

      ctx.moveTo(0, 10);
      ctx.lineTo(30, 10);
      ctx.moveTo(0, 20);
      ctx.lineTo(30, 20);
      ctx.moveTo(10, 0);
      ctx.lineTo(10, 30);
      ctx.moveTo(20, 0);
      ctx.lineTo(20, 30);
      //TODO add a loop here
      

      ctx.lineWidth = 0.2;
      ctx.lineCap = 'round';
      ctx.strokeStyle = 'Black';
      ctx.stroke();
    }),
  }
});


/*
for the comp game mode
//
  var forecast1 = Stage.image('forecast')
                  .pin({'handle':0.5,'offsetY':-20})
                  .appendTo(stage)
  ;
  var forecast2 = Stage.image('forecast')
                  .pin({'handle':0.5,
                        'offsetY':-20,
                        'offsetX':-10,
                      })
                  .appendTo(stage)
  ;
//texture
    'forecast':Stage.canvas(function(ctx){
      var res = 5; //ratio for changing resulution
      //this image
       this.size(8, 8, res); //setting size of texture and resolution
      ctx.scale(res, res);
      //lines for box
      ctx.fillStyle = 'fuchsia'
      ctx.fillRect(0,0,8,8);
      
    }),

references:
      //assets grabbed from here
  https://github.com/shakiba/stage.js/blob/master/example/game-tictactoe/app.js
      //best aka only real doc for this API
  https://www.npmjs.com/package/stage-js 
*/