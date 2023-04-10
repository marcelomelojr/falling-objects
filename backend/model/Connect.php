<?php

/**
 * @package Connect
 * @author flavio
 */

class Connect extends PDO
{
    // Atributos:
    private $conn;

    // Métodos especiais:
    public function __construct()
    {
        try {
            if ($_SERVER["HTTP_HOST"] == "localhost") {
                $this->setConn(new PDO("mysql:host=localhost;dbname=cacada_falls", "root", ""));
            } else {
                $this->setConn(new PDO("mysql:host=localhost;dbname=cacada_falls", "root", ""));
            }
        } catch (PDOException $e) {
            echo "Error na base de dados: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Error generico: " . $e->getMessage();
        }
    }

    protected function getConn()
    {
        return $this->conn;
    }

    protected function setConn($conn)
    {
        $this->conn = $conn;
    }

    // Métodos publicos:

    /**
     * setParam
     * Function utilizada para setar um unico parametro de forma dinamica no formato do PDO
     *
     * @param object $statement
     * @param string $key
     * @param string value
     * @return void
     */
    private function setParam($statement, $key, $value)
    {
        $statement->bindParam($key, $value);
    }

    /**
     * setParams
     * Function utilizada para executar o setamento de todos os parametors que temos de forma dinamica no formato de PDO
     *
     * @param object $statement
     * @param array $parameter
     * @return void
     */
    private function setParams($statement, $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            $this->setParam($statement, $key, $value);
        }
    }

    /**
     * newQuery
     * Function utilizada para executar uma query e em caso de erro, retornar uma mensagem que vai ser identificada no controller
     * e formatada no view para um retorno facil de trabalhar no front.
     *
     * @param string $newQuery
     * @param array $params
     * @return object
     */
    public function newQuery($newQuery, $params = array())
    {
        try {
            $stmt = $this->getConn()->prepare($newQuery);
            $this->setParams($stmt, $params);

            if ($stmt->execute()) {
                return $stmt;
            } else {
                //var_dump($stmt->errorInfo());
                return array("iderror" => $stmt->errorCode(), "msg" => $stmt->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            return array("iderror" => $e->getCode(), "msg" => $e->getMessage());
        } catch (Exception $e) {
            //var_dump($e->getMessage());
            return array("iderror" => $e->getCode(), "msg" => $e->getMessage());
        }
    }

    /**
     * closeConnection
     * Funcao utilizada para encerrar conexao com a base de dados
     */
    public function closeConnection()
    {
        $this->setConn(null);
    }
}
