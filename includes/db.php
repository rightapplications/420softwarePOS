<?php
class db {
    static public $host ;
    static private $user ;
    static private $password ;
    static private $encoding;
    static private $dbname ;
    static $link ;
    static private $iDebugLevel;

    static function connect ($aConf) {
        db::$host = $aConf['host'] ;
        db::$user = $aConf['user'] ;
        db::$password = $aConf['password'] ;
        db::$dbname = $aConf['database'] ;
        db::$encoding = @$aConf['encoding'] ;
        db::$iDebugLevel = 0;
        db::$link = @mysql_connect(db::$host, db::$user, db::$password) ;
        if (FALSE === db::$link)
            die('Can\'t create connection with MySQL') ;
        if (db::$dbname != '')
            db::select_db (db::$dbname) ;
        if (db::$encoding)
            @mysql_query("SET NAMES ".db::$encoding.";");				
    }

    static private function select_db ($dbname) {
        db::$dbname = $dbname ;
        $ret = @mysql_select_db (db::$dbname, db::$link) ;
        if (FALSE === $ret)
            die(mysql_error(db::$link)) ;
    }

    static function query($sQuery, $aParams = NULL, $isDebug = FALSE) {
        $sQuery = db::_prepeare($sQuery, $aParams, $isDebug);
        $result = mysql_query($sQuery, db::$link) ;
        if ($isDebug || db::$iDebugLevel > 0) {
                dump($sQuery);
                dump(mysql_affected_rows());
                dump(mysql_error());
        }
        if ($result == false) {
            return false;
        }
        return $result;
    }

    static function get($sQuery, $aParams = NULL, $isDebug = FALSE, $NO_HTML_SPESIAL_CHARS = FALSE, $use_id = false) {
        $result = db::query($sQuery, $aParams, $isDebug);
        $aResult = array();
        while ($aRow = @mysql_fetch_assoc($result)) {
            if ($NO_HTML_SPESIAL_CHARS) {
                foreach ($aRow as $key => $val) {
                    $aRow[$key] = @str_replace(array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $val);
                }
            }
            if ($use_id===false) {
                $aResult[] = @$aRow;
            } elseif ($use_id===true) {
                $aResult[@$aRow['id']] = @$aRow;
            } else {
                $aResult[@$aRow[$use_id]] = @$aRow;
            }
        }
        @mysql_free_result($result);
        return @$aResult;
    }
    
    static function get_pager($sQuery,$perPage = '',$aParams = NULL,$isDebug = false){
        global $sPageListing;
        global $SEARCH_ROWS_MAX;
        if($perPage){
            $SEARCH_ROWS_MAX = $perPage ;
        }
        $countQuery = preg_replace("/(SELECT )[\w\s,.\*\+\-\/]*( FROM)/", "SELECT COUNT(*) FROM", $sQuery, 1);
        $iCount = db::get_one($countQuery);
        if (!$iCount) {
            if ($isDebug || db::$iDebugLevel > 0) {
                dump($sQuery);
                dump(mysql_affected_rows());
                dump(mysql_error());
            }
            return null;
        }
        include_once (ABS.'includes/page_listing.php');
        $limit = " LIMIT ".$start.", ".$SEARCH_ROWS_MAX;
        $sQuery = $sQuery.$limit;
        $result = db::get($sQuery,$aParams,$isDebug);
        if($result){
            return $result;
        }
        else{
            return null;
        }
    }

    static function get_one($sQuery, $aParams = NULL, $isDebug = FALSE, $NO_HTML_SPESIAL_CHARS = FALSE) {
        $result = db::query($sQuery, $aParams, $isDebug);
        if (!$result) {
            return null;
        }
        $aRow = @mysql_fetch_row($result);
        @mysql_free_result($result);
        if ($NO_HTML_SPESIAL_CHARS) {
            return @str_replace(array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $aRow[0]);
        }
        return @$aRow[0];
    }


    static function get_row($sQuery, $aParams = NULL, $isDebug = FALSE, $NO_HTML_SPESIAL_CHARS = FALSE) {

        $result = db::query($sQuery, $aParams, $isDebug);
        if (!$result) {
            return null;
        }
        $aRow = @mysql_fetch_assoc($result);
        if ($NO_HTML_SPESIAL_CHARS) {
            foreach ($aRow as $key => $val) {
                $aRow[$key] = @str_replace(array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $val);
            }
        }
        @mysql_free_result($result);
        return @$aRow;
    }
    
    static function get_last_id(){
        $result = mysql_insert_id();
        return $result;
    }


    static private function _prepeare($sQuery, $aParams = NULL, $isDebug = FALSE) {
        if ($aParams !== NULL) {
            $iCount = count($aParams);
           
            if (preg_match_all("/~~/m", $sQuery, $as) == $iCount) {
                foreach ($aParams as $key => $val) {
                    
                    if ($val === NULL  ||  ( is_string($val) && $val == 'NULL') ) {
                        $aParams[$key] = "NULL";
                    } elseif (is_string($val)) { 
                        $aParams[$key] = preg_replace( "/~~/", "###___###", $aParams[$key]);
                        
                        if (!get_magic_quotes_gpc()) {
                            $aParams[$key] = '\''.mysql_real_escape_string($val).'\'';
                        }
                        else{
                            $val = stripslashes($val);
                            $aParams[$key] = '\''.mysql_real_escape_string($val).'\'';  
                        }
                        $aParams[$key] = str_replace('\\','\\\\',$aParams[$key]);
                        $aParams[$key] = str_replace('$','\$',$aParams[$key]);
                    }
                }
                $aPattern = @array_fill( 0, $iCount, '/~~/'); 
                $sQuery = @preg_replace( $aPattern, $aParams, $sQuery, 1);
                $sQuery = @preg_replace( "/###___###/", "~~", $sQuery);
            } else {
                if ($isDebug)
                    die("Database error: query '$sQuery' is not match ".var_export($aParams, true));
                else
                    die("Database error: query is not match array");
            }
        }
        return $sQuery;
    }
    
    static function clear($txt){
        $txt = str_ireplace(' WHERE ','',$txt);
        $txt = str_ireplace(' OR ','',$txt);
        $txt = str_ireplace(' DELETE ','',$txt);
        $txt = str_ireplace(' SELECT ','',$txt);
        $txt = str_ireplace(' UPDATE ','',$txt);
        $txt = str_ireplace(' SET ','',$txt);
        $txt = str_ireplace(' ALTER ','',$txt);
        $txt = str_ireplace(' DROP ','',$txt);
        $txt = str_ireplace('<script','',$txt);
        return $txt;
    }

}
?>