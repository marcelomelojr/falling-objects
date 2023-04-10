<?php

/**
 * @package PontuacaoView
 * @author flavio
 */

class PontuacaoView
{
    /**
     * responseRequest
     * Function que verifica que deu certo insert:
     * 
     * @param array $values
     * @return array
     */
    public function responseRequest($values)
    {
        if (isset($values["idstatus"]) && $values["idstatus"] = 200) {
            return array($values["idstatus"], $values["msg"]);
        } else {
            return array($values["iderror"], $values["msg"]);
        }
    }

    /**
     * gerarTabelaTops
     * Gerar tabela html dos top5:
     * 
     * @param array $values
     * @return string
     */
    public function gerarTabelaTops($values) {
        $str = "";
        if(isset($values) && is_array($values)) {
            $tot = count($values) ? count($values) : 0;
            if ($tot > 0) { 
                // existe tops
                foreach ($values as $key => $top) {
                    $img = $key == 0 ? "<img src='img/' alt='IMG LOGO'>" : "";
                    $nome = sanitizarVarString($top["nome"]) ?? "Sem nome";
                    $pontos = sanitizarVarInt($top["pontos"]) ?? 0;
                    $tempo = sanitizarVarInt($top["tempo"]) ?? 0;

                    $str .= "
                        
                    ";
                }
            } else {
                // nao tem ninguem no top
                $str = "
                    
                ";
            }
        } else {
            // nao tem ninguem no top
            $str = "
                
            ";
        }

        return $str;
    }
}
