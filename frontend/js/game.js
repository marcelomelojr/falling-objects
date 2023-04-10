/**
 * AQUI TEM AS CONFIG DO VIDEO EM CAMERA
 */
const controls = window;
const drawingUtils = window;
const mpFaceMesh = window;
const config = {
    locateFile: (file) => {
        console.log(`frontend/config/${file}`);
        return (`frontend/config/${file}`);
    }
};

const videoElement = document.getElementsByClassName("input_video")[0];
const canvasElement = document.getElementsByClassName("output_canvas")[0];
const controlsElement = document.getElementsByClassName("control-panel")[0];
const canvasCtx = canvasElement.getContext("2d");

const fpsControl = new controls.FPS();
const spinner = document.querySelector(".loading");
spinner.ontransitionend = () => {
    spinner.style.display = "none";
};

const solutionOptions = {
    selfieMode: true,
    enableFaceGeometry: false,
    maxNumFaces: 1,
    refineLandmarks: true,
    minDetectionConfidence: 0.5,
    minTrackingConfidence: 0.5
};
/**
 * ======================================================================================
 */
// VARIAVEIS DO GAME
let velocityObjects = 3;
let velocityCar = 30;
let innitialLives = 1;


// VARIAVEIS DO CANVAS (NÃO ALTERAR)
var canvas = document.getElementById("canvas");
var contxt = canvas.getContext("2d");
var lefty = false;
var righty = false;
var gameOver = true;
var score = 0;
var track = 0;
var badTrack = 0;
var lives = 3;
var level = 1;

/**
 * AQUI TEM AS CONFIG DE MOVIMENTO COM CAMERA
 */
function onResults(results) {
    // console.log(results);
    document.body.classList.add("loaded");
    fpsControl.tick();
    var face_2d = [];
    // https://github.com/google/mediapipe/blob/master/mediapipe/modules/face_geometry/data/canonical_face_model.obj      
    // https://github.com/google/mediapipe/blob/master/mediapipe/modules/face_geometry/data/canonical_face_model_uv_visualization.png
    var points = [1, 33, 263, 61, 291, 199];
    /*
    var pointsObj = [ 0.0,
        -3.406404,
        5.979507,
        -2.266659,
        -7.425768,
        4.389812,
        2.266659,
        -7.425768,
        4.389812,
        -0.729766,
        -1.593712,
        5.833208,
        0.729766,
        -1.593712,
        5.833208,
        //0.000000, 1.728369, 6.316750];
        -1.246815,
        0.230297,
        5.681036];
  */
    var pointsObj = [0, -1.126865, 7.475604, -4.445859, 2.663991, 3.173422,
        4.445859, 2.663991, 3.173422, -2.456206, -4.342621, 4.283884,
        2.456206, -4.342621, 4.283884,
        0, -9.403378, 4.264492
    ]; //chin
    canvasCtx.save();
    canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
    canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);
    var width = results.image.width; //canvasElement.width; 
    var height = results.image.height; //canvasElement.height; //results.image.height;
    var roll = 0,
        pitch = 0,
        yaw = 0;
    var direcao = "";
    var x, y, z;
    var normalizedFocaleY = 1.28;
    var focalLength = height * normalizedFocaleY;
    var s = 0; //0.953571;
    var cx = width / 2;
    var cy = height / 2;
    var cam_matrix = cv.matFromArray(3, 3, cv.CV_64FC1, [
        focalLength,
        s,
        cx,
        0,
        focalLength,
        cy,
        0,
        0,
        1
    ]);
    //var dist_matrix = cv.Mat.zeros(4, 1, cv.CV_64FC1); 
    var k1 = 0.1318020374;
    var k2 = -0.1550007612;
    var p1 = -0.0071350401;
    var p2 = -0.0096747708;
    var dist_matrix = cv.matFromArray(4, 1, cv.CV_64FC1, [k1, k2, p1, p2]);
    var message = "";
    if (results.multiFaceLandmarks) {
        for (const landmarks of results.multiFaceLandmarks) {
            drawingUtils.drawConnectors(canvasCtx, landmarks, mpFaceMesh.FACEMESH_TESSELATION, {
                color: "#C0C0C070",
                lineWidth: 1
            });
            for (const point of points) {
                var point0 = landmarks[point];
                //console.log("landmarks : " + landmarks.landmark.data64F);
                drawingUtils.drawLandmarks(canvasCtx, [point0], {color: "#FFFFFF"});
                var x = point0.x * width;
                var y = point0.y * height;
                //var z = point0.z; 
                face_2d.push(x);
                face_2d.push(y);
            }
        }
    }
    if (face_2d.length > 0) {
        var rvec = new cv.Mat(); // = cv.matFromArray(1, 3, cv.CV_64FC1, [0, 0, 0]); //new cv.Mat({ width: 1, height: 3 }, cv.CV_64FC1); 
        var tvec = new cv.Mat(); // = cv.matFromArray(1, 3, cv.CV_64FC1, [-100, 100, 1000]); //new cv.Mat({ width: 1, height: 3 }, cv.CV_64FC1); 
        const numRows = points.length;
        const imagePoints = cv.matFromArray(numRows, 2, cv.CV_64FC1, face_2d);
        var modelPointsObj = cv.matFromArray(6, 3, cv.CV_64FC1, pointsObj);
        //console.log("modelPointsObj : " + modelPointsObj.data64F);
        //console.log("imagePoints : " + imagePoints.data64F);
        // https://docs.opencv.org/4.6.0/d9/d0c/group__calib3d.html#ga549c2075fac14829ff4a58bc931c033d
        // https://docs.opencv.org/4.6.0/d5/d1f/calib3d_solvePnP.html
        var success = cv.solvePnP(modelPointsObj, //modelPoints,
            imagePoints, cam_matrix, dist_matrix, rvec,
            tvec, false,
            cv.SOLVEPNP_ITERATIVE //SOLVEPNP_EPNP //SOLVEPNP_ITERATIVE 
        );
        if (success) {
            var rmat = cv.Mat.zeros(3, 3, cv.CV_64FC1);
            const jaco = new cv.Mat();
            //console.log("rvec", rvec.data64F[0], rvec.data64F[1], rvec.data64F[2]);
            //console.log("tvec", tvec.data64F[0], tvec.data64F[1], tvec.data64F[2]);
            cv.Rodrigues(rvec, rmat, jaco);
            var sy = Math.sqrt(rmat.data64F[0] * rmat.data64F[0] + rmat.data64F[3] * rmat.data64F[3]);
            var singular = sy < 1e-6;
            if (!singular) {
                //console.log("!singular");
                x = Math.atan2(rmat.data64F[7], rmat.data64F[8]);
                y = Math.atan2(-rmat.data64F[6], sy);
                z = Math.atan2(rmat.data64F[3], rmat.data64F[0]);
            } else {
                //console.log("singular");
                x = Math.atan2(-rmat.data64F[5], rmat.data64F[4]);
                //  x = Math.atan2(rmat.data64F[1], rmat.data64F[2]);
                y = Math.atan2(-rmat.data64F[6], sy);
                z = 0;
            }
            roll = y;
            pitch = x;
            yaw = z;
            var worldPoints = cv.matFromArray(9, 3, cv.CV_64FC1, [
                modelPointsObj.data64F[0] + 3,
                modelPointsObj.data64F[1],
                modelPointsObj.data64F[2],
                modelPointsObj.data64F[0],
                modelPointsObj.data64F[1] + 3,
                modelPointsObj.data64F[2],
                modelPointsObj.data64F[0],
                modelPointsObj.data64F[1],
                modelPointsObj.data64F[2] - 3,
                modelPointsObj.data64F[0],
                modelPointsObj.data64F[1],
                modelPointsObj.data64F[2],
                modelPointsObj.data64F[3],
                modelPointsObj.data64F[4],
                modelPointsObj.data64F[5],
                modelPointsObj.data64F[6],
                modelPointsObj.data64F[7],
                modelPointsObj.data64F[8],
                modelPointsObj.data64F[9],
                modelPointsObj.data64F[10],
                modelPointsObj.data64F[11],
                modelPointsObj.data64F[12],
                modelPointsObj.data64F[13],
                modelPointsObj.data64F[14],
                modelPointsObj.data64F[15],
                modelPointsObj.data64F[16],
                modelPointsObj.data64F[17] //
            ]);
            //console.log("worldPoints : " + worldPoints.data64F);
            var imagePointsProjected = new cv.Mat({width: 9, height: 2}, cv.CV_64FC1);
            cv.projectPoints(worldPoints,
                rvec, tvec, cam_matrix, dist_matrix, imagePointsProjected, jaco);
            canvasCtx.lineWidth = 5;
            var scaleX = canvasElement.width / width;
            var scaleY = canvasElement.height / height;
            canvasCtx.strokeStyle = "red";
            canvasCtx.beginPath();
            canvasCtx.moveTo(imagePointsProjected.data64F[6] * scaleX, imagePointsProjected.data64F[7] * scaleX);
            canvasCtx.lineTo(imagePointsProjected.data64F[0] * scaleX, imagePointsProjected.data64F[1] * scaleY);
            canvasCtx.closePath();
            canvasCtx.stroke();
            canvasCtx.strokeStyle = "green";
            canvasCtx.beginPath();
            canvasCtx.moveTo(imagePointsProjected.data64F[6] * scaleX, imagePointsProjected.data64F[7] * scaleX);
            canvasCtx.lineTo(imagePointsProjected.data64F[2] * scaleX, imagePointsProjected.data64F[3] * scaleY);
            canvasCtx.closePath();
            canvasCtx.stroke();
            canvasCtx.strokeStyle = "blue";
            canvasCtx.beginPath();
            canvasCtx.moveTo(imagePointsProjected.data64F[6] * scaleX, imagePointsProjected.data64F[7] * scaleX);
            canvasCtx.lineTo(imagePointsProjected.data64F[4] * scaleX, imagePointsProjected.data64F[5] * scaleY);
            canvasCtx.closePath();
            canvasCtx.stroke();
            // https://developer.mozilla.org/en-US/docs/Web/CSS/named-color
            canvasCtx.fillStyle = "aqua";
            for (var i = 6; i <= 6 + 6 * 2; i += 2) {
                canvasCtx.rect(imagePointsProjected.data64F[i] * scaleX - 5, imagePointsProjected.data64F[i + 1] * scaleY - 5, 10, 10);
                canvasCtx.fill();
            }
            jaco.delete();
            imagePointsProjected.delete();
        }
        canvasCtx.fillStyle = "black";
        canvasCtx.font = "bold 30px Arial";
        canvasCtx.fillText("roll: " + (180.0 * (roll / Math.PI)).toFixed(2),
            //"roll: " + roll.toFixed(2),
            width * 0.8, 50);
        canvasCtx.fillText("pitch: " + (180.0 * (pitch / Math.PI)).toFixed(2),
            //"pitch: " + pitch.toFixed(2),
            width * 0.8, 100);
        canvasCtx.fillText("yaw: " + (180.0 * (yaw / Math.PI)).toFixed(2),
            //"yaw: " + yaw.toFixed(3),
            width * 0.8, 150);
        //const direcao_result = pitch < -10 ? 'down' : pitch > 30 ? 'up' : 'froward';
        //console.log(direcao)
        direcao = (180.0 * (roll / Math.PI)).toFixed(2) < -15 ? 'direita' : (180.0 * (roll / Math.PI)).toFixed(2) > 15 ? 'esquerda' : 'meio';
        canvasCtx.fillText("direcao: " + direcao,
            width * 0.8, 200);
        if (direcao === "direita") {
            righty = true;
            lefty = false;
        } else if (direcao === "esquerda") {
            righty = false;
            lefty = true;
        } else {
            righty = false;
            lefty = false;
        }
        //console.log("pose %f %f %f", (180.0 * (roll / Math.PI)).toFixed(2), (180.0 * (pitch / Math.PI)).toFixed(2), (180.0 * (yaw / Math.PI)).toFixed(2));
        rvec.delete();
        tvec.delete();
    }
    canvasCtx.restore();
}

const faceMesh = new mpFaceMesh.FaceMesh(config);
faceMesh.setOptions(solutionOptions);
faceMesh.onResults(onResults);
new controls.ControlPanel(controlsElement, solutionOptions)
    .add([
        new controls.StaticText({title: "MediaPipe Face Mesh"}),
        fpsControl,
        new controls.Toggle({title: "Selfie Mode", field: "selfieMode"}),
        new controls.SourcePicker({
            onFrame: async (input, size) => {
                const aspect = size.height / size.width;
                let width, height;
                if (window.innerWidth > window.innerHeight) {
                    height = window.innerHeight;
                    width = height / aspect;
                } else {
                    width = window.innerWidth;
                    height = width * aspect;
                }
                canvasElement.width = width;
                canvasElement.height = height;
                await faceMesh.send({image: input});
            }
        }),
        new controls.Slider({
            title: "Max Number of Faces",
            field: "maxNumFaces",
            range: [1, 4],
            step: 1
        }),
        new controls.Toggle({
            title: "Refine Landmarks",
            field: "refineLandmarks"
        }),
        new controls.Slider({
            title: "Min Detection Confidence",
            field: "minDetectionConfidence",
            range: [0, 1],
            step: 0.01
        }),
        new controls.Slider({
            title: "Min Tracking Confidence",
            field: "minTrackingConfidence",
            range: [0, 1],
            step: 0.01
        })
    ])
    .on((x) => {
        const options = x;
        videoElement.classList.toggle("selfie", options.selfieMode);
        faceMesh.setOptions(options);
    });


// document.addEventListener("keydown", keysDown, false);
// document.addEventListener("keyup", keysUp, false);

var button = document.getElementById("btn-start-game");
button.addEventListener("click", iniciarGame, false);

var buttonStartAgain = document.getElementById("btn-restart-game");
buttonStartAgain.addEventListener("click", restartGane, false);

// when key is pressed down, move
// function keysDown(e) {
//     if (e.keyCode == 39) {
//         righty = true;
//     } else if (e.keyCode == 37) {
//         lefty = true;
//     } else if (e.keyCode == 32 && gameOver) {
//         playAgain();
//     }
// }

// when key is released, stop moving
// function keysUp(e) {
//     if (e.keyCode == 39) {
//         righty = false;
//     } else if (e.keyCode == 37) {
//         lefty = false;
//     }
//
// }

var elementTam = document.getElementById('getTam');
var distWidth = elementTam.clientWidth;
var distHei = elementTam.clientHeight;

// player specs
var player = {
    size: 30,
    sizeH: distHei * 0.115,
    sizeW: distWidth * 0.3,
    /*x: (distWidth - 30) / 2,
    y: distHei - 190,*/
    x: (distWidth - 30) / 2,
    y: distHei - (distHei * 0.34),
    color: "green"
};

// specs for balls you want to collect
var goodArc = {
    x: [],
    y: [],
    speed: 2,
    color: ["red", "blue", "yellow"],
    imagem: ["imagem-peca1", "imagem-peca2", "imagem-peca3", "imagem-peca4", "imagem-peca5", "imagem-peca6", "imagem-peca8", "imagem-peca9", "imagem-peca10"],
    vW: [236.08, 119.14, 181.05, 45.33, 45, 135.31, 47.83, 27.22, 36.72],
    vH: [172.8, 192, 192, 192, 192, 211.19, 192, 192, 192],
    state: []
};
var redNum = 0;

// specs for balls you want to avoid
var badArc = {
    x: [],
    y: [],
    speed: 8,
    color: ["black", "purple", "#003300", "#663300", "white"]

};
var blackNum = 0;
var rad = 10;

// adds value to x property of goodArc
function drawNewGood() {
    if (Math.random() < .0175) {
        let positionX = Math.random() * canvas.width - 225;

        positionX < 150 ? positionX = 150 : positionX;
        positionX > 650 ? positionX = 650 : positionX;

        goodArc.x.push(positionX);
        goodArc.y.push(0);
        goodArc.state.push(true);

    }
    redNum = goodArc.x.length;
}

//adds values to x property of badArc
function drawNewBad() {
    if (score < 30) {
        if (Math.random() < .05) {
            badArc.x.push(Math.random() * canvas.width);
            badArc.y.push(0);
        }
    } else if (score < 50) {
        if (Math.random() < .1) {
            badArc.x.push(Math.random() * canvas.width);
            badArc.y.push(0);
        }
    } else {
        if (Math.random() < .2) {
            badArc.x.push(Math.random() * canvas.width);
            badArc.y.push(0);
        }
    }
    blackNum = badArc.x.length;
}

