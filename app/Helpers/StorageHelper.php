<?php

Namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

Class StorageHelper
{

    /**
     * Método facil para realizar um save no STORAGE
     *
     * @param $file
     * @param $local
     * @return string
     * @throws \Exception
     */
    public static function salvar($base64String, $local)
    {
        // Verifica se a string Base64 é válida
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $data = base64_decode($data);

            if ($data === false) {
                throw new \Exception("Erro ao decodificar a imagem Base64.");
            }

            // Gerar um nome de arquivo único
            $extension = $type[1]; // Captura a extensão (jpg, png, etc.)
            $img_ = uniqid('photo_', true) . '.' . $extension;

            // Salvar a imagem na pasta específica
            $path = 'public/' . $local . '/' . $img_;
            Storage::put($path, $data); // Salva o arquivo no storage

            return $img_;
        } else {
            throw new \Exception("String Base64 inválida.");
        }
    }
    /**
     * Deleta o arquivo especificado do armazenamento.
     *
     * @param string|null $filePath
     * @return bool
     */
    public static function deletar($filePath)
    {
        if (!$filePath) {
            return false;
        }

        // Verifica se o arquivo existe antes de tentar deletá-lo
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return true;
        }

        return false;
    }
}
