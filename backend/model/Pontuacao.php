<?php

/**
 * @package Pontuacao
 * @author flavio
 */

class Pontuacao
{
    // Atributos
    private $nome;
    private $email;
    private $telefone;
    private $pontos;
    private $tempo;

    // Metodos especiais
    public function __construct($nome, $email, $telefone, $pontos, $tempo)
    {
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setTelefone($telefone);
        $this->setPontos($pontos);
        $this->setTempo($tempo);
    }

    /**
     * Get the value of nome
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     */
    public function setNome($nome): self
    {
        $this->nome = sanitizarVarString($nome);

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail($email): self
    {
        /* Obtem o dominio do email */
        if (verificaEmail($email)) {
            list($usuario, $dominio) = explode("@", $email);
            $usuario = sanitizarVarString($usuario);
            $dominio = sanitizarVarString($dominio);

            $this->email = "$usuario@$dominio";
        } else {
            $this->email = null;
        }
        return $this;
    }

    /**
     * Get the value of telefone
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set the value of telefone
     */
    public function setTelefone($telefone): self
    {
        $this->telefone = sanitizarVarInt($telefone);


        return $this;
    }

    /**
     * Get the value of pontos
     */
    public function getPontos()
    {
        return $this->pontos;
    }

    /**
     * Set the value of pontos
     */
    public function setPontos($pontos): self
    {
        $this->pontos = sanitizarVarInt($pontos);

        return $this;
    }

    /**
     * Get the value of tempo
     */
    public function getTempo()
    {
        return $this->tempo;
    }

    /**
     * Set the value of tempo
     */
    public function setTempo($tempo): self
    {
        $this->tempo = sanitizarVarInt($tempo);

        return $this;
    }

    // Metodos publicos
    public function adicionarPontuacao()
    {
        // Chama classe de pdo
        $pdo = new ConnectController();

        // Recolhe dados 
        $nome = $this->getNome();
        $email = $this->getEmail();
        $telefone = $this->getTelefone();
        $pontos = $this->getPontos();
        $tempo = $this->getTempo();

        $values = array(":nome" => $nome, ":email" => $email, ":telefone" => $telefone, ":pontos" => $pontos, ":tempo" => $tempo);
        $result = $pdo->insertQuery("INSERT INTO pontuacoes (nome, email, telefone, pontos, tempo) VALUES (:nome, :email, :telefone, :pontos, :tempo)", $values);

        return $result;
    }

    public function obterTops()
    {
        // Chama classe de pdo
        $pdo = new ConnectController();

        $result = $pdo->selectQuery("SELECT * FROM pontuacoes ORDER BY pontos DESC, tempo ASC LIMIT 5");

        return $result;
    }
}