// draws red and blue balls
function drawRedBall() {
    for (var i = 0; i < redNum; i++) {
        if (goodArc.state[i] == true) {
            //Keeps track of position in color array with changing redNum size
            var trackCol = (i + track);

            // contxt.beginPath();
            // contxt.arc(goodArc.x[i], goodArc.y[i], rad, 0, Math.PI * 2);
            // contxt.fillStyle = goodArc.color[trackCol % 3];
            // contxt.fill();
            // contxt.closePath();

            // config com foto
            var num = trackCol % 9;
            var img = document.getElementById(goodArc.imagem[num]);
            contxt.beginPath();
            contxt.drawImage(img, goodArc.x[i], goodArc.y[i], goodArc.vW[num], goodArc.vH[num]);
            contxt.fill();
            contxt.closePath();
        }
    }
}

// draw player to canvas
function drawPlayer() {
    let img = document.getElementById("caminhao");
    contxt.beginPath();
    contxt.drawImage(img, player.x, player.y, player.sizeW, player.sizeH);
    contxt.fill();
    contxt.closePath();
}

// moves objects in play
async function playUpdate() {

    if (lefty && player.x > 0) {
        player.x -= velocityCar;
    }
    if (righty && player.x + player.size < canvas.width - 275) {
        player.x += velocityCar;
    }
    for (var i = 0; i < redNum; i++) {
        goodArc.y[i] += goodArc.speed;
    }

    // collision detection
    for (let i = 0; i < redNum; i++) {
        // Only counts collision once
        let num = (i + track) % 9;
        if (goodArc.state[i]) {
            // if (player.x < goodArc.x[i] + rad && player.x + 30 + rad > goodArc.x[i] && player.y < goodArc.y[i] + rad && player.y + 30 > goodArc.y[i]) {
            if (player.x < goodArc.x[i] +
                goodArc.vW[num] && player.x +
                player.sizeW + goodArc.vW[num] > goodArc.x[i]
                && player.y < goodArc.y[i] + goodArc.vH[num]
                && player.y + player.sizeH - 15 > goodArc.y[i]) {
                score++
                // // Cycles through goodArc's color array
                // player.color = goodArc.color[(i + track) % 9];

                goodArc.state[i] = false;
                // goodArc.x.pop();
                // goodArc.y.pop();
                // goodArc.state.pop();

                delete goodArc.x[i];
                delete goodArc.y[i];
                delete goodArc.state[i];
                //
                // console.log(goodArc, i, "Pegou")
                // return;
            }
        }
        // Removes circles from array that are no longer in play
        //if (goodArc.y[i] + rad > canvas.height) {

        if ((goodArc.y[i] + goodArc.vH[num]) > canvas.height) {
            // console.log(goodArc.y[i] + goodArc.vH[num], canvas.height);
            goodArc.x.shift();
            goodArc.y.shift();
            goodArc.state.shift();
            track++;

            if (goodArc.state[i]){
                // console.log(goodArc.y[i] + goodArc.vH[num], canvas.height, JSON.stringify(goodArc));
                // if ((goodArc.y[i] + goodArc.vH[num]) > canvas.height) {
                    lives--;
                // }


            }

            // config para dar gameover
            if (lives <= 0) {
                gamesOver();
                telaGameOver();
            }
        }
    }
    // for (let i = 0; i < blackNum; i++) {
    //     if (player.x < badArc.x[i] + rad && player.x + 30 + rad > badArc.x[i] && player.y < badArc.y[i] + rad && player.y + 30 > badArc.y[i]) {
    //         lives--;
    //         player.color = badArc.color[(i + badTrack) % 5];
    //         badArc.y[i] = 0;
    //         if (lives <= 0) {
    //             gamesOver();
    //         }
    //     }
    //     // Removes circles from x and y arrays that are no longer in play
    //     if (badArc.y[i] + rad > canvas.height) {
    //         badArc.x.shift();
    //         badArc.y.shift();
    //         badTrack++;
    //     }
    // }
    switch (score) {
        case 5:
            goodArc.speed = 1.5 * velocityObjects;
            break;
        case 10:
            badArc.speed = 3;
            goodArc.speed = 2 * velocityObjects;
            level = 2;
            break;
        case 20:
            goodArc.speed = 2 * velocityObjects
            level = 2;
            break;
        case 30:
            goodArc.speed = 3 * velocityObjects
            level = 3;
            break;
        case 40:
            goodArc.speed = 4 * velocityObjects
            level = 4;
            break;
        case 50:
            goodArc.speed = 5 * velocityObjects
            level = 5;
            break;
    }

}

