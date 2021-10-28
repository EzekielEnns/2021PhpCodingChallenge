/*
this class 
  contains ui elements and creates 
  the bridge between data/connection
  and the ui
*/
class Game{
    constructor(ui){
      this.ui = ui;
      this.player = {};
      this.player.action = {'v':1};
    }
    
    //UI METHODS

    //finds where a given index fits
    findPlacement(index){
      let x = 0;
      let y = 0;
      if(index > 5) y = 20;
      else if(index > 2) y = 10;
      //
      if((index-2)%3==0) x = 20;
      else if((index-1)%3==0) x = 10;
      return ({'x':x,'y':y});
    }

    //updates action to match said point
    findIndex(point){
      let index = 0;  //index of array
      if(point.x >= 20)index = 2; 
      else if(point.x >= 10)index = 1; 
      else index = 0;
      
      if(point.y >= 20)index+=6;
      else if(point.y >= 10)index+=3;

      return index;
    }

    updateBoard(asset,index){
      let point = game.findPlacement(index);
      Stage.image(asset)
      .pin({'offsetX':point.x,'offsetY':point.y})
      .appendTo(game.ui.board);
    }

    //Game Logic Methods
    //sets click listner for lobby on board
    LobbyLogic() {
      this.ui.board.off('click')
      this.ui.board.on('click',function (point) {
        let asset =  game.player.action.v == 1?'X':'O';
        let index = game.findIndex(point);
        game.updateBoard(asset,index);
        game.player.action.i = index;
        conn.send(game.player);
        game.player.action.v = game.player.action.value == 1?2:1;
      });
    }
  }