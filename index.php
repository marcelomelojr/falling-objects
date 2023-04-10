<?php
require_once("backend/autoload.php");

$db = new DbConfig();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jogo caçada</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <script src="frontend/js/camera_utils.js" crossorigin="anonymous"></script>
    <script src="frontend/js/control_utils.js" crossorigin="anonymous"></script>
    <script src="frontend/js/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="frontend/js/face_mesh.js" crossorigin="anonymous"></script>
    <script src="frontend/js/opencv.js"></script>
    <script type="module" src="frontend/js/game.js"></script>
    <link rel="stylesheet" href="frontend/css/geral.css"/>
    <link rel="stylesheet" href="frontend/css/bootstrap.css"/>
    <link rel="stylesheet" href="frontend/fonts/RobotoCondensed-Bold_0.ttf"/>
    <link rel="stylesheet" href="frontend/fonts/WickedMouseDemo.otf"/>
    <script src="frontend/js/config.js"></script>
    <script src="frontend/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<!-- partial:index.partial.html -->
<div class="container">
    <video class="input_video"></video>
    <div class="canvas-container">
        <canvas class="output_canvas" width="1px" height="1px">
        </canvas>
    </div>
    <div class="loading">
        <div class="spinner"></div>
        <div class="message">
            Loading
        </div>
    </div>

    <a class="abs logo" href="" target="_blank">
        <div style="display: flex;align-items: center;bottom: 0;right: 10px;">
<!--            <img class="logo" src="logo_white.png" alt="" style="height: 0px;">-->
            <span class="title"></span>
        </div>
    </a>
    <div class="shoutout">
        <div>
            <a href=""></a>
        </div>
    </div>
</div>
<div class="control-panel">
</div>
<!-- partial -->
<img class="imagem-calc-medidas" src="frontend/images/AF_GAME_BG_01_edit.png" id="getTam" height="100%"/>
<img src="frontend/images/CAMINHAO.png" class="caminhao" id="caminhao" hidden/>
<img src="frontend/images/AF_GAME_PECAS1.png" class="imagem-peca1" id="imagem-peca1" hidden/>
<img src="frontend/images/AF_GAME_PECAS2.png" class="imagem-peca2" id="imagem-peca2" hidden/>
<img src="frontend/images/AF_GAME_PECAS3.png" class="imagem-peca3" id="imagem-peca3" hidden/>
<img src="frontend/images/AF_GAME_PECAS4.png" class="imagem-peca4" id="imagem-peca4" hidden/>
<img src="frontend/images/AF_GAME_PECAS5.png" class="imagem-peca5" id="imagem-peca5" hidden/>
<img src="frontend/images/AF_GAME_PECAS6.png" class="imagem-peca6" id="imagem-peca6" hidden/>
<!--    <img src="frontend/images/AF_GAME_PECAS7.png" class="imagem-peca7" id="imagem-peca7" />-->
<img src="frontend/images/AF_GAME_PECAS8.png" class="imagem-peca8" id="imagem-peca8" hidden/>
<img src="frontend/images/AF_GAME_PECAS9.png" class="imagem-peca9" id="imagem-peca9" hidden/>
<img src="frontend/images/AF_GAME_PECAS10.png" class="imagem-peca10" id="imagem-peca10" hidden/>


<div class="cadastro-ui">
    <form class="form-cadastro">
        <h2>Cadastre-se</h2>
        <input type="text" id="nome" name="nome" class="input-cadastro" placeholder="Nome" >

        <input type="text" id="email" name="email" class="input-cadastro" placeholder="E-mail" >

        <input type="text" id="telefone" name="telefone" class="input-cadastro" placeholder="Telefone" >

        <div style="display: flex; width: 80%; font-size: 200%">
            <label for="termos"
                   style="color: #0b53be; font-weight: bold; font-size: 150%; padding: 10px; margin-top: 20px">
                <input type="checkbox" name="termos" id="termos" style="height: 100px; width: 100px " >
                Aceito os termos de uso
            </label>
        </div>

        <p style="font-size: 200%; color: indianred; display: none; margin-bottom: -20px" id="alert"> Preencha todos os campos </p>

        <button class="cadastro-button" type="button" id="cadastro" style="margin-top: 350px">Cadastrar</button>

    </form>
</div>

<div class="ranking-ui" style="display: none">
    <div class="container-ranking" style="width: 80%">
        <div class="row" style="margin-top: 250px">
            <h2 style="font-size: 250%">Ranking</h2>
            <table class="table table-striped " id="table-ranking" style="font-size: 200%; ">
                <thead>
                <tr style="height: 40px">
                    <th scope="col">Posição</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Pontuação</th>
                </tr>
                </thead>
                <tbody id="table-ranking">
                </tbody>
            </table>
            <div class="row flex">
                <div class="col-12">
                    <button class="cadastro-button" style="margin-top: 580px; width: 100%" type="button"
                            id="btn-cadastro">
                        Cadastro
                    </button>
                </div>
<!--                <div class="col-6">-->
<!--                    <button class="cadastro-button" type="button" id="btn-jogar"-->
<!--                            style="margin-top: 580px; width: 100%">Jogar-->
<!--                    </button>-->
<!--                </div>-->
            </div>
        </div>


    </div>
</div>

<div class="start-ui start-ui-sxm start-ui-sm start-ui-md start-ui-lg start-ui-xl start-ui-xxl" id="start-ui"
     style="display: none">


    <div class="btn-start-position">
        <!--<button class="btn-start-game" id="btn-start-game">INICIAR</button>-->
        <img src="frontend/images/INICIAR.png" id="btn-start-game" width="350" height="190"/>
    </div>

</div>

<div class="game-ui game-ui-sxm game-ui-sm game-ui-md game-ui-lg game-ui-xl game-ui-xxl" id="game-ui"
     style="display: none;">
    <img class="imagem-calc-medidas" src="frontend/images/AF_GAME_BG_02.png" id="fundogame" height="100%"/>
    <div class="container-game">
        <p id="level"></p>
        <div class="container-pontuacao" id="container-pontuacao">
            <p id="pontuacao"></p>
        </div>
        <!--<canvas id="canvas" width="1080" height="1770"></canvas>-->
        <canvas id="canvas" width="800" height="1460"></canvas>
        <!--<p><b>INSTRUCTIONS:</b>
            <- Left and Right arrow keys to move ->
        </p>
        <p>Collect as many primary colors as you can but be careful to avoid all other colors!</p>-->
    </div>

</div>

<div class="endgame-u endgame-ui-sxm endgame-ui-sm endgame-ui-md endgame-ui-lg endgame-ui-xl endgame-ui-xxl"
     id="endgame-ui" style="display: none;">
    <div class="container-resultados">
        <img src="frontend/images/estrela.png" width="250" height="250"/>
        <div class="container-resultados-child">
            <p class="span-pontuacao-qnt" id="end-pontuacao">
                0
            </p>
            <p class="span-pontuacao-txt">
                pontos
            </p>
        </div>
    </div>
    <div class="btn-restart-position">
        <img src="frontend/images/FINALIZAR.png" id="btn-restart-game" width="550" height="230"/>
    </div>
</div>


</body>
<script
        src="https://code.jquery.com/jquery-3.6.4.slim.min.js"
        integrity="sha256-a2yjHM4jnF9f54xUQakjZGaqYs/V1CYvWpoqZzC2/Bw="
        crossorigin="anonymous"></script>
<script>
    const cadastro = document.getElementById('cadastro');
    const nome = document.getElementById('nome');
    const email = document.getElementById('email');
    const telefone = document.getElementById('telefone');

    const div_cadastro = $('.cadastro-ui');
    const div_start = $('.start-ui');
    const div_ranking = $('.ranking-ui');

    const btn_jogar = document.getElementById('btn-jogar');
    const btn_cadastro = document.getElementById('btn-cadastro');

    cadastro.addEventListener('click', function () {
        if (nome.value === '' || email.value === '' || telefone.value === '') {
            $('#alert').show();
        } else {
            $('#alert').hide();
            div_cadastro.hide();
            div_ranking.hide();
            div_start.show();
        }
    })

    // btn_jogar.addEventListener('click', function () {
    //     div_ranking.hide();
    //     div_cadastro.hide();
    //     div_start.show();
    // })

    btn_cadastro.addEventListener('click', function () {
        div_cadastro.show();
        div_ranking.hide();
        div_start.hide();
    })

</script>
</html>