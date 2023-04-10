<?php

/**
 * @package PontuacaoController
 * @author flavio
 */

class PontuacaoController
{
    /**
     * criarPontuacao
     * Function que vai criar um novo registro de pontuacao DB MySQL:
     * 
     * @return array
     */
    public function criarPontuacao()
    {
        // Call de classes necessarias:
        $view = new PontuacaoView();

        // Constroi dados no model e executa query dentro do model
        $nome = isset($_POST["nome"]) && $_POST["nome"] != "" ? $_POST["nome"] : "Sem nome";
        $email = verificaEmail($_POST["email"]) && isset($_POST["email"]) ? $_POST["email"] : null;
        $telefone = isset($_POST["telefone"]) && $_POST["telefone"] != "" ? $_POST["telefone"] : null;
        $pontos = isset($_POST["pontos"]) && $_POST["pontos"] != "" ? $_POST["pontos"] : 0;
        $tempo = isset($_POST["tempo"]) && $_POST["tempo"] != "" ? $_POST["tempo"] : 0;

        $model = new Pontuacao($nome, $email, $telefone, $pontos, $tempo);
        $values = $model->adicionarPontuacao();

        $message = $view->responseRequest($values);
        return $message;
    }

    /**
     * top5
     * Function que vai retornar a lista das 5 maiores pontuacoes no formato da table pronta para dar echo.
     * 
     * @return string
     */
    public function top5()
    {
        // call de classes necessarias
        $view = new PontuacaoView();
        $model = new Pontuacao("", "", "", "", "");

        $values = $model->obterTops();
        return $view->gerarTabelaTops($values);
    }
}
