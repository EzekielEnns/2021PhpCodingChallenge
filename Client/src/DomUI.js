/* contains all dom ui methods */
/* 
https://api.jquery.com/
    <div id="info">
      <h1 id="user"></h1>
      <h1 id="spacer"></h1>
      <h1 id="opponent"></h1>
    </div>
*/

//sets up name ui with a callback 
function AskForName(callback) {
    $('#name').on('keydown', (e)=> {
        if(e.code == 'Enter'){
            let name = e.currentTarget.value;
            if(name.length > 0){
                $('#spacer').html("/");
                $('#user').text(name);
                $('#opponent').text("Connecting...");
                callback(name);
            }   
        }
    });
}

function restName(callback){
    $('#user').text("");
    $('#opponent').text("");
    $("#spacer").html('<input id="name" type="text" placeholder="Enter Your Name"></input>');
    
    AskForName(callback);
}

function resetPage() {
    game.ui.board.empty();
    restName(function (name) {
        game.player.name = name;
        //TODO ERROR send happens quicker then connection
        conn = new Connection(URL,function () {
          conn.send(game.player);
        }); 
      });
}