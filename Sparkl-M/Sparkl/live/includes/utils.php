<?php




/**
 * pr - pr($array): print_r($array,true) IF ARRAY - otherwise return as is (intended for use in echo or other output)
 *
 * @param $array
 * @return mixed|string|true
 */
function pr($array){
    return (is_array($array)) ? print_r($array,true) : $array ;
}

/**
 * achk - achk($array): check if array exists & valid. if so, return it - otherwise return false (or provided default)
 *
 * @param $array
 * @param $emptyreturn
 * @return void
 */
function achk($array,$emptyreturn=false){
    $ret = (isset($array) && is_array($array) &&isset($array[0])) ? $array : $emptyreturn ;
}

function aflat($array,$indice=0,$emptyreturn=''){
    return (isset($array[$indice])) ? $array[$indice] : $emptyreturn;
}

function vchk($value,$type='s',$emptyreturn=''){
    $emptyreturn = ($emptyreturn==='' && $type==='n') ? 0 : (($type==='b' && $emptyreturn==='') ? false : $emptyreturn ) ;
    return (isset($value)) ? $value : $emptyreturn ;
}

/**
 * req - req['key']: check for a request var and return it.  If empty, it returns '', 0 or supplied default
 *
 * @param $key
 * @param $type             if left as default - req returns '' if var is empty.  if set to 'n', it returns 0
 * @param $emptyreturn      any desired OVERRIDE to the $type param in case of empty
 * @return mixed | any
 */
function req($key,$type='s',$emptyreturn=''){
    $emptyreturn = ($emptyreturn!='') ? $emptyreturn : (($type==='n') ? 0 : '');
    return (isset($_REQUEST[$key]) && $_REQUEST[$key]!='') ? $_REQUEST[$key] : $emptyreturn ;
}

/**
 * sess - sess('key') checks if any session var exists and returns its value.
 *        If empty, it returns '', 0 or supplied default
 *
 * @param $key          optional ONLY when $aNestKeys provided
 * @param $type         default = 's' returns '' if the session val is empty
 *                                'n' returns 0 instead of ''
 * @param $emptyreturn  overrides $type if a custom value is desired on empty return
 * @param $aNestKeys    traverses session and safely returns valid value if exists.
 *                      nested key names should be provided in a basic indexed array
 *                      so:         ['app','user','userid']
 *                      returns:    $_SESSION['app']['user']['userid']
 *
 * @return array|int|mixed|string
 */
function sess($key='',$type='str',$emptyreturn='',$aNestKeys=false){
    $emptyreturn = ($emptyreturn!=='') ? $emptyreturn : (($type==='num') ? 0 : '');
    if($aNestKeys!==false && is_array($aNestKeys)){

        $arrcheck = $_SESSION;
        $bValid = (isset($arrcheck)) ? true : false;
        for($i=0;$i<count($aNestKeys);$i++){

            if($bValid && isset($arrcheck[$aNestKeys[$i]])){
                $bValid = true;
                $arrcheck = $arrcheck[$aNestKeys[$i]];
            }
            else{
                $bValid = false;
            }
        }
        $ret = ($bValid) ? $arrcheck : $emptyreturn;

    }
    else{
        $ret= (isset($_SESSION[$key])) ? $_SESSION[$key] : $emptyreturn ;
    }
    return $ret;
}


function cRPL($content){
    $grep = GREP;

    //  exit(pr($content,true) + pr($grep,true));


    $ret = $content;
    $retArr = array();
    if(is_array($content) || is_object($content)){

        foreach($content as $key=>$val){
            $item = $val;
            foreach($grep as $k=>$v){
                $item = str_replace($k,$v,$item);
                //  $retArr[] = "replace $k with $v in $item <br> \n";
            }
            $retArr[$key] = $item;
        }
        $ret = $retArr;
        // pr($ret,true);
    }
    else{
        $retStr = $content;
        foreach($grep as $k=>$v){
            $retStr = str_replace($k,$v,$retStr);
        }
        $ret = $retStr;
    }
    // return pr($content,true) + pr($grep,true);
    //exit("cRPL-content:" . pr($content,true) . " cRPL-grep:" . pr($grep,true). " cRPL-retarr:" . pr($retArr,true));
    return $ret;
}

?>
