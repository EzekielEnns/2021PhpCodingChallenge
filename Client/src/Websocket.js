/* Contains all websocket objects */

class Connection{
    constructor(url,callback){
        this.conn = new WebSocket(url);
        this.conn.onerror = this.onerror;
        this.conn.onclose = this.onclose;
        this.conn.onmessage = this.onmessage;
        this.conn.onopen = callback;
    }

    onclose(event){
        alert('server: "Bye bye 👋"');
        resetPage();
    }

    onmessage(event){
        console.log(event);
        let data = JSON.parse(event.data);
        console.log(data);
        // board,done,won,names
        data.players.forEach(element => {
            //ERROR NAME GETS WRITTEN HERE FIRST
            if(element != game.player.name){
                $('#opponent').text(element.name);
            }
            else{
                game.player.action.v = element.marker;
            }
        });
        //looping through all elements and 
        //updating board 
        game.ui.board.empty();
        data.board.forEach((element,index)=> {
            //todo tween for smooth animations
            let asset = '';
            //getting asset
            switch (element) {
            case 2:
                asset = 'O';
                break;
            case 1:
                asset = 'X';
                break;
            default:
                break;
            }

            //draw based on x and y
            if(asset != ''){
                game.updateBoard(asset,index);
            }
        });
    }
//TODO fix This from breaking 
    send(data){
        let msg = JSON.stringify(data);
        try {
            this.conn.send(msg);
        } catch (error) {
            alert(error);
        }
    }

    onerror(data){
        alert("oops something went wrong 🤦");
        console.log(data);
    }
}