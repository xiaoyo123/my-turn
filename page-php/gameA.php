<!DOCTYPE html>
<html>

<head>
    <?php
    session_start();
    if (isset($_SESSION['username'])) 
        $player = $_SESSION['username'];
    else 
        header("location: login.php");
    $db = new PDO('mysql: host=localhost; dbname=account', 'root', '801559');
    $sql = $db->query("SELECT player1, player2 FROM play");
    while($row = $sql->fetch(PDO::FETCH_OBJ) ){
        if ($row->player1 == "") {
            $UP = $db->exec("UPDATE play SET player1='$player'");
            $atk = rand(0, 1);
            $First_Round = "INSERT INTO around (round, HP, atk, card1, card2, card3, card4, card5, damage, defense, persist, invincible, lifesteal, purify, self_persist)
                            VALUE (1, 50, $atk, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
            $db->exec($First_Round);
        }
        else {
            $UP = $db->exec("UPDATE play SET player2='$player'");
            header("location: gameB.php");
        }
    }
    ?>    
    <style>
        body {
            background-image: url("../background/war-6111531_960_7201.jpg");
            background-repeat: no-repeat;
            background-position: 50% 0%;
            background-size: 80%;
            margin: 0px;
            padding: 0px;
            border: none;
            user-select: none;
        }
        .rival {
            width: 10%;
            position: absolute;
            top: 10%;
            right: 10%;
            text-align: center;
        }
        .ourside {
            width: 10%;
            position: absolute;
            bottom: 10%;
            left: 10%;
            text-align: center;
        }
        .card-on-desk {
            width: 50%;
            height: 50%;
            position: absolute;
            bottom: 25%;
            pointer-events: none;
            left: 25%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .hp {
            top: 0%;
            left: 0%;
            width: 100%;
            height: 10%;
            background-color: red;
        }
        .hands {
            width: 50%;
            height: 25%;
            left: 25%;
            position: absolute;
            bottom: 0%;
        }
        .card>img {
            width: 100%;
            height: auto;
            background-color: blue;
            pointer-events: none;
        }
        .card {
            position: relative;
            display: inline-block;
            width: 100px;
            height: 100px;
        }
        .on-desk {
            position: relative;
            display: inline-block;
            width: 50%;
        }
        .on-desk>img {
            width: 30%;
        }
        .moving {
            display: none;
            border: 5px;
            border-style: solid;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0%;
            left: 0%;
            pointer-events: none;
            box-sizing: border-box;
            color: #fff;
        }

        .mycard {
            position: absolute;
            top: 0%;
            left: 0%;
            width: 50%;
            height: 100%;
            z-index: 1;
        }

        .rival-card {
            position: absolute;
            top: 0%;
            right: 0%;
            width: 50%;
            height: 100%;
            z-index: 1;
        }
    </style>
    <script src="http://code.jquery.com/jquery-1.9.0rc1.js"></script>
    <script type="text/javascript">
        var move = false;
        var element;
        const now = [];
        var round, HP, atk;
        //*
        var start = setInterval(function(){Start()}, 7000);
        var run;
        var check_round_end;

        function Check_Round(){
            console.log("start");
            $.ajax({
                type: "POST",
                url: "solve.php",
                dataType: "json",
                data: {
                    rd: round
                },
                success: function(data){
                    console.log(data);
                    if(data[0] == 1){
                        round++;
                        /*這條
                        document.getElementById("mycard").innerHTML = "";
                        //*/
                        document.getElementById("rival-card").innerHTML = "";
                        while(now.length > 0)
                            now.pop();
                        run = setInterval(function(){getCard()}, 5000);

                        for(var i = 0; i < 10; i++){
                            var nowC = document.getElementById("card" + i);
                            if(nowC.innerHTML == ""){
                                nowC.innerHTML = "<img id='"+ data[1] + "' src='" + data[2] + "'>";
                                break;
                            }
                        }
                        document.getElementById("hp").innerHTML = data[3];
                        document.getElementById("rival-hp").innerHTML = data[4];
                    }
                },
                error: function(jqXHR){
                    console.log("fail");
                }
            })
        }

        function init(){
            round = 1;
            HP = 50;
            while(now.length > 0)
                now.pop();
            $.ajax({
                type: "POST",
                url: "start.php",
                dataType: "json",
                success: function(data){
                    console.log(data);
                    atk = data[0];
                    for(var i = 0; i < data.length - 1; i++){
                        var now = document.getElementById("card" + i);
                        now.innerHTML = "<img id='"+ data[i + 1][0] + "' src='" + data[i + 1][1] + "'>";
                    }
                }
            })
        }

        function Start(){
            $.ajax({
                type: "POST",
                url: "check.php",
                dataType: "json",
                success: function(data){
                    if(data == 1){
                        // console.log("stop");
                        clearInterval(start);
                        init();
                        run = setInterval(function(){getCard()}, 5000);
                    }
                }
            })
        }//*/
        // var run = setInterval(function(){getCard()}, 5000);

        function getCard(){
            $.ajax({
                type: "POST",
                url: "getCardA.php",
                dataType: "json",
                data: {
                    rd: round
                },
                success: function(data){
                    var rival = document.getElementById("rival-card");
                    for(var i = 0; i < data.length; i++){
                        rival.innerHTML += "<div class='on-desk'><img id='"+ data[i][0] + "' src='" + data[i][1] + "'></div>";
                    }
                    if(data.length > 0){
                        clearInterval(run);    
                        check_round_end = setInterval(function(){Check_Round()}, 10000);
                    }
                },
                error: function(){
                    console.log("failed");
                }
            })
        }
        $(function () {
            var _x, _y;
            $(".card").mousedown(function (e) {
                element = $(this);
                _x = e.pageX - parseInt($(this).css("left"));
                _y = e.pageY - parseInt($(this).css("top"));
                move = true;
            })
            $(document).mousemove(function (e) {
                if (move) {
                    var x = e.pageX - _x;
                    var y = e.pageY - _y;
                    $(element).css({ "top": y, "left": x });
                    $(".moving").css({ display: "block" });
                }
            }).mouseup(function () {
                console.log("click up", move);
                $(".moving").css({ display: "none" });
                //如果再桌子上
                var desk_x = $(".card-on-desk").offset().left;
                var desk_y = $(".card-on-desk").offset().top;
                var desk_w = $(".card-on-desk").width();
                var desk_h = $(".card-on-desk").height();
                if (element.offset().left > desk_x && element.offset().left < desk_x + desk_w && element.offset().top > desk_y && element.offset().top < desk_y + desk_h && move) {
                    now.push(parseInt(element.children("img").attr("id")));
                    //把卡片放到桌子上 並疊在最上面
                    element.remove();
                    $(".mycard").append(element);
                    var card_x = ((Math.random() * 1000) % (50));
                    var card_y = ((Math.random() * 1000) % (10) + element.height() / 4);
                    var rotate = ((Math.random() * 1000) % (30) - 15);
                    //更改卡片類別
                    element.attr("class", "on-desk");
                    element.css({ "top": card_y, "left": card_x, "z-index": 1/*, "transform": "rotate(" + rotate + "deg)"*/ });
                }
                else if (move) {
                    element.css({ "top": 0, "left": 0 });
                }
                move = false;
            })
            $(".card").dblclick(function () {
                var element = $(this);
                now.push(parseInt(element.children("img").attr("id")));
                element.remove();
                $(".mycard").append(element);
                var card_x = ((Math.random() * 1000) % (50));
                var card_y = ((Math.random() * 1000) % (10) + element.height() / 4);
                var rotate = ((Math.random() * 1000) % (30) - 15);
                //更改卡片類別
                element.attr("class", "on-desk");
                element.css({ "top": card_y, "left": card_x, "z-index": 1/*, "transform": "rotate(" + rotate + "deg)" */});
            });
        })
        function post(){
            $.ajax({
                type: "POST",
                url: "playA.php",
                dataType: "json",
                data: {
                    id: now,
                    rd: round,
                    HP: HP, 
                    atk: atk
                },  
                success: function(data){
                    // console.log(data);
                    // console.log("sus");
                },
                error: function(jqXHR){
                    // console.log("failed");
                }
            })
        }
        
    </script>
</head>

<body>

    <div class="rival" id="rival">
        <img src="">
        <div class="hp" id="rival-hp" name="rival-hp">50</div>
        <div class="rival-name" id="rival-name" name="rival-name">name</div>
    </div>
    <div class="ourside" id="ourside" name="ourside">
        <div class="name" id="name" name="name">name</div>
        <div class="hp" id="hp" name="hp">50</div>
        <img src="">
    </div>
    <div class="card-on-desk" id="card-on-desk" name="card-on-desk">
        <div class="rival-card" id="rival-card" name="rival-card"></div>
        <!-- 牌的顯示太大了 -->
        <div class="mycard" id="mycard" name="mycard"></div>
        <div class="moving">移動到此處按下左鍵</div>
    </div>
    <div class="hands" id="hands" name="hands">
        <div class="card" id="card0"></div>
        <div class="card" id="card1"></div>
        <div class="card" id="card2"></div>
        <div class="card" id="card3"></div>
        <div class="card" id="card4"></div>
        <div class="card" id="card5"></div>
        <div class="card" id="card6"></div>
        <div class="card" id="card7"></div>
        <div class="card" id="card8"></div>
        <div class="card" id="card9"></div>
    </div>
    <input type="button" value="出牌" onclick="post()"></button>
</body>

</html>