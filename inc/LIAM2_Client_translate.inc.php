<?php
/**
 * @param $key
 * @param $language
 * @return string
 * @throws Exception
 */
function translate($key, $language) {
    $liam_client_lang = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/json/LIAM2_Client_lang.json');
    $liam_client_lang = json_decode($liam_client_lang);
    try {
        if (isset($liam_client_lang->$key->$language->text)) {
            $text = $liam_client_lang->$key->$language->text;
        } else {
            throw new Exception();
        }
    } catch (Exception $e) {
        $text = '';
    }
    return $text;
}