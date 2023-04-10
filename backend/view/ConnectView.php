<?php

/**
 * @package ConnectView
 * @author flavio
 */

 class ConnectView
{
    /**
     * validateQuery
     * Funcao que recolhe um array e valida ele para ver se alguma acao aconteceu ou nao e o retorna em array
     * 
     * @param array $values
     * @return array
     */
    public function validateQuery($values)
    {
        if (is_array($values)) {
            if (isset($values["iderror"]) && isset($values["msg"])) {
                return $values;
            }
        } else {
            //return 200;
            return array("idstatus" => 200, "msg" => "Query executada");
        }
    }

    /**
     * validateFetchAll
     * Funcao que vai retornar todos os valores de um array ou em caso de nao ter, um array vazio
     * 
     * @param array $values
     * @return array
     */
    public function validateFetchAll($values)
    {
        if (is_array($values)) {
            // Deu tudo certo, retorna os valores:
            return $values;
        } else {
            // Aconteceu algo errado, retorna error:
            return array();
        }
    }


    /**
     * validateRowsAffected
     * Funcao que vai retornar um array informando se foi ou nao alteradas alguma row da DB.
     * 
     * @param object $values
     * @return array
     */
    public function validateRowsAffected($values)
    {
        if ($values->rowCount() > 0) {
            return array("idstatus" => 200, "msg" => "Foi atualizado");
        } else {
            // Nenhuma alteracao foi feita:
            return array("iderror" => 406, "msg" => "Não aconteceu nenhuma alteração. Tente novamente");
        }
    }
}