//signals end of game and resets x, y, and state arrays for arcs
function gamesOver() {
    goodArc.x = [];
    badArc.x = [];
    goodArc.y = [];
    badArc.y = [];
    goodArc.state = [];
    gameOver = true;
}

//resets game, life, and score counters
function playAgain() {
    gameOver = false;
    player.color = "green";
    level = 1;
    score = 0;
    lives = innitialLives;
    badArc.speed = velocityObjects;
    goodArc.speed = velocityObjects;
}

function showHidden(showid, hiddenid) {
    document.getElementById(showid).style.display = 'block';
    document.getElementById(hiddenid).style.display = 'none';
}

function iniciarGame() {
    showHidden("game-ui", "start-ui");
    //let element = document.getElementById('game-ui');
    let element = document.getElementById('fundogame');
    let canvaGame = document.getElementById('canvas');
    //let cpontuacao = document.getElementById("container-pontuacao");
    var distWidth = element.clientWidth;
    var distHei = element.clientHeight;
    canvaGame.setAttribute('width', distWidth);
    canvaGame.setAttribute('height', distHei - 460);
    //canvaGame.setAttribute('height', distHei - 150);
    //canvaGame.style.width = `${distWidth}px`;
    //canvaGame.style.height = `${distHei}px`;
    //cpontuacao.style.width = `${distWidth}px`;
    playAgain();
}


function telaGameOver() {
    showHidden("endgame-ui", "game-ui");
}

function restartGane() {
    showHidden("start-ui", "endgame-ui");
    const dados = getDadosPlayer();


    fetch('cadastro.php', {
        method: 'POST', body: JSON.stringify(
            {
                nome: dados.nome,
                email: dados.email,
                telefone: dados.telefone,
                pontuacao: dados.pontuacao,
                tempo: 0
            }
        )
    }).then((response) => {
        return response.json();

    }).then((data) => {

        document.querySelector('.cadastro-ui').style.display = 'none';
        document.querySelector('.start-ui').style.display = 'none';
        document.querySelector('.ranking-ui').style.display = 'block';
        const nome = document.getElementById('nome').value = '';
        const email = document.getElementById('email').value = '';
        const telefone = document.getElementById('telefone').value = '';


        let table = '';
        data.forEach((item, index) => {
            table += `<tr>
                 <td>${index + 1}</td>
                 <td>${item.nome}</td>
                 <td>${item.pontos}</td>
             </tr>`;
        });

        document.querySelector('#table-ranking').innerHTML = table;


    }).catch(err => console.log(err.message));
}

function getDadosPlayer() {
    let nome = document.getElementById("nome").value;
    let email = document.getElementById("email").value;
    let telefone = document.getElementById("telefone").value;
    let pontuacao = document.getElementById("pontuacao").innerHTML;

    let dados = {
        nome: nome,
        email: email,
        telefone: telefone,
        pontuacao: pontuacao
    }


    return dados;
}

let pontuacaoP = document.getElementById("pontuacao");
let finalPontuacao = document.getElementById("end-pontuacao");

async function draw() {
    contxt.clearRect(0, 0, canvas.width, canvas.height);
    if (!gameOver) {
        drawPlayer();
        //drawBlackBall();
        drawRedBall();
        await playUpdate();
        drawNewGood();
        //drawNewBad();

        //score
        /*contxt.fillStyle = "black";
        contxt.font = "20px Helvetica";
        contxt.textAlign = "left";
        contxt.fillText(score, 10, 25);*/
        pontuacaoP.innerHTML = `${score}`;

        // lives
        // contxt.textAlign = "right";
        // contxt.fillText("Lives: " + lives, 500, 25);
    } else {
        /*contxt.fillStyle = "black";
        contxt.font = "25px Helvetica";
        contxt.textAlign = "center";
        contxt.fillText("GAME OVER!", canvas.width / 2, 175);

        contxt.font = "20px Helvetica";
        contxt.fillText("PRESS SPACE TO PLAY", canvas.width / 2, 475);

        contxt.fillText("FINAL SCORE: " + score, canvas.width / 2, 230);*/
        finalPontuacao.innerHTML = `${score}`;
    }
    document.getElementById("level").innerHTML = "Level: " + level;
    requestAnimationFrame(draw);
}


await draw();