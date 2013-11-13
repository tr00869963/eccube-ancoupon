<?php

class An_Eccube_Utils {
    public static function encodeJson($data) {
        if (function_exists('json_decode') && function_exists('json_last_error')) {
            $json = json_encode($data);
            $error = json_last_error();
            if ($error !== JSON_ERROR_NONE) {
                $message = json_last_error_msg();
                throw new RuntimeException($message, $code);
            }
            return $json;
        }
        
        $encoder = new Services_JSON();
        $json = $encoder->decode($data);
        if (Services_JSON::isError($json)) {
            throw new RuntimeException($json->toString(), $json->getCode());
        }
        
        return $json;
    }
    
    public static function decodeJson($json, $return_assoc = false) {
        if (function_exists('json_decode') && function_exists('json_last_error')) {
            $data = json_decode($json, $return_assoc);
            $error = json_last_error();
            if ($error !== JSON_ERROR_NONE) {
                $message = json_last_error_msg();
                throw new RuntimeException($message, $error);
            }
            
            return $data;
        }
        
        // オートローダーを働かせて SERVICES_JSON_LOOSE_TYPE を定義させるため。
        class_exists('Services_JSON');
        
        $options = $return_assoc ? SERVICES_JSON_LOOSE_TYPE : 0;
        $decoder = new Services_JSON($options);
        $data = $decoder->decode($json);
        if (Services_JSON::isError($json)) {
            throw new RuntimeException($data->toString(), $data->getCode());
        }
        
        return $data;
    }
    
    /**
     * 削除対象のディレクトリから比較対象のディレクトリにある同名のファイルを削除します。
     * 要は SC_Utils::copyDirectory() の逆。
     * 
     * @param string $target_dir 削除対象のディレクトリ
     * @param string $source_dir 比較対象のディレクトリ
     */
    public static function deleteFileByMirror($target_dir, $source_dir) {
        $dir = opendir($source_dir);
        while ($name = readdir($dir)) {
            if ($name == '.' || $name == '..') {
                continue;
            }

            $target_path = $target_dir . '/' . $name;
            $source_path = $source_dir . '/' . $name;
            
            if (is_file($source_path)) {
                if (is_file($target_path)) {
                    unlink($target_path);
                    GC_Utils::gfPrintLog("$target_path を削除しました。");
                }
            } elseif (is_dir($source_path)) {
                if (is_dir($target_path)) {
                    self::deleteFileByMirror($target_path, $source_path);
                }
            }
        }
        closedir($dir);
    }
    
    /**
     * 文字列に対して別の文字列が後方一致しているかどうかを取得します。
     * 
     * @param string $target
     * @param string $tail
     * @return bool
     */
    public static function isStringEndWith($target, $tail) {
        return !substr_compare($target, $tail, -strlen($tail));
    }
    
    /**
     * タイムゾーンのない日付からタイムゾーン付きの日付に変換します。
     * 
     * @param string $date
     */
    public static function toDateTimeWithTimezone($datetime) {
        return date('Y-m-d\TH:i:sP', strtotime($datetime)); 
    }
}
