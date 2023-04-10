<?php
require 'backend/autoload.php';

$json = file_get_contents('php://input');
$data = json_decode($json);

$nome = $data->nome;
$email = $data->email;
$telefone = $data->telefone;
$pontuacao = $data->pontuacao;
$tempo = $data->tempo;

$db = new Connect();
$pont = new Pontuacao($nome, $email, $telefone, $pontuacao, $tempo);
$query = "INSERT INTO pontuacoes (nome, email, telefone, pontos, tempo) VALUES (:nome, :email, :telefone, :pontuacao, :tempo);";


$response = $db->newQuery($query, [
    'nome' => $nome,
    'email' => $email,
    'telefone' => $telefone,
    'pontuacao' => $pontuacao,
    'tempo' => $tempo
]);

$db->closeConnection();
var_dump($pont->obterTops());

//var_dump($db->newQuery('SELECT * FROM pontuacoes'));





