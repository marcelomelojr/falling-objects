<?php

/**
 * @package ConnectController
 * @author flavio
 */

class ConnectController
{
    /**
     * selectQuery
     * Function utilizada para fazer SELECT no base de dados de forma simplificada e validando erros
     * 
     * @param string $newQuery
     * @param array $params
     * @return array
     */
    public function selectQuery($newQuery, $params = array())
    {
        $model = new Connect();
        $view = new ConnectView();

        $stmt = $model->newQuery($newQuery, $params);
        $validate = $view->validateQuery($stmt);

        if (isset($validate["idstatus"]) && $validate["idstatus"] == 200) {
            return $view->validateFetchAll($stmt->fetchAll(PDO::FETCH_ASSOC));
        } else {
            // Aconteceu um erro ao executar a query, entao retorna como msg de erro e seu status:
            return $validate;
        }
    }

    /**
     * insertQuery
     * Function utilizada para fazer INSERT na base de dados de uma forma simplificada, validando possiveis erros e os
     * retornando de um jeito simplificado (array com status e msg de erro) para trabalhar de um jeito facil no front.
     * 
     * @param string $newQuery
     * @param array $params
     * @return array
     */
    public function insertQuery($newQuery, $params = array())
    {
        $model = new Connect();
        $view = new ConnectView();

        $stmt = $model->newQuery($newQuery, $params);

        $validate = $view->validateQuery($stmt);

        /*if (isset($validate["idstatus"]) && $validate["idstatus"] == 200) {
            //return $stmt;
            return $validate;
        } else {
            var_dump($validate);
        }*/
        // Validate pode retornar "iderror" em caso de erro ou idstatus em caso de dar certo
        return $validate;
    }

    /**
     * updateQuery
     * Function utilziada para fazer UPDATE e DELETE na base de dados de uma forma simplificada, validando possiveis erros e
     * verificando se realmente aconteceu alguma mudanca e retornando como array com status e msg para trabalhar facil no front.
     * 
     * @param string $newQuery
     * @param array $params
     * @return array
     */
    public function updateQuery($newQuery, $params = array())
    {
        $model = new Connect();
        $view = new ConnectView();

        $stmt = $model->newQuery($newQuery, $params);
        $validate = $view->validateRowsAffected($stmt);

        /*if($validate == 200) {
            return array("idstatus" => 200, "msg" => "Foi atualizado");
        } else {
            return $validate;
        }*/

        return $validate;
    }
}
