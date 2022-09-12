<?php 
namespace App;

class File {

    public static function saveUploadedFile($file, $directory, $fileName, $accept = ['pdf'], $maxSize = (2*1048576))
    {
        $errorCode = 0;
        
        if($file != null  && $file['error'] === 0){
            
            $name = $file['name'];
            $pieces = explode('.', $name);
            $extension = strtolower(end($pieces));
            
            if(array_search($extension, $accept) === false){
                $error = 'Extensão do arquivo é incompatível.';
                $errorCode = 1;
            } else if($file['size'] > $maxSize){
                $error = 'O arquivo é maior do que o tamanho máximo permitido.';
                $errorCode = 2;
            } else {
            
                $path = $directory . $fileName . "." . $extension;

                if (move_uploaded_file($file['tmp_name'],  $path )) {

                    return (Object) [
                        'errorStatus' => false,
                        'path' => $path,
                        'fileName' => ($fileName . "." . $extension)
                    ];

                } else {
                    $error = 'Houve um erro ao gravar o arquivo no disco.';
                }
            }
        } else if ($file['error'] === 4) {
            $error = 'O Arquivo não foi enviado.';
            $errorCode = 4;
        } else {
            $error = 'Houve um erro no envio do arquivo.';
        }

        if($errorCode != 4) {
            $message = "Erro ao gravar o arquivo " . $fileName . "." . $extension . " no disco.";
            Log::error($message, "files.log", $error);
        }

        return (Object) [
            'errorStatus' => true,
            'errorMessage' => $error,
            'errorCode' => $errorCode
        ];
    }
}