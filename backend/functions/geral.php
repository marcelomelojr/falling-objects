<?php
// Define functions gerais:
function existValue($value, $undefined = "")
{
    $validate = (isset($value) && $value != null) ? $value : $undefined;
    return validateValue($validate);
}

function validateValue($value)
{
    $value = stripcslashes($value);
    $value = strip_tags($value);

    return $value;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function verificaEmail($email)
{

    /* Verifica se o email e valido */
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        /* Obtem o dominio do email */
        list($usuario, $dominio) = explode("@", $email);

        /* Faz um verificacao de DNS no dominio */
        if (checkdnsrr($dominio, "MX") == 1) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function formataData($value, $format = "d/m/Y", $time_zone = "America/Sao_Paulo")
{
    $timezone = new DateTimeZone($time_zone);
    $dateTime = new DateTime($value, $timezone);

    return $dateTime->format($format);
}

function sanitizarVarString($value)
{
    $newvalue = htmlentities($value, ENT_QUOTES, 'UTF-8');
    //$newvalue = htmlspecialchars($newvalue);
    $newvalue = stripslashes($newvalue);
    $newvalue = trim($newvalue);
    $newvalue = filter_var($newvalue, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

    return $newvalue;
}

function sanitizarVarInt($value)
{
    /*if (is_numeric($value)) {
        $newvalue = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        return $newvalue;
    } else {
        return "";
    }*/
    $newvalue = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    return $newvalue;
}

function sanitizarVarDouble($value)
{
    if (is_numeric($value)) {
        $newvalue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $newvalue;
    } else {
        return "";
    }
}
