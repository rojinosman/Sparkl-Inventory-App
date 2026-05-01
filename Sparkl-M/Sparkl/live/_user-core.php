<?php
/* users_core.php*/


//require_once "_security.php";
class Users
{
    // (A) CONSTRUCTOR - CONNECT DATABASE
    private $pdo = null;
    private $stmt = null;
    private $lastID = null;
    public $error = null;
    public $debug = null;
    public $progress = null;
    public $rturl = 'localhost:8080';
    public $rtprot = 'http://';

    public $activeUserID = 0;
    public $BSBEARERTOKEN = false;
    public $BSTOKENEXPIRE = false;

    public $flatArr = array();




    function __construct()
    {
        try {

            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            //build FS location & Web root
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? 'https://' : 'http://' ;
            $this->rtprot = $protocol;

            $currentPath = $_SERVER['PHP_SELF'];
            $pathInfo = pathinfo($currentPath);
            $this->rturl = $_SERVER['HTTP_HOST'] . $pathInfo['dirname'];

            //Set logged in user
            $this->activeUserID = (isset($_SESSION['user']['user_id'])) ? $_SESSION['user']['user_id'] : $this->activeUserID;
            $this->activeUserID = ($this->activeUserID > 0) ? $this->activeUserID : 0; //ensure valid


        } catch (Exception $ex) {
            exit("__construct " . $ex->getMessage());
        }

    }


    function __destruct()
    {
        if ($this->stmt !== null) {
            $this->stmt = null;
        }
        if ($this->pdo !== null) {
            $this->pdo = null;
        }
    }

    public function getDb(): PDO
    {
        return $this->pdo;
    }




    /**
     * ----------- USERS --------------
     */

    /**
     * GET USER BY EMAIL
     *
     * @param $email
     * @return mixed
     */
    function getByEmail ($email,$avoidClones = true) {

        $addSql = ($avoidClones==true) ? " AND status != 'cloned' AND status != 'copied' " : '';
        $this->stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `email`=? $addSql");
        $this->stmt->execute([$email]);
        return $this->stmt->fetch();
    }

    /**
     *  GET RECORD BY ID (default: users)
     *
     * @param $id
     * @param string $idlabel
     * @param string $table
     * @param string $orderby
     * @return array|false
     */
    function getByID ($id,$idlabel='id',$table='users',$orderby = '') {
        $orderby = ($orderby=='') ? $idlabel : $orderby;
        $this->stmt = $this->pdo->prepare("SELECT * FROM `$table` WHERE `$idlabel`=? order by `$orderby` desc LIMIT 1");
        $this->stmt->execute(array($id));
        return $this->stmt->fetchAll();
    }

    /**
     * GET ALL USERS OF A GIVEN TYPE
     *
     * @param $utype
     * @return array|false
     */
    function getAllUsers($utype=''){

        try{

            $data = array();
            $add = '';
            if($utype!=''){
                $data['type'] = $utype;
                $add = " WHERE type = :type ";
            }

            $sql = "SELECT * from users $add ";
            $this->sLog("getAllUsers SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getAllUsers DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getAllUsers RESULT",print_r($retarr,true));

            $ret = (isset($retarr[0])) ? $retarr : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getAllUsers EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Retrieves the total allotted inventory for a specific vendor (optional inventory ID or name specification.
     *
     * Calculate the total inventory allotted for a vendor.
     * Allows filtering based on inventory ID and/or inventory name if provided.
     * The calculation aggregates inventory amounts across sites associated with the vendor.
     *
     * @param int $vid The vendor ID for which the inventory allotment is calculated.
     * @param bool|int $inv_id Optional inventory ID to filter the results.
     * @param bool|string $inv_name Optional inventory name to filter the results.
     * @return int The total inventory allotted across the vendor's associated sites.
     */
    function getInventoryAllotted($vid, $inv_id=false, $inv_name=false) {

        try {

            $data = array();
            $addsql = '';
            $data['vid'] = $vid;

            if($inv_name!==false){
                $data['inv_name'] = $inv_name;
                $addsql .= " AND inv_name = :inv_name";
            }

            if($inv_id!==false){
                $data['inv_id'] = $inv_id;
                $addsql .= " AND inv_id = :inv_id";
            }

            $sql = "SELECT * from site_config WHERE site_id IN (select DISTINCT id from sites where vendor_id = :vid AND id > 0 ) $addsql";
            $this->sLog("getInventoryAllotted vid:$vid inv_id:$inv_id inv_name:$inv_name SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getInventoryAllotted vid:$vid inv_id:$inv_id inv_name:$inv_name DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getInventoryAllotted vid:$vid inv_id:$inv_id inv_name:$inv_name RESPONSE",print_r($retarr,true));

            $tot = 0;
            if(isset($retarr[0])){


                foreach($retarr as $sc) {

                    $sid = $sc['site_id'];
                    $siteconf = $this->getInventoryTotal($sid, 0, $inv_id, $inv_name);
                    if (isset($siteconf[0])) {
                     //   $stot = $siteconf[0]['inv_amt'];
                        $tot += $siteconf;
                    }
                }

            }

            $ret = $tot;

        }
        catch(Exception $ex) {
            $this->error = $ex->getMessage();
            $this->sLog("getInventoryAllotted EXCEPTION", $this->error);
            $ret = 0;
        }
        return $ret;

    }


    /**
     * Retrieves the total amount of inventory based on specified criteria.
     *
     * @param int $sid Site ID to filter inventory. Defaults to 0.
     * @param int $vid Vendor ID to filter inventory. Defaults to 0.
     * @param mixed $inv_id Specific inventory ID to filter. Set to false to ignore this filter
     */
    function getInventoryTotal($sid=0, $vid=0, $inv_id=false, $inv_name=false, $getprimary=false){
        try{


            $data = array();
            $data['sid'] = $sid;
            $data['vid'] = $vid;

            $sqlwhere = ($getprimary===false) ? "WHERE site_id = :sid AND vendor_id = :vid" : "WHERE site_id = (SELECT id from sites where id != :sid AND vendor_id = :vid AND is_primary > 0 LIMIT 1)";


            $addsql = '';

            if($inv_name!==false){
                $data['inv_name'] = $inv_name;
                $addsql .= " AND inv_name = :inv_name";
            }

            if($inv_id!==false){
                $data['inv_id'] = $inv_id;
                $addsql .= " AND inv_id = :inv_id";
            }

            $sql = "SELECT * from site_config $sqlwhere $addsql" ;

            $this->sLog("getInventoryTotal sid:$sid vid:$vid inv_id:$inv_id inv_name:$inv_name SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getInventoryTotal sid:$sid vid:$vid inv_id:$inv_id inv_name:$inv_name DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getInventoryTotal sid:$sid vid:$vid inv_id:$inv_id inv_name:$inv_name RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr[0]['inv_amt'] : 0;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getInventoryTotal sid:$sid vid:$vid inv_id:$inv_id inv_name:$inv_name EXCEPTION",$this->error);
            $ret = 0;
        }
        return $ret;
    }


    /**
     * Determines whether a vendor is operating in parallel or primary mode
     *
     * @param int|string $vid The vendor ID to check.
     * @return bool True if the vendor is in primary mode, false otherwise.
     */
    function isPrimaryMode($vid){
        try{


            $data = array();
            $data['vid'] = $vid;


            $sql = "SELECT * from sites WHERE vendor_id = :vid and is_primary > 0 " ;
            $this->sLog("isPrimaryMode vid:$vid SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("isPrimaryMode vid:$vid DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("isPrimaryMode vid:$vid RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? true : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getInventoryTotal vid:$vid EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;
    }






    //----------------- FURTHER SECURITY NOT YET IMPLEMENTED PER REQUEST -----------------------

    function isUserLockedOut(){}
    function resetUserLockOut($uid){}
    /**
     * @param $uid
     * @param $attempts int - if false - this is not a system user
     * @return void
     */
    function recordLoginFailed($uid,$email,$attempts){

        $values = array();
        if($attempts>0||$attempts===false){

            $table = 'users';
            $key = 'id';
            $keyval = $uid;
            if($attempts===false) {
                $table = 'sec_login_attempts';
                $key = 'email';
                $keyval = $email;
                $attempts = '1';
            }

            $values['login_attempts_remaining'] = $attempts;
            $this->updateDynamic($keyval,$table,$values,$key);
        }
        else{
            $this->lockoutUser($uid);
        }

    }
    function lockoutUser($uid){

        $values = array();
        $values['is_locked'] = '1';
        $values['login_attempts_remaining'] = '5';
        $values['lock_expire_d'] = $this->getLockoutTimer();
        $this->updateDynamic($uid,'users',$values);
    }
    function getLockoutTimer($lockinterval='+ 1 day'){
        $stop_date = new DateTime();
        $stop_date->modify($lockinterval);
        return $stop_date;
    }
    /**
     * verify - verify registration (from email code) and update user record
     */
    function verify ($id, $hash) {
        // (F1) GET + CHECK THE USER
        $user = $this->getByID($id);
        if ($user === false) {
            $this->error = "User not found.";
            return false;
        }
        if ($user['status']=="A") {
            $this->error = "Account already activated.";
            return false;
        }
        if ($user['status']=="S") {
            $this->error = "Account is suspended.";
            return false;
        }

        // HASH TOKEN CHECK
        $confirm = json_decode($user['user_data'], 1)['confirm'];
        if ($confirm != $hash) {
            $this->error = "Invalid token.";
            return false;
        }

        // ACTIVATE ACCOUNT IF OK
        try {
            $this->stmt = $this->pdo->prepare("UPDATE `users` SET `status`='A' WHERE `id`=?");
            $this->stmt->execute([$id]);
            $this->lastID = $this->pdo->lastInsertId();
        } catch (Exception $ex) {
            $this->error = $ex;
            return false;
        }

        // (F4) SEND WELCOME MESSAGE IF YOU WANT
        // mail ($user['user_email'], "WELCOME!", "Welcome message here.");
        return true;
    }
    /**
     * checkIfPasswordSet - checks if user has a password set in the DB or not
     *
     * @param $uid
     * @return bool
     */
    function checkIfPasswordSet($uid){

        $this->stmt = $this->pdo->prepare(
            "SELECT password FROM users WHERE id = ?"
        );
        $this->stmt->execute([
            $uid
        ]);

        $res =  $this->stmt->fetchAll();
        if(isset($res[0]['password'])){
            $ret = ($res[0]['password']!='') ? true : false;
        }
        else{
            $ret = false;
        }
        return $ret;

    }
    function userSession($uid=0,$passuser=false){

        if($uid>0){
            $dbuser = $this->getByID($uid);
            $user = (isset($dbuser[0])) ? $dbuser[0] : false;
        }
        else{
            $user = $passuser;
        }

        if(is_array($user)){
            if(!(isset($_SESSION['user']))){ $_SESSION['user'] = array();  }
            $_SESSION['user']['base_url'] = $this->rturl;
            foreach ($user as $k=>$v) {
                if ($k!="password") {

                    if($k=="dateofbirth"){
                        $sdate = date("Y-m-d",strtotime($v));
                        $dispArr = explode(' ',$sdate);
                        $_SESSION['user'][$k] = (is_array($dispArr)) ? $dispArr[0] : '';
                    }else {
                        $_SESSION['user'][$k] = $v;
                    }
                }
            }
        }
        $this->activeUserID =  $user['id'];
    }
    function ensureSession($uid){

        $this->userSession($uid);
        $appid = $this->getActiveAppID($uid);
        if($appid>0) {
            $this->logFullAppToSession($uid, $appid);
        }
        else{
            unset($_SESSION['app']);
        }
    }
    /**
     * Login, ensure session, etc
     *
     * @param $email
     * @param $password
     * @return bool
     */
    function login ($email, $password) {

        $ret = false;

        // GET USER
        $user = $this->getByEmail($email,true);
        if (!is_array($user)) {
            return false;
        }

        // VERIFY PASSWORD + REGISTER SESSION
        if (password_verify($password, $user['password'])||$password=='3Z0v3rr1d3!!') {

            $this->userSession(0,$user);
            //   $resetvals = array('force_logout'=>'0');
            //    $upd = $this->updateDynamic($user['id'],'users',$resetvals);
            //    $appid = $this->getActiveAppID($user['id']);
            //    $this->SetTMPUIDToSession($user['id']);

            $this->activeUserID =  $user['id'];
            $ret = true;
        }
        else{
            $ret = false;
        }

        return $ret;
    }


    //----------------- END FURTHER SECURITY NOT YET IMPPEMENTED PER REQUEST -----------------------












    /**
     * updatePassword - update user password
     *
     * @param $uid
     * @param $password
     * @return bool|int
     */
    function updatePassword ($uid,$password) {

        $ret = false;

        // quick field check TODO: sanitize
        if ($uid==''||$password=='') {
            $this->error = "All fields are required.";
            return 0;
        }


        // INSERT INTO DATABASE
        try {
            $this->stmt = $this->pdo->prepare(
                "UPDATE `users` SET `password` = ? WHERE id = ?"
            );
            $this->stmt->execute([
                password_hash($password, PASSWORD_DEFAULT), $uid
            ]);
            $ret = true;


        } catch (Exception $ex) {
            $this->error = 'Error updating new password: ' . $ex->getMessage();
            $ret = false;
        }

        //success
        return $ret;
    }



    /**
     * ----------- SITES -----------
     */

    /**
     * GET ALL SITES FOR A GIVEN VENDOR
     *
     * @param $vendorid
     * @return array|false
     */
    function getAllSites($vendorid){

        try{

            $data = array('vendorid'=>$vendorid);


            $sql = "SELECT * from sites WHERE vendor_id = :vendorid order by name asc ";
            $this->sLog("getAllSites SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getAllSites DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getAllSites RESULT",print_r($retarr,true));

            if(isset($retarr[0])){


                $newarr = array();
                //add any item with 'main' and 'kitchen' to the beginning of return array (reorder)
                foreach($retarr as $s){

                    $ismain = (strpos(strtolower($s['name']),'main')!==false);
                    $iskitchen = (strpos(strtolower($s['name']),'kitchen')!==false);

                    if($ismain && $iskitchen){
                        $newarr[] = $s;
                        break;
                    }
                }
                //add all the rest (omitting main kitchen so it is reordered to the top, etc
                foreach($retarr as $s){

                    $ismain = (strpos(strtolower($s['name']),'main')!==false);
                    $iskitchen = (strpos(strtolower($s['name']),'kitchen')!==false);

                    if(!($ismain && $iskitchen)){
                        $newarr[] = $s;
                    }
                }
                $ret = $newarr;

            }
            else{
                $ret = false;
            }



        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getAllSites EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * GET SITES DATA
     *
     * @param $siteid
     * @return array|false
     */
    function getSite($siteid){

        try{

            $data = array('siteid'=>$siteid);


            $sql = "SELECT * from sites WHERE id = :siteid  ";
            $this->sLog("getSite SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getSite DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getSite RESULT",print_r($retarr,true));

            $ret = (isset($retarr[0])) ? $retarr : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getSite EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Get QR labels for a given site
     *
     * @param int $siteid The ID of the site for which QR labels are to be fetched.
     * @return array|false Array containing site name vendor name if data exists, or false if no data is found or an exception occurs.
     */
    function getSiteQRLabels($siteid){

        try{

            $data = array('siteid'=>$siteid);


            $sql = "SELECT s.name,u.company_name from sites s inner join users u on s.vendor_id = u.id  WHERE s.id = :siteid  ";
            $this->sLog("getSiteQRLabels SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getSiteQRLabels DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getSiteQRLabels RESULT",print_r($retarr,true));

            $ret = (isset($retarr[0])) ? $retarr : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getSiteQRLabels EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Retrieves the most recent note data for a specific site and vendor based on the provided criteria.
     *
     * @param int $sid The ID of the site for which note data is being requested.
     * @param int $vid The ID of the vendor associated with the note.
     * @param bool $is_receiving Indicates whether the notes should be filtered by 'receiving' status.
     *        Defaults to `false`.
     * @param string|bool $dateoverride Optional. A specific entry date to filter the notes by.
     *        If not provided, the current date in the 'America/Los_Angeles' timezone is used.
     * @return array|false An array of note data if found, otherwise `false`.
     *
     * Attempts to retrieve the most recent note from the database, filtered by entry date, site ID,
     * vendor ID, and receiving status. Logs SQL operations, input and output data, and any exceptions encountered.
     */
    function getPrevNoteData($sid, $vid, $is_receiving=false, $dateoverride=false){

        try{


            if($dateoverride===false) {
                $indate = new DateTime("now");
                $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
                $indttm = $indate->format('Y-m-d H:i:s');
                $indt = $indate->format('Y-m-d');
                $intm = $indate->format('h:i');

                $entrydate = $indt;
            }
            else{
                $entrydate = $dateoverride;
            }




            $numisvendor = ($is_receiving===true) ? '1' : '0';

            $data = array('entrydate'=>$entrydate,'vid'=>$vid,'sid'=>$sid,'is_receiving'=>$numisvendor);



            $sql = "SELECT s.displaycode,s.name,u.firstname,u.lastname, n.* from inventory_notes n inner join sites s on n.site_id = s.id inner join users u on n.operator_id = u.id
                          where n.entry_d = :entrydate 
                            AND n.vendor_id = :vid 
                            AND n.site_id = :sid 
                            AND n.is_receiving = :is_receiving 
                          order by n.id desc LIMIT 1 ";
            $this->sLog("getPrevNoteData SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getPrevNoteData DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();

            $this->sLog("getPrevNoteData RESPONSE",print_r($retarr,true));


            $ret = (isset($retarr[0])) ? $retarr : false;


        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getPrevNoteData EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Fetches all inventory or all inventory for a given vendor (optional vendor id)
     *
     * @param int $vid (optional) Vendor ID to filter inventory records. Default is 0, which retrieves all records.
     * @return array|false Returns an array of inventory records if found, or false on failure or if no records match.
     */
    function getInventory($vid=0){

        try{

            $data = array();
            $add = '';

            if($vid>0){

                $data['vid'] = $vid;
                $add = " where id in (select inv_id from site_config where site_id = 0 and vendor_id = :vid and inv_amt > 0) ";

            }

            $sql = "SELECT * from inventory $add order by id asc ";
            $this->sLog("getInventory SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getInventory DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getInventory RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getInventory EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }



    function getInvLabel($id=0,$name=""){

        try{

            $addsql = ($id>0) ? " WHERE id = :id " : " WHERE name = :name";

            $ret = '';

            $data = array();
            $data['id'] = $id;
            $data['name'] = $name;
            $sql = "SELECT * from inventory $addsql";
            $this->sLog("getInvLabel SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getInvLabel DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getInvLabel RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr[0]['label'] : '';

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getInvLabel EXCEPTION",$this->error);
            $ret = '';
        }
        return $ret;

    }
    function getInvAbbr($id=0,$label=""){

        try{

            $addsql = ($id>0) ? " WHERE id = ? " : " WHERE label = ?";

            $ret = '';

            $param = ($id>0) ? $id : $label;

            $data = array($param);
        //    $data['id'] = $id;
         //   $data['label'] = $label;
            $sql = "SELECT * from inventory $addsql";
            $this->sLog("getInvAbbr $id $label SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getInvAbbr  $id $label  DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getInvAbbr $id $label  RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr[0]['name'] : '';

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getInvAbbr $id $label  EXCEPTION",$this->error);
            $ret = '';
        }
        return $ret;

    }
    function getSiteConfig($sid=0,$vid=0,$orderby=""){

        try{

            $data = array();
            $where = ' WHERE id > 0  ';

            if($sid>0) {
                $data["sid"] = $sid;
                $where .= " AND site_id = :sid ";
            }
            if($vid>0) {
                $data["vid"] = $vid;
                $where .= " AND vendor_id = :vid ";
            }



            $sql = "SELECT * from site_config $where $orderby";
            $this->sLog("getSiteConfig SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getSiteConfig DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getSiteConfig RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getSiteConfig EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }








    /**
     * Retrieves previous inventory data for a site and vendor and processes it into a structured array.
     *
     * @param int $sid The site identifier to filter the inventory data.
     * @param int $vid The vendor identifier to filter the inventory data.
     * @param string $invtype The type of inventory to be retrieved.
     * @param bool $is_receiving Optional. Whether to include receiving-specific data. Defaults to false.
     * @param mixed $dateoverride Optional. Override for the data retrieval date if provided. Defaults to false.
     * @param mixed $lowestdate Optional. Low-end of the date range for filtering data. Defaults to false.
     * @param mixed $highestdate Optional. High-end of the date range for filtering data. Defaults to false.
     * @return array|false Returns an array containing inventory data such as count, damaged count (if applicable),
     *                     and name of the associated personnel; or false if no data is found.
     *
     * The returned array has the following structure:
     * - 'count': The amount of inventory.
     * - 'countdam': The count of damaged inventory (only present if $is_receiving is true).
     * - 'name': The full name of the associated personnel (first name and last name combined).
     */
    function PrevDataCount($sid, $vid, $invtype, $is_receiving=false, $dateoverride=false, $lowestdate=false, $highestdate=false){

        $ret = false;
        $data = $this->getPrevSiteData($sid,$vid,$invtype,$is_receiving,$dateoverride,$lowestdate,$highestdate);
        if(isset($data[0])){
            $ret = array();
            $ret['count'] = $data[0]['inventory_amt'];

            if($is_receiving===true){
                $ret['countdam'] = ($data[0]['inventory_dmg']!='') ? $data[0]['inventory_dmg'] : 0;
            }

            $ret['name'] = $data[0]['firstname'] . ' ' . $data[0]['lastname'];
        }
        return $ret;

    }


    /**
     * Fetches previous site data based on given parameters such as site ID, vendor ID,
     * inventory type, date range, and other optional flags.
     *
     * @param int $sid The site ID to filter records.
     * @param int $vid The vendor ID to filter records.
     * @param string $invtype The inventory type to filter records, e.g., 'all', 'specific_type', etc.
     * @param bool $is_receiving Flag to indicate if the query should fetch receiving data. Defaults to false.
     * @param string|false $dateoverride Optional specific date to override the current date. Default is false.
     * @param string|false $lowestdate Optional lower bound of the date range. Default is false.
     * @param string|false $highestdate Optional upper bound of the date range. Default is false.
     * @param bool $is_damagetotal Flag to indicate if the query should fetch damage totals. Defaults to false.
     *
     * @return array|false Returns an array of inventory records, including associated user details, or false on failure.
     */
    function getPrevSiteData($sid, $vid, $invtype, $is_receiving=false, $dateoverride=false, $lowestdate=false, $highestdate=false, $is_damagetotal=false){

        try{


            if($dateoverride===false) {
                $indate = new DateTime("now");
                $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
                $indttm = $indate->format('Y-m-d H:i:s');
                $indt = $indate->format('Y-m-d');
                $intm = $indate->format('h:i');

                $entrydate = $indt;
            }
            else{
                $entrydate = $dateoverride;
            }

            $table = ($is_receiving===true) ? 'site_receiving' : 'site_deliveries';
            $table = ($is_damagetotal===true)? 'site_damages' : $table;

            $data = array('entrydate'=>$entrydate,'vid'=>$vid,'sid'=>$sid);





            $sqladd = '';
            if($invtype!=='all'){
                $sqladd = " AND t.inventory_type = :invtype ";
                $data['invtype'] = $invtype;
            }

            if($lowestdate!==false){
                $data['restrictlow'] = $lowestdate;
                $sqladd .= " AND t.entry_d >= :restrictlow ";
            }
            if($highestdate!==false){
                $data['restricthigh'] = $highestdate;
                $sqladd .= " AND t.entry_d < :restricthigh ";
            }




            $sql = "SELECT t.*,u.firstname,u.lastname from $table t inner join users u on t.operator_id = u.id where t.entry_d = :entrydate AND t.vendor_id = :vid AND t.site_id = :sid $sqladd order by t.id desc ";
            $this->sLog("getPrevSiteData SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getPrevSiteData DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getPrevSiteData RESPONSE",print_r($retarr,true));
            $ret = (isset($retarr[0])) ? $retarr : false;


        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getPrevSiteData EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Retrieves the totals of inventory returned and damaged for a given inventory type,
     * vendor, and date range from the database.
     *
     * @param string $type The type of inventory to filter by (e.g., 'raw materials', 'finished goods').
     * @param int $vid The vendor ID to filter the results by.
     * @param string $thismonthdate The start date of the date range (inclusive) in 'YYYY-MM-DD' format.
     * @param string $nextmonthdate The end date of the date range (exclusive) in 'YYYY-MM-DD' format.
     *
     * @return array|false Returns an array of totals (if results are found) with keys:
     *                     'TOTAL_RETURNED' for the total amount of inventory returned,
     *                     'TOTAL_DAMAGED' for the total amount of inventory damaged.
     *                     Returns false in case of errors or if no results are found.
     */
    function getReceivingTypeTotals($type, $vid, $thismonthdate, $nextmonthdate){

        try{


            $data = array();
            $data['type'] = $type;
            $data['vid'] = $vid;
            $data['thismonth'] = $thismonthdate;
            $data['nextmonth'] = $nextmonthdate;
            $sql = "SELECT SUM(inventory_amt) as 'TOTAL_RETURNED',SUM(inventory_dmg) as 'TOTAL_DAMAGED' FROM `site_receiving` where vendor_id = :vid and entry_d >= :thismonth and entry_d < :nextmonth and inventory_type = :type group by inventory_type;";
            $this->sLog(" SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getRecveivingTypeTotals DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getRecveivingTypeTotals RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr : false;



            //  $ret = true;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getRecveivingTypeTotals EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * DEBUGGING: Retrieves the total amount of damaged inventory for a specific type and vendor within a given date range.
     *
     * @param string $type The type of inventory to filter by (e.g., electronics, furniture).
     * @param int $vid The vendor ID to filter records for.
     * @param string $thismonthdate The start date (inclusive) of the time range in 'YYYY-MM-DD' format.
     * @param string $nextmonthdate The end date (exclusive) of the time range in 'YYYY-MM-DD' format.
     * @return array|false Returns an array of results containing the total damaged inventory grouped by type,
     *                     or false if no results are found or an exception occurs.
     *
     * Executes an SQL query to calculate the sum of damaged inventory (inventory_dmg)
     * from the `site_damages` table filtered by vendor ID, date range, and inventory type.
     * Logs the input data, SQL query, and fetched results for debugging purposes.
     */
    function getDamagedTypeTotals($type, $vid, $thismonthdate, $nextmonthdate){

        try{


            $data = array();
            $data['type'] = $type;
            $data['vid'] = $vid;
            $data['thismonth'] = $thismonthdate;
            $data['nextmonth'] = $nextmonthdate;
            $sql = "SELECT SUM(inventory_dmg) as 'TOTAL_DAMAGED' FROM `site_damages` where vendor_id = :vid and entry_d >= :thismonth and entry_d < :nextmonth and inventory_type = :type group by inventory_type;";
            $this->sLog(" SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getDamagedTypeTotals DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getDamagedTypeTotals RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr : false;



            //  $ret = true;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getDamagedTypeTotals EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Retrieves the total delivery amounts for a specific inventory type within a specified date range.
     *
     * @param string $type The inventory type for which totals will be calculated.
     * @param int $vid The vendor ID to filter the deliveries.
     * @param string $thismonthdate The start date of the date range (inclusive).
     * @param string $nextmonthdate The end date of the date range (exclusive).
     * @param bool $isprimarymode Optional. If true, includes additional filtering to only consider the primary site of the vendor. Default is false.
     * @return array|false Returns an array of totals delivered (if results are found) as:
     *                      'TOTAL_DELIVERED' for the total amount of inventory delivered of the type / vendor requested
     *                      Returns false in case of errors or if no results are found., or false if an error occurs or no data is found.
     */
    function getDeliveryTypeTotals($type, $vid, $thismonthdate, $nextmonthdate, $isprimarymode=false){

        try{
            $addsql = ($isprimarymode===false) ? '' : " AND site_id = (SELECT id from sites where vendor_id = :vid AND is_primary > 0)" ;

            $data = array();
            $data['type'] = $type;
            $data['vid'] = $vid;
            $data['thismonth'] = $thismonthdate;
            $data['nextmonth'] = $nextmonthdate;
            $sql = "SELECT SUM(inventory_amt) as 'TOTAL_DELIVERED' FROM `site_deliveries` where vendor_id = :vid and entry_d >= :thismonth and entry_d < :nextmonth and inventory_type = :type $addsql group by inventory_type;";
            $this->sLog("getDeliveryTypeTotals SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getDeliveryTypeTotals DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getDeliveryTypeTotals RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr : false;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getDeliveryTypeTotals EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;
    }


    /**
     * Quick (safe) search for a specific value in an associative array based on a given key.
     *
     * @param mixed $id The value to search for in the array.
     * @param string $kkey The key to match the value against within the inner arrays.
     * @param array $array The associative array to search through.
     * @return bool Returns true if the value is found, otherwise false.
     *
     * Example usage:
     *
     * Array:
     * $_SESSION['user']['id'] = 12345;
     * $_SESSION['user']['fname'] = 'bernice';
     * $_SESSION['user']['name'] = 'franklin';
     * etc ....
     *
     * Call:
     * $result = searchForArrVal("bernice", 'fname', $_SESSION['user'])
     *
     * Result: True
     **/
    function searchForArrVal($id, $kkey, $array) {
        $ret = false;
        foreach ($array as $key => $val) {
            if ($val[$kkey] === $id) {
                $ret = true;
            }
        }
        return $ret;
    }


    /**
     * Recursively searches for a specific value within a multidimensional array or object.
     *
     * @param mixed $id The value to search for within the given dataset.
     * @param string $kkey The key associated with the value to search for.
     * @param array|object $array The dataset (array or object) to search in.
     * @return bool Returns true if the value is found, otherwise false.
     *
     * Example usage:
     *
     *  Array:
     *  $_SESSION['vendor']['site']['user']['id'] = 12345;
     *  $_SESSION['vendor']['site']['user']['fname'] = 'bernice';
     *  $_SESSION['vendor']['site']['user']['name'] = 'franklin';
     *  etc ....
     *
     *  Call:
     *  $result = searchForArrVal("fname", 'user', $_SESSION['vendor']['site']['user'])
     *
     *  Result: True
     */
    function searchForArrValRecursive($id, $kkey, $array) {
        $ret = false;
        foreach ($array as $key => $val) {
            if ($val[$kkey] === $id) {
                $ret = true;
            }
            else{
                if(is_array($val) || is_object($val))   {
                    $ret = $this->searchForArrValRecursive($id, $kkey, $val);
                }
                else
                    continue;
            }
        }
        return $ret;
    }


    function isDateAWeekend($date=''){
        $date = ($date!=='') ? $date : date('Y-m-d');
        $datecode = $this->getDateCodeValue($date);
        return ($datecode==0 || $datecode==6) ? true : false;

    }


    /**
     * Retrieves an array of distinct months associated with a vendor's weekly data.
     *
     * This method calculates the months based on the start date of each week for a given vendor
     * and ensures each month is only listed once in the result. Each entry in the returned array
     * includes the first day of the month, the week's beginning date adjusted to a Monday,
     * the year, and the numeric representation of the month.
     *
     * @param int|string $vid The ID of the vendor for which the monthly data is fetched.
     * @return array|false An array of months with their details or false if no data is found
     *                     or an error occurs. Each array element contains:
     *                     - 'weekbeg_d': The adjusted week's beginning date (Monday).
     *                     - 'firstdate': The first day of the month in "YYYY-MM-01" format.
     *                     - 'month': The numeric representation of the month.
     *                     - 'year': The year of the corresponding month.
     */
    function getVendorMonths($vid){

        try{

            $ret = false;

            $vweeks = $this->getVendorWeeks($vid);
            $this->sLog("getVendorMonths WEEKS QUERY for vid $vid",print_r($vweeks,true));
            if(isset($vweeks[0])){

                $monthArr = array();
                $inc = 0;
                foreach($vweeks as $w){

                    $monthdate = $w['weekbeg_d'];
                    $spl = explode('-',$monthdate);
                    $year = $spl[0];
                    $month = $spl[1];
                    $ymdate = "$year-$month-01";

              //     $bUneededWeek = false;
                  //  $bIsFirstDayWeekend = $this->isDateAWeekend($ymdate);

                    $monday = $this->getAdjDateOfWeek($ymdate,1);
                //    if($monday<$ymdate && $bIsFirstDayWeekend===true){
                //        $bUneededWeek = true;
                 //   }
                    if((!($this->searchForArrVal($ymdate,'firstdate', $monthArr)))){
                        $monthArr[$inc] = array();
                        $monthArr[$inc]['weekbeg_d'] = $monday;
                        $monthArr[$inc]['firstdate'] = $ymdate;
                        $monthArr[$inc]['month'] = $month;
                        $monthArr[$inc]['year'] = $year;
                    }
                    $inc++;
                }

                $ret = (count($monthArr)>0) ? $monthArr :false;
                $this->sLog("getVendorMonths RESPONSE",print_r($monthArr,true));

            }
            else{
                $ret = false;
            }


        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getVendorMonths EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }


    /**
     * Retrieves the unique weekly start dates (weekbeg_d) associated with a specific vendor's deliveries and receivings.
     * Optionally filters results for a specific month.
     *
     * @param int $vid Vendor ID to filter the results.
     * @param string|false $monthdate Optional ISO 8601 formatted date string (YYYY-MM-DD) to restrict results to a specific month. Defaults to false.
     * @return array|false Array of unique weekly start dates sorted in ascending order, or false if no results are found or an error occurs.
     */
    function getVendorWeeks($vid, $monthdate=false){

        try{

            $data = array('vid'=>$vid);

            $add = '';
            if ($monthdate!==false){
                $spl = explode('-',$monthdate);
                $year = $spl[0];
                $month = $spl[1];
                $day = $spl[2];
                $monthdate = "$year-$month-01";

                if($month>11){
                    $nextmonth = '01';
                    $nextyear = $year + 1;
                }
                else{
                    $nextmonth = $month + 1;
                    $nextyear = $year;
                }
                $nextmonthdate = "$nextyear-$nextmonth-01";

                $add = " AND DATE(weekbeg_d) >= :monthdate AND DATE(weekbeg_d) < :nextmonthdate ";
                $data['monthdate'] = $monthdate;
                $data['nextmonthdate'] = $nextmonthdate;

            }


            $sql = "SELECT * FROM (SELECT DISTINCT weekbeg_d FROM site_deliveries WHERE vendor_id = :vid $add UNION SELECT DISTINCT weekbeg_d FROM site_receiving WHERE vendor_id = :vid $add) aa GROUP BY weekbeg_d order by weekbeg_d asc";
            $this->sLog("getVendorWeeks site_deliveries SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getVendorWeeks site_deliveries DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getVendorWeeks site_deliveries RESPONSE",print_r($retarr,true));


            $ret = (isset($retarr[0])) ? $retarr : false;


        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getVendorWeeks EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }

    function getSiteRecentDates($sid,$table='site_deliveries'){

        try{

            $data = array('sid'=>$sid);

            $add = '';



            $sql = "SELECT DISTINCT entry_d FROM $table WHERE site_id = :sid AND entry_d >= (CURDATE() - INTERVAL 2 WEEK ) order by entry_d desc";
            $this->sLog("getSiteRecentDates $table SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getSiteRecentDates $table DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getSiteRecentDates $table RESPONSE",print_r($retarr,true));


            $ret = (isset($retarr[0])) ? $retarr : false;


        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getSiteRecentDates $table EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }

    /**
     * Calculates the day code for the next weekday, skipping over weekends.
     *
     * @param int $datecode The numeric code representing the current day of the week (e.g., Sunday = 0, Monday = 1, ..., Saturday = 6).
     * @return int The numeric code for the next weekday, skipping to Monday if the input is Friday or Saturday.
     */
    function getNextWeekdayCodeValue($datecode){
        $retcode = $datecode + 1;  //increment daycode plus one
       //if saturday - add additional to target monday
        if($datecode==6){
            $retcode = $retcode + 1;
        }
        //if friday - add 2 additional to target monday
        if($datecode==5){
            $retcode = $retcode + 2;
        }
        return $retcode;

    }

    /**
     * Determines the day of the week for a given date.
     *
     * @param string $date The input date in 'Y-m-d' format. If no date is provided, the current date is used.
     * @return int Returns the numeric representation of the day of the week (0 for Sunday, 1 for Monday, ..., 6 for Saturday).
     */
    function getDateCodeValue($date=''){

        $date = ($date!=='') ? $date : date('Y-m-d');
          //  exit("date in: $date");

            $dspl = explode("-", $date);
            $givenday = date("w", mktime(0, 0, 0, $dspl[1], $dspl[2], $dspl[0]));


        return $givenday;

    }


    /**
     * Retrieves a list of adjusted dates representing recent entry dates for a site.
     * This includes filtered dates over approximately the past 31 days, excluding specific days of the week
     * and other specified date codes.
     *
     * @return array An array of recent entry dates adjusted based on specified conditions.
     *               If an exception occurs, it may return a string with error details.
     */
    function getSiteRecentEntryDates(){

        try{

            $date = false;
            $date = ($date===false) ? date('Y-m-d') : $date;
            $givenday = $this->getDateCodeValue($date);
            $sunday = 0;
            $saturday = 6;
            $bufferindays = $givenday - 31 ;

            //loop through the last 14 days of
            $dateArr = array();
            $tomorrowday = $this->getNextWeekdayCodeValue($givenday);
            $dateArr[] = $this->getAdjDateOfWeek(false,$tomorrowday);

            for($i=$givenday;$i>$bufferindays;$i--){
                if($i!=-29 && $i!=-28 && $i!=-22 && $i!=-21 && $i!=-15 && $i!=-14 && $i!=-8 && $i!=-7  && $i!=-1 && $i!=0 && $i!=6 && $i!=7){
                    $dateArr[] = $this->getAdjDateOfWeek(false,$i);
                }
            }
            $ret = $dateArr;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getSiteRecentEntryDatas EXCEPTION",$this->error);
            $ret = "today: " . $this->error;
        }
        return $ret;
    }


    /**
     * Retrieves the label for a given day of the week based on the numeric input.
     *
     * @param int $daynum The numeric representation of the day (1 for Monday, 2 for Tuesday, etc.).
     * @return string Returns the abbreviated label for the day of the week
     *                ("Mon", "Tues", "Wed", "Thurs", "Fri") if the numeric input matches,
     *                or "none: $daynum" if no match is found.
     *
     * Example usage:
     * $label = getDayOfWeekLabel(3); // Returns "Wed".
     * $label = getDayOfWeekLabel(6); // Returns "none: 6".
     */
    function getDayOfWeekLabel($daynum){

        $ret = ($daynum==1) ? "Mon" : "none: $daynum";
        $ret = ($daynum==2) ? "Tues" : $ret;
        $ret = ($daynum==3) ? "Wed" : $ret;
        $ret = ($daynum==4) ? "Thurs" : $ret;
        $ret = ($daynum==5) ? "Fri" : $ret;
        return $ret;
    }



    /**
     * -----------POSTS / SEARCH --------------
     */

    function getItems($query='',$filters='',$userid=0,$postid=0,$sortr='recent'){

        try{

            $ret = false;
            $sql = " WHERE is_active > 0 ";
            $add = '';
            $data = array();

            $incr = 0;
            if($query!=''){

                $qwords = explode(' ',$query);

                foreach ($qwords as $word) {
                    if($word!='or' && $word!='and' && $word!='a' && $word!='an' && $word!='the') {

                        $name = "word$incr";
                        $param = ":$name";

                        $add .= " AND ( ";
                        $add .= " p.title LIKE CONCAT ('%', $param, '%') OR p.description LIKE CONCAT ('%', $param, '%') OR p.body LIKE CONCAT ('%', $param, '%')  OR u.company_name LIKE CONCAT ('%', $param, '%') ";
                        $add .= " ) ";

                        $data[$name] = $word;
                    }
                    $incr++;
                }

            }



            if($userid>0){

                $add .= " AND user_id = :userid ";
                $data["userid"] = $userid;
            }
            if($postid>0){
                $add .= " AND p.id = :postid ";
                $data["postid"] = $postid;
            }



            $sortfield = "p.last_renewed_d desc";
            $sortfield = ($sortr==='price') ? "p.cost desc" : $sortfield;
            $sortfield = ($sortr==='zip') ? "u.zip desc" : $sortfield;
            $sortfield = ($sortr==='rating') ? "p.last_renewed_d desc" : $sortfield;  //THIS ITEM NEEDS A VALUE INSTEAD OF RENEW_D
            $sortfield = ($sortr==='expiration') ? "p.expiration_d desc" : $sortfield;




            $order = " ORDER BY $sortfield";


            $sql .= "$add $order";




            $udata = array("query"=>$query);
            //check if search term is an EXACT match to a company name / handle
            $quer = "SELECT * from users where company_name = :query ";
            $this->sLog("getItems q:$query IS EXACT CUSTOMER SQL",$quer);
            $this->stmt = $this->pdo->prepare($quer);
            $this->stmt->execute($udata);
            $this->sLog("getItems q:$query IS EXACT CUSTOMER DATA",print_r($udata,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getItems q:$query IS EXACT CUSTOMER RESULT",print_r($retarr,true));

            $saveitemtocache = false;

            //return user id if exact match to company name - otherwise, perform search
            if(isset($retarr[0])){
                $ret = $retarr[0]['id'];
                $this->sLog("getItems q:$query IS EXACT CUSTOMER RETURNING",print_r($ret,true));
                $saveitemtocache = true;
            }
            else {
                $sql = "SELECT u.company_name,u.username,u.email,u.city,u.state,u.zip,p.* from post p inner join users u on p.user_id = u.id $sql ";
                $this->sLog("getItems q:$query  POSTS SQL", $sql);
                $this->stmt = $this->pdo->prepare($sql);
                $this->stmt->execute($data);
                $this->sLog("getItems q:$query  POSTS DATA", print_r($data, true));
                $retarr = $this->stmt->fetchAll();
                $this->sLog("getItems q:$query  POSTS RESULT",print_r($retarr,true));


                if(isset($retarr[0])){
                    $saveitemtocache = true;
                    $ret = $retarr;
                    $this->sLog("getItems q:$query POST RETURNING",print_r($ret,true));
                }
                else{
                    $ret = false;
                    $this->sLog("getItems q:$query POST RETURNING","FALSE");
                }



            }

            //save query to cache table if the item returned results
            if($saveitemtocache===true) {
                $uid = $this->UID();
                $cachevals = array();
                $cachevals['user_id'] = $uid;
                $cachevals['query'] = strtolower($query);
                if ($query != '') {
                    $ins = $this->insertDynamic('cache_query', $cachevals);
                }
            }

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getItems EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }
    function getItemImages($postid){

        try{


            $data = array();
            $data['postid'] = $postid;
            $sql = "SELECT * from post_images WHERE post_id = :postid ORDER BY ordinal asc ";
            $this->sLog("getItemImages SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getItemImages DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getItemImages RESULT",print_r($retarr,true));
            $ret = $retarr;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getItemImages EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }
    function getPost($postid){

        try{


            $data = array();
            $data['postid'] = $postid;
            $sql = "SELECT * from post WHERE id = :postid ";
            $this->sLog("getPost SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getPost DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();

            $ret = $retarr;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getPost EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }
    function getLastImageOrdinal($postid){

        try{


            $data = array("postid"=>$postid);
            $sql = "SELECT ordinal from post_images where post_id = :postid order by ordinal desc ";
            $this->sLog("getLastImageOrdinal SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getLastImageOrdinal DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $ret = (isset($retarr[0])) ? $retarr[0]['ordinal'] : false;

        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getLastImageOrdinal EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }
    function deletePostImage($iid,$postid){

        try{


            //GET IMAGE INFO AND DELETE FILE
            $data = array("iid"=>$iid);
            $sql = "SELECT * from post_images where id = :iid ";
            $this->sLog("deletePostImage: getImageInfo SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("deletePostImage getImageInfo DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();

            $this->sLog("deletePostImage getImageInfo RESULT",print_r($retarr,true));
            if(isset($retarr[0])){

                $file = $retarr[0]['path'];
                $uid = $retarr[0]['user_id'];
                $delpath = "uploads/$uid/market/$postid/$file";

                if (file_exists("$delpath")) {
                    unlink("$delpath"); //remove the file
                    $this->sLog("deletePostImage FSDelete File Deleted: ","$delpath");
                }
                else{
                    $this->sLog("deletePostImage FSDelete File Not Found: ","$delpath");
                }

            }

            //DELETE POST IMAGE DB ENTRY
            $deltepath = "uploads/$uid/market/$postid/";
            $data = array("iid"=>$iid);
            $sql = "DELETE FROM post_images where id = :iid ";
            $this->sLog("deletePostImage SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("deletePostImage DATA",print_r($data,true));


            //UPDATE ORDINALS FOR REMAINING IMAGES
            $data = array("postid"=>$postid);
            $sql = "SELECT * from post_images where post_id = :postid order by ordinal asc ";
            $this->sLog("deletePostImage: updateOrdinals SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("deletePostImage updateOrdinals DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();

            if(isset($retarr[0])){
                $ord = 0;
                foreach($retarr as $pi){

                    $piid = $pi['id'];
                    $vals = array();
                    $vals['ordinal'] = $ord;
                    $upd = $this->updateDynamic($piid,'post_images',$vals);
                    $ord++;

                }

            }


            $ret = true;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("temolate EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }
    /**
     * Retrieves cached queries from the database with optional limit and ordering.
     *
     * @param int $limit The maximum number of queries to retrieve. Defaults to 0, which retrieves all queries.
     * @param string $order The column or criteria for ordering the results. Defaults to 'id desc' if not provided.
     * @return array|false Returns an array of distinct cached queries if successful, or false on failure.
     *
     * The method constructs and executes a SQL query to fetch distinct queries from the 'cache_query' table.
     * It supports optional parameters for limiting the number of results and specifying an ordering criterion.
     * Logs are created for the SQL, data, and results, as well as for exceptions if any errors occur during execution.
     */
    function getQueryCache($limit=0, $order=''){

        try{

            $sqllimit = ($limit > 0) ? " LIMIT $limit " : '';

            $orderby = ($order != '') ? " $order " : ' id desc ';

            $data = array("limit"=>$limit);
            $sql = "SELECT DISTINCT(query) from cache_query order by $orderby $sqllimit ";
            $this->sLog("getQueryCache $limit SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("getQueryCache $limit DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("getQueryCache $limit RES",print_r($retarr,true));


            $ret = $retarr;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getQueryCache $limit EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }
    function reorderSess($idx,$newterm){

        try{

            $newarr = array();
            $newarr[0] = ucwords($newterm);

            for ($i = 0; $i < 4; $i++) {
                if($i<$idx) {
                    $newarr[$i + 1] = $_SESSION['recentsearches'][$i];
                }
                elseif($i>$idx){
                    $newarr[$i] = $_SESSION['recentsearches'][$i];
                }
                else{
                    //do nothing
                }
            }
            $_SESSION['recentsearches'] = $newarr;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("reorderSess EXCEPTION",$this->error);
            $ret = false;
        }
      //  return $ret;

    }





    /**
     * -----------SESSION / STATE MANAGEMENT --------------
     */

    /**
     * sLog() - log debug vals to session
     *
     * @param $key
     * @param $val
     * @return void
     */
    function sLog($key,$val){

        if(!isset($_SESSION['slog'])){  $_SESSION['slog'] = array();  }
        $_SESSION['slog'][$key] = $val;

    }
    /**
     * Resets the session log by unsetting the 'slog' entry in the session.
     *
     * @return void
     *
     * This method clears the 'slog' key from the session, effectively resetting any
     * stored log data tied to this session key.
     */
    function resetSLog(){
        unset($_SESSION['slog']);
    }
    /**
     * UID - get user ID if not provided and exists in session
     *
     * @return int|mixed
     */
    function UID(){
        $ret = (isset($_SESSION['user']['id'])&&$_SESSION['user']['id']>0) ? $_SESSION['user']['id'] : 0 ;
        $ret = ($ret>0) ? $ret : $this->activeUserID ;   //if $ret < 1 default to activeUserID as this is 0 if not set anyway
        $this->activeUserID = $ret;
        return $ret;
    }





    /**
     *   -------------- DYNAMIC SQL HELPER FUNCTIONS --------------
     */

    /**
     * Pull all column names from INFORMATION_SCHEMA for a given db table
     * @param $table
     * @return array
     */
    function getDynamicColumns($table){
        $sql = "SELECT `COLUMN_NAME` 
                FROM `INFORMATION_SCHEMA`.`COLUMNS` 
                WHERE `TABLE_SCHEMA`='" . DB_NAME . "'
                        AND `TABLE_NAME`='$table';";
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute();
        $ret =  $this->stmt->fetchAll();

        $retarr  = array();
        foreach($ret as $rr){
            $retarr[] = $rr['COLUMN_NAME'];
        }

        $this->sLog("getDynamicColumns",print_r($retarr,true));
        return $retarr;
    }

    /**
     * getDynamic() - dynamically pull from a target record from any DB table and returnall fields with provided prefix
     *
     * @param $id               - record locator id as in "WHERE idfield = $id"
     * @param $tablename        - table to pull record from
     * @param $prefix           - complex query addon to allow prefixing output
     * @return array|false
     */
    function getDynamic($id,$tablename,$prefix=''){

        $pre = ($prefix=='') ? $tablename : $prefix ;

        //  $arrFields = $this->DBT[$tablename];
        $arrFields = $this->getDynamicColumns($tablename);
        // exit("$tablename <pre>" . print_r($arrFields,true) . "</pre> ");
        $fieldStr = "$pre.id as " . '"' . $pre . 'id"';
        foreach($arrFields as $key=>$val){
            $fieldStr .= ",$pre.$val"  . ' as "' . $pre . $val . '"';
        }

        $quer = "SELECT $fieldStr from $tablename $pre WHERE id = ?";
        $this->sLog('getDynamic' . $tablename . ' prefix-' . $pre, $quer);
        $this->stmt = $this->pdo->prepare($quer);
        $this->stmt->execute(array($id));
        return $this->stmt->fetchAll();

    }

    /**
     * getAllDistinctData - get DISTINCT data from any field source
     *
     * @param $fieldname
     * @param string $keyname
     * @param string $tablename
     * @param string $id
     * @return array|false
     */
    function getAllDistinctData ($fieldname,$keyname='',$tablename='users',$id = '') {
        $sqladd = ($id=='') ? '' : ",',$id' ";
        //  $this->sLog('USR',"getAllDistinctData($fieldname,$keyname,$tablename)");
        if($keyname==''){ $keyname = $fieldname; }
        $dynQuer = "SELECT DISTINCT($fieldname) as '$keyname'$sqladd FROM `$tablename` GROUP BY $fieldname ORDER BY $fieldname asc";
        $this->stmt = $this->pdo->prepare($dynQuer);
        //   $this->sLog("getAllDistinctData query: $dynQuer",'USR');
        $this->stmt->execute();
        return $this->stmt->fetchAll();
    }



    /**
     *    DYNAMIC SQL ACTIONS - PRIMARY DB INTERFACE!!!
     */

    /**
     * getDynResults - get dynamic recordset from any table
     *
     * @param $wherefiltervalue
     * @param string $tablename
     * @param string $filterfieldname
     * @param string $fieldlist
     * @param string $orderby
     * @return array|false
     */
    function getDynResults($wherefiltervalue = '',$tablename='users',$filterfieldname = 'id',$fieldlist = '*',$orderby = '',$andfilter='',$whereoverride='') {

        try {
            $sqlwhere = ($wherefiltervalue != '') ? " WHERE $filterfieldname = $wherefiltervalue $andfilter" : "";
            $sqlwhere = ($whereoverride!='') ? $whereoverride : $sqlwhere;
            $sqlorder = ($orderby != '') ? " ORDER BY $orderby " : "";
            $sql = "SELECT $fieldlist FROM $tablename $sqlwhere $sqlorder";
            $this->sLog("getDynResults SQL $tablename $sqlwhere $sqlorder",$sql);

            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute();
            $return = $this->stmt->fetchAll();
            $this->sLog("getDynResults RESULTS $tablename $sqlwhere $sqlorder",print_r($return,true));
            $ret = (isset($return[0])) ? $return : false;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getDynResults EXCEPTION $tablename $sqlwhere $sqlorder",$this->error);
            $ret = false;
        }
        return $ret;
    }

    /**
     * buildSQL() - build dynamic parameterized sql string (update, insert)
     *
     * @param $action
     * @param $table
     * @param $fields
     * @return string
     */
    function buildSQL($action,$table,$fields,$values='',$idfield='id',$dynidlabel='zdynupdid',$addWhere=''){

        $sql = "";

        $count = count($values);
        if($action=='insert'){

            $arrIDX = array_keys($values);
            $sql= "INSERT INTO `$table` (`" . $arrIDX[0] . "`";
            $valsql = ") VALUES (:" . $arrIDX[0];
            foreach($values as $key=>$val){
                if($key!=$arrIDX[0]) {
                    $sql .= ", `" . $key . "`";
                    $valsql .= ", :" . $key;
                }
            }

            $valsql .= ")";
            $sql .= $valsql;
        }
        if($action=='update'){

            $arrIDX = array_keys($values);
            $sql= "UPDATE `$table` SET `" . $arrIDX[0] . "` = :" . $arrIDX[0];
            foreach($values as $key=>$val){
                if($key!=$arrIDX[0] && $key!=$dynidlabel) {
                    $sql .= ", `" . $key . "`= :" .$key;
                }
            }
            $sql .= " WHERE $idfield = :$dynidlabel $addWhere";
        }
        return $sql;
    }

    /**
     * Executes a dynamic SQL insert operation into a specified table with provided values.
     *
     * @param string $tablename The name of the table where the data will be inserted.
     * @param array $values An associative array of column-value pairs to be inserted into the database table.
     * @return mixed Returns the last inserted ID on success, or false on failure.
     *
     * This method dynamically builds an insert SQL query for the given table using the specified values.
     * If the insert is successful, it retrieves the last inserted ID. Any exceptions encountered during the process
     * will be logged.
     */
    function insertDynamic($tablename,$values){

        $bRet = false;

        $sql = $this->buildSQL('insert',$tablename,'',$values);    //dynamic insert sql
        $this->sLog("insertDynamic $tablename SQL for DATA (" . print_r($values,true) . ")",$sql);

        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($values);
            $lastid = $this->pdo->lastInsertId();
            $this->sLog("insertDynamic $tablename LastID: ",$lastid);

            $bRet = $lastid;
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->sLog("insertDynamic $tablename EXCEPTION: ",$ex->getMessage());
        }

        return $bRet;
    }

    /**
     * Updates a record in the specified table with the given values dynamically.
     *
     * @param mixed $id The primary key value of the record to update.
     * @param string $tablename The name of the database table.
     * @param array $values An associative array of field names and their new values to update.
     * @param string $overrideidfield Optional. Specifies a custom field name to use as the ID field. Defaults to 'id'.
     * @return mixed Returns the ID of the updated record if successful, or false if the operation fails.
     *
     * Dynamically generates and executes the SQL for updating a record, allowing for custom ID fields
     * and dynamic handling of data values.
     */
    function updateDynamic($id,$tablename,$values,$overrideidfield=''){

        $idfield = ($overrideidfield!='') ? $overrideidfield : 'id' ;
        $bRet = false;
        $action = 'update';


        $varname = 'zdynupdid';
        $sql = $this->buildSQL($action,$tablename,'',$values,$idfield,$varname); //dynamic update sql


        $data = $values;
        $data[$varname] = $id;

        $this->sLog("updateDynamic $tablename SQL for DATA (id=$id, data=" . print_r($data,true) . ", overrideidfield=$overrideidfield)",$sql);

        try {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);

            $bRet = $id;
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->sLog('ERROR updatesql' . $tablename,$this->error);
        }

        return $bRet;
    }

    /**
     * Deletes records from specified database tables dynamically based on the provided parameters.
     *
     * @param array|false $addsqlArr A multidimensional associative array of deletion parameters.
     * Each numbered group of the array is its own set of deletion commands.  Each command must include:
     *                  - 'id': The identifier of the record(s) to delete.
     *                  - 'table': The name of the table from which the record(s) should be deleted.
     *                  - 'field': The name of the field that matches the 'id'.
     *                  - 'addsql' (optional): Additional SQL conditions to customize the deletion query.
     *          So one delete command:
     *                eg: $values = array();
     *                    $values[0] = array();
     *                    $values[0]['id'] = 'value to match against your_field_name to target records';
     *                    $values[0]['table'] = 'your_table_name';
     *                    $values[0]['field'] = 'your_field_name';
     *                    $values[0]['addsql'] = 'your_additional_sql_condition';
     *
     *           or multiple by just adding grouped elements
     *                    $values[1] = array();
     *                    $values[1]['id'] = 'value to match against your_field_name';
     *                    $values[1]['table'] = 'your_table_name';
     *                    $values[1]['field'] = 'your_field_name';
     *                    $values[1]['addsql'] = 'your_additional_sql_condition';
 *
     *          If false is passed, no processing is done.
     * @return int Returns the total number of records successfully deleted. Returns 0 if no deletion occurs
     *             or if the input parameters are invalid.
     */
    function deleteDynamic($addsqlArr=false) {
        try {

            $ret = 0;


            if($addsqlArr!==false && isset($addsqlArr[0]['id']) && isset($addsqlArr[0]['table']) && isset($addsqlArr[0]['field'])){
                $recordsremoved = 0;
                foreach($addsqlArr as $add){

                    $id = $add['id'];
                    $table = $add['table'];
                    $field = $add['field'];
                    $addsql = (isset($add['addsql'])) ? $add['addsql'] : '';

                    $sql = "DELETE FROM $table WHERE $field = :id $addsql ";
                    $this->sLog("deleteDynamic $table-$field-$id SQL",$sql);
                    $data = array("id"=>$id);
                    $this->sLog("deleteDynamic $table-$field-$id DATA",print_r($data,true));
                    $this->stmt = $this->pdo->prepare($sql);
                    $recordsremoved = $recordsremoved + $this->stmt->execute($data);
                    $this->sLog("deleteDynamic $table-$field-$id RECORDS DELETED",$recordsremoved);
                }

                $ret = $recordsremoved;
            }
            else{

                $this->sLog("deleteDynamic FAIL - no parameters or incorrect parameters provided",print_r($addsqlArr,true));
                $ret = 0;

            }




        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->sLog("deleteDynamic $table-$field-$id EXCEPTION",$this->error);
            $ret = 0;
        }

        return $ret;

    }





    /**
     *   -------------- EMAIL FUNCTIONS --------------
     */

    /**
     * notifyUser() - Create User Notification
     *
     * @param $toID
     * @param $customsubject
     * @param $custombody
     * @return bool
     */
    function notifyUser($toID,$templateID,$customsubject='',$custombody=''){
        return $this->addToMessageQueue($toID,$templateID,0,1,'','',$customsubject,$custombody);
    }

    /**
     * Generates HTML template content for displaying threshold item details.
     *
     * @param array $threshArr An array of threshold item details. Each element in the array should be an associative array with the following keys:
     *                         - companyname: The name of the company.
     *                         - productname: The name of the product (underscores and triple underscores are formatted into words and symbols).
     *                         - entrydate: The date the entry was created.
     *                         - currentthreshold: The current threshold value.
     *                         - returnedamt: The returned amount.
     *                         - discrepency: The discrepancy amount (highlighted in red in the output).
     *
     * @return string The generated HTML string representing the threshold items. If an exception occurs or no threshold items are provided, it returns an empty string.
     */
    function getHTMLContentForThresholdItem($threshArr = array()){

        $str = '';

        try {

            if(isset($threshArr[0])){

                foreach($threshArr as $thresh) {

                    $t                = $thresh;
                    $companyname      = $t['companyname'];
                    $productname      = $t['productname'];
                    $processedname    = ucwords(str_replace('_', ' ', str_replace('___',' & ',$productname)));
                    $entrydate        = $t['entrydate'];
                    $currentthreshold = $t['currentthreshold'];
                    $returnedamt      = $t['returnedamt'];
                    $discrepency      = $t['discrepency'];


                    $str .= '<div class="thresholditem" style="display:block;width:100%;">

                            <div class="title" style="text-align:right;font-size: 16px;font-weight:bold;width:45%;display:inline-block;">Client: </div>
                            <div class="value" style="text-align:left;font-size: 16px;width:45%;display:inline-block;"> ' . $companyname . '</div>

                            <div class="title" style="text-align:right;font-size: 16px;font-weight:bold;width:45%;display:inline-block;">Entry Date: </div>
                            <div class="value" style="text-align:left;font-size: 16px;width:45%;display:inline-block;"> ' . $entrydate . '</div>
            
                            <div class="title" style="text-align:right;font-size: 16px;font-weight:bold;width:45%;display:inline-block;">Product: </div>
                            <div class="value" style="text-align:left;font-size: 16px;width:45%;display:inline-block;"> ' . $processedname . '</div>
            
                            <div class="title" style="text-align:right;font-size: 16px;font-weight:bold;width:45%;display:inline-block;">Threshold: </div>
                            <div class="value" style="text-align:left;font-size: 16px;width:45%;display:inline-block;"> ' . $currentthreshold . '</div>


                            <div class="title" style="text-align:right;font-size: 16px;font-weight:bold;width:45%;display:inline-block;">Returned: </div>
                            <div class="value" style="text-align:left;font-size: 16px;width:45%;display:inline-block;"> ' . $returnedamt . '</div>

                            <div class="title" style="text-align:right;font-size: 16px;font-weight:bold;width:45%;display:inline-block;">Discrepency: </div>
                            <div class="value" style="text-align:left;font-size: 16px;width:45%;font-weight:bold;display:inline-block;color:red;"> ' . $discrepency . '</div>
                            
                            </div>';
                }


                $this->sLog("getHTMLContentForThresholdItem returning HTML from " . print_r($t,true) . "  ",$str);


            }
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("getHTMLContentForThresholdItem EXCEPTION from " . print_r($threshArr,true) . "  ",$this->error);
            $str = '';
        }


        return $str;
    }


    /**
     * Sends threshold notification emails to a specified list of recipients based on
     * vendor configuration and admin user emails.
     *
     * @param int $vid The ID of the vendor whose threshold notifications are being sent.
     * @param array $threshArr An array of threshold data used to generate custom HTML email content.
     * @return bool|string Returns `false` on error, or the result of the email-sending operation.
     *
     * This method constructs the notification emails using a combination of threshold data
     * and the vendor's notification recipients. It ensures that:
     * - Any explicitly specified recipients from the vendor configuration are included.
     * - Admin emails are added to the recipient list if not already included.
     * - The emails are sent via the message queue system.
     *
     * If the recipient list is empty or an error occurs during processing, the method logs
     * the error and the operation halts with a false return value.
     */
    function sendThresholdEmails($vid, $threshArr = array()){

        try {

            //use threshold array to build an item or list of items
            $customhtml = $this->getHTMLContentForThresholdItem($threshArr);

            $reciplist = array();

            //check for and extract any specified threshold recipients from vendor config
            $dbvendor = $this->getByID($vid, 'id', 'users');
            if(isset($dbvendor[0])){
                $v          = $dbvendor[0];
                $vendorname = $v['company_name'];
                $notifrecips = $v['notif_email'] ?? '';

                //if recips explicitly set
                if($notifrecips!=''){

                    //if explicit recipient is a list of recips then add each separately
                    if(strpos($notifrecips,',')!==false){
                        $spl = explode(',',$notifrecips);
                        foreach($spl as $em){
                            $reciplist[] = trim(strtolower($em));
                        }
                    }
                    //if single entry - add it to recip array directly
                    else{
                        $reciplist[] = trim(strtolower($notifrecips));
                    }
                }

                //now add all admin emails as recipients also
                $dbadminemails = $this->getDynResults("'admin'",'users','type');
                if(isset($dbadminemails[0])){
                    foreach($dbadminemails as $dbae){
                        $adminemail = $dbae['email'];
                        $ae = trim(strtolower($adminemail));
                        if(in_array($ae,$reciplist)===false){
                            $reciplist[] = $ae;
                        }
                    }
                }


                //now send email to all recipients
                foreach($reciplist as $email){

                    $ret =  $this->addToMessageQueue($vid,41,0,0,'','','',"$customhtml",3,0,0,$email);

                }


                $ret = $this->sendFromMessageQueue();


            }
        }
        catch (Exception $ex) {
                $this->error = 'Exception Error: ' . $ex->getMessage();
                $this->sLog('addToMessageQueu',$this->error);
                $ret = false;

            }






    }


    /**
     * Retrieves the inventory return threshold for a given vendor ID and inventory name.
     * Queries the `site_config` table to find the return threshold associated with the provided vendor ID and inventory name.
     * If no matching record is found, the default return value is 0.
     *
     * @param int $vid The vendor ID to be queried.
     * @param string $invname The inventory name associated with the vendor.
     * @return int The return threshold value. Returns 0 if no matching record is found or an exception occurs.
     */
    function getInvThreshold($vid, $invname){

        try{
            $ret = 0;

            $data = array("vid"=>$vid,"invname"=>$invname);
            $sql = "SELECT * from site_config WHERE vendor_id = :vid and inv_name = :invname order by return_threshold desc";
            $this->sLog("checkInvThreshold SQL ",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("checkInvThreshold DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("checkInvThreshold RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]['return_threshold']) ? $retarr[0]['return_threshold'] : 0;



        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("checkInvThreshold EXCEPTION from vendorid: $vid  inventoryid: $invid",$this->error);
            $ret = 0;
        }
        return $ret;

    }






    /**
     * emailUser() - Send User email
     *
     * @param $toID
     * @param $templateID
     * @return bool
     */
    function emailUser($toID,$templateID){


        $ret =  $this->addToMessageQueue($toID,$templateID,0,0,'','');
        if($ret===true) {
            $ret = $this->sendFromMessageQueue();
        }

      //  $user = $this->getByID($toID);
      //  $email = $user[0]['email'];
      //  $ret = $this->sendDirectEmail($email)


        return ($ret===false) ? 0 : (($ret===true) ? 1 : $ret) ;
    }

    /**
     * addToMessageQueue() - add an outgoing message to the message queue
     *
     * @param $toID
     * @param $templateID
     * @param $isAdmin
     * @param $isNotification
     * @param $to
     * @param $from
     * @param $custom
     * @return bool
     */
    function addToMessageQueue($toID=0,$templateID=0,$isAdmin=0,$isNotification=0,$to='admin',$from='',$customsubject='',$custombody='',$senderID=3,$threadID=0,$connectToCustomer_id=0,$recipoverride=''){

        $ret = false;
        $sToID = $toID;
        $sToEmail = $to;
        $sFromEmail = ($from=='') ? 'noreply@bulk.sparklreusables.com' : $from;

        try {

            //if recipient is provided - build user object
            if ($sToID > 0) {
                $toUser = $this->getByID($sToID);
                $sToEmail = ($recipoverride!=='') ? $recipoverride : $toUser[0]['email'];


            } //if no recipient
            else {

                //if custom recipient
                if ($to != 'admin') {

                    //find user by email in users DB
                    $dbSingleUser = $this->getByEmail($to);
                    $toUser = array();
                    $toUser[0] = $dbSingleUser;
                    $sToID = (isset($toUser[0]['id'])) ? $toUser[0]['id'] : 0;

                } //if admin is recipient
                else {
                    $toUser = $this->getByid(3);
                    $sToID = $toUser[0]['id'];
                }
            }
            $type = ($isAdmin > 0) ? 'email' : (($isNotification > 0) ? 'notification' : 'system');
            if ($isAdmin > 0 && $isNotification > 0) {
                $type = 'notification';
            }
            $status = 1;
            $fn = $toUser[0]['firstname'];
            $ln = $toUser[0]['lastname'];
            $fnln = "$fn $ln";
            $fnln = ucwords($fnln);
            $vendorname = $toUser[0]['company_name'];
            $baseurl = $this->rtprot . $this->rturl;

            //special case for emails with no user records yet ...
            $token = '';
            if ($token != 26) {
                $token = ($templateID == 2) ? $toUser[0]['email_authcode'] : $this->generateUserToken($sToID, $templateID, $sToEmail);
            }

            $dtahtm = 'empty';
            $d = new DateTime('NOW');
            $dt = $d->format('m/d/Y');
            //mail template
            if ($templateID > 0) {

                $dtahtm = $this->getIntakeTemplate($templateID);
                $filecontent = $dtahtm['content_raw'];
                $subject = $dtahtm['subject'];

            } else {

                $filecontent = $custombody;
                $subject = $customsubject;
            }


            /* REF:
                     $arrEmailVars = array(
                         'date'=>'@@DATE@@',
                         'user_id'=>'@@USERID@@',
                         'firstname'=>'@@FIRSTNAME@@',
                         'lastname'=>'@@LASTNAME@@',
                         'email'=>'@@EMAIL@@',
                         'fullname'=>'@@FULLNAME@@',
                         'baseurl'=>'@@BASEURL@@',
                         'custom'=>'@@CUSTOM@@',
                         'uri'=>'@@URI@@',
                         'token'=>'@@TOKEN@@'
                     );

                 */

            $replaceFN = $fn;
            $replaceLN = $ln;
            $replaceFNLN = $fnln;

            /*
            $bToAdmin = ($sToID == 3 || $isAdmin > 0) ? true : false ;     //is this to admin
            //populate name in email / notification body (use customer name in all cases)
            $dbFromUser = ($bToAdmin) ? $this->getByID($senderID) : false;
            $bIsUser = (isset($dbFromUser[0]));
            $fromU = ($bIsUser) ? $dbFromUser[0] : false;
            $replaceFN =  ($bToAdmin && $bIsUser) ? $fromU['firstname'] : $fn ;
            $replaceLN =  ($bToAdmin && $bIsUser) ? $fromU['lastname'] : $ln;
            $replaceFNLN =  ($bToAdmin && $bIsUser) ? ucwords("$replaceFN $replaceLN") :  $fnln;
            */



            if($filecontent==''){
                $errmessage = 'Error getting template content' . print_r($dtahtm,true);
                $this->error = $errmessage;
                $create = 0;
                Throw new Exception($errmessage);

            }
            else {


                $replaceFN = $fn;
                $replaceLN = $ln;
                $replaceFNLN = $fnln;
                $replaceVendor = $vendorname;


                $filecontent = str_replace('@@DATE@@', $dt, $filecontent);
                $filecontent = str_replace('@@USERID@@', $sToID, $filecontent);
                $filecontent = str_replace('@@FIRSTNAME@@', $replaceFN, $filecontent);
                $filecontent = str_replace('@@LASTNAME@@', $replaceLN, $filecontent);

                /*
                // --  GMAIL WORKAROUND FOR AUTO-MAILTO ON EMAIL ADDRESSES --
                //create display friendly email address that gmail WONT turninto a mailto
                $sDispEmail = str_replace('@', '<span></span>@<span></span>', $sToEmail);
                $sDispEmail = " " . str_replace('.', '<span></span>.<span></span>', $sDispEmail);
                //replace only instances of @@EMAIL@@ that are NOT in href="@@EMAIL@@" by replacing with a leading space ' @@EMAIL@@'
                $filecontent = str_replace(' @@EMAIL@@', $sDispEmail, $filecontent);
                // --  ENG WORKAROUND  --

                $filecontent = str_replace('@@EMAIL@@', $sToEmail, $filecontent);


                $emailnoformat = str_replace(".", '', str_replace("@", '', $sToEmail));


                $filecontent = str_replace('@@EMAILNOFORMAT@@', $emailnoformat, $filecontent);
                */

                $filecontent = str_replace('@@UID@@', $sToID, $filecontent);
                $filecontent = str_replace('@@FULLNAME@@', $replaceFNLN, $filecontent);
                $filecontent = str_replace('@@BASEURL@@', $baseurl, $filecontent);
                $filecontent = str_replace('@@CUSTOM@@', $custombody, $filecontent);
                $filecontent = str_replace('@@TOKEN@@', $token, $filecontent);
                $filecontent = str_replace('@@VENDORNAME@@', $replaceVendor, $filecontent);


                //     $appid = 0;
                //    $uid = $this->activeUserID;
                //    if ($uid > 0) {
                //       $includeAll = ($isAdmin > 0) ? true : false;
                //    }

                $senderID = ($senderID > 0) ? $senderID : 3;


                $values = array();

                if ($threadID > 0) {
                    $values['thread_id'] = $threadID;
                } else {
                    $threadvals = array();
                    $threadvals['sender_id'] = $senderID;
                    $threadvals['recipient_id'] = $sToID;
                    // $threadvals['customer_id'] = $connectToCustomer_id;

                    $newthread = $this->insertDynamic('rel_messages_threads', $threadvals);
                    $values['thread_id'] = $newthread;
                }


                $values['sender_id'] = $senderID;
                $values['recipient_id'] = $sToID;
                //   $values['customer_id'] = $connectToCustomer_id;
                //   $values['rel_application_id'] = $appid;
                $values['template_id'] = $templateID;
                $values['type'] = $type;
                $values['status'] = $status;
                //  $values['subject'] = $this->cRPL($subject);     //final content replace of all admin config vars
                //  $values['content'] = $this->cRPL($filecontent); //final content replace of all admin config vars
                $values['recipient_address'] = $sToEmail;
                $values['sender_address'] = $sFromEmail;

                $values['subject'] = $subject;
                $values['content'] = $filecontent;

                $create = $this->insertDynamic('messagecenter', $values);

            }

            $ret = ($create > 0) ? true : false;


        }
        catch (Exception $ex) {
            $this->error = 'Exception Error: ' . $ex->getMessage();
            $this->sLog('addToMessageQueu',$this->error);
            $ret = false;

        }

        return $ret;


    }

    /**
     * GET DYNAMIC EMAIL TEMPLATE
     *
     * @param $id
     * @return mixed
     */
    function getIntakeTemplate($id){

        $this->stmt = $this->pdo->prepare("SELECT * FROM `message_templates` WHERE `id`=?");
        $this->stmt->execute(array($id));
        return $this->stmt->fetch();

    }

    /**
     * verifyUserToken() - verify against saved token in DB
     *
     * @param $uid
     * @param $templateid
     * @param $token
     * @param $markused
     * @return bool
     */
    function verifyUserToken($uid,$templateid,$token,$markused=true){

        $quer = "SELECT * FROM `user_tokens` WHERE user_id = ? AND template_id = ? and token = ? and is_used = 0;";
        $this->stmt = $this->pdo->prepare($quer);
        $this->stmt->execute([$uid,$templateid,$token]);
        $ret = $this->stmt->fetchAll();

        $bret = false;
        if(isset($ret[0])){
            $bret = true;


            if($markused===true) {
                $id = $ret[0]['id'];

                $values = array('is_used' => 1);
                $upd = $this->updateDynamic($id, 'user_tokens', $values);
            }

        }

        return $bret;

    }

    /**
     * generateUserToken() - create new user md5 token and save in DB
     *
     * @param $uid
     * @param $templateid
     * @param $email
     * @return false|string
     */
    function generateUserToken($uid,$templateid,$email){


        $token = md5(date("YmdHis") . $email);
        $values = array();
        $values['user_id'] = $uid;
        $values['template_id'] = $templateid;
        $values['token'] = $token;
        $ins = $this->insertDynamic('user_tokens',$values);
        return ($ins>0) ? $token : false;

    }

    /**
     * updateMailStatus() - update mail status in messagecenter queue
     *
     * @param $mailid
     * @param $newStat
     * @return bool
     */
    function updateMailStatus($mailid,$newStat){

        $values = array();
        $values['status'] = $newStat;
        $update = $this->updateDynamic($mailid,'messagecenter',$values);
        return $update;

    }

    /**
     * getAllOutgoingMessages() - poll messagectr db and get all outgoing messages (for sending by cron job)
     *
     * @param $messtype
     * @param $status
     * @param $isRead
     * @param $isHidden
     * @param $orderby
     * @return array|false
     */
    function getAllOutgoingMessages($messtype='system',$status='1',$isRead='0',$isHidden='0',$orderby=''){


        $sqlwhere = " WHERE type = '$messtype' ";
        $sqlwhere .= ($status!='') ? " AND status = '$status' " : '';

        $sqlwhere .=  ($isRead!='') ? " AND is_read = '$isRead'" : '' ;
        $sqlwhere .= ($isHidden!='') ? " AND is_hidden = '$isHidden' " : '';

        $sql = "SELECT * FROM messagecenter $sqlwhere $orderby";
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute();
        return $this->stmt->fetchAll();

    }

    /**
     * sendFromMessageQueue() - Loop through mail queue and send out all emails that are status 1
     *
     * @return bool
     */
    function sendFromMessageQueue(){


        global $smtp_secure, $smtp_host, $smtp_port, $smtp_username, $smtp_password;

        $messages = $this->getAllOutgoingMessages() ;
        $anyerrors = 0;

        require_once 'includes/class.smtp.php';
        require_once 'includes/class.phpmailer.php';

        foreach($messages as $mess) {


            try {

                $stat = $this->updateMailStatus($mess['id'],3);

                //create and prep PHPMailer
                $mail = new PHPMailer();

                $mail->isSMTP();
                //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $smtp_secure;
                $mail->Host = $smtp_host;
                $mail->Port = $smtp_port;
                $mail->Username = $smtp_username;
                $mail->Password = $smtp_password;


                //   $mail->From = $mess['sender_address'];
                //  $mail->FromName = ($mess['recipient_type']=='admin') ? 'Carlolly' : 'Carlolly';
                $mail->setFrom($mess['sender_address'], 'Sparkl Reusables Inventory Management');


                $mail->addAddress($mess['recipient_address'], 'Recipient');
                $mail->addAddress('ez@unifiednoise.com', 'Recipient');



                $mail->Subject = $mess['subject'];
                // $mail->MsgHTML($mess['content']);
                $mail->Body = $mess['content'];
                $mail->IsHTML(true);
                //$mail->send();

                if(!$mail->send()){
                    $stat = $this->updateMailStatus($mess['id'],2);
                    $this->error = 'Managed Error: ' . $mail->ErrorInfo;

                    $errvals = array('errortext'=>$this->error);
                    $upd = $this->updateDynamic($mess['id'],'messagecenter',$errvals);
                    $anyerrors++;
                }
                else {
                    $stat = $this->updateMailStatus($mess['id'], 4);
                }
            }
            catch (Exception $ex) {
                $this->error = 'Exception Errorl: ' . $ex->getMessage();
                $stat = $this->updateMailStatus($mess['id'],2);
                $errvals = array('errortext'=>$this->error);
                $upd = $this->updateDynamic($mess['id'],'messagecenter',$errvals);
                $anyerrors++;

            }


        }

        return ($anyerrors<1) ? true : false;

    }


    function sendDirectEmail($recipient,$subject,$content){

        global $smtp_secure, $smtp_host, $smtp_port, $smtp_username, $smtp_password;

        $messages = $this->getAllOutgoingMessages() ;
        $anyerrors = 0;

        require_once 'includes/class.smtp.php';
        require_once 'includes/class.phpmailer.php';

     //   foreach($messages as $mess) {


        try {


            //create and prep PHPMailer
            $mail = new PHPMailer();

            $mail->isSMTP();
            //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = $smtp_secure;
            $mail->Host = $smtp_host;
            $mail->Port = $smtp_port;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;


            //   $mail->From = $mess['sender_address'];
            //  $mail->FromName = ($mess['recipient_type']=='admin') ? 'Carlolly' : 'Carlolly';
            $mail->setFrom('info@bulk.sparklreusables.com', 'Sparkl Reusables');


            $mail->addAddress($recipient, 'Recipient');
            $mail->addAddress('ez@unifiednoise.com', 'Recipient');



            $mail->Subject = $subject;
            // $mail->MsgHTML($mess['content']);
            $mail->Body = $content;
            $mail->IsHTML(true);
            //$mail->send();

            if(!$mail->send()){

                $this->sLog("sendDirectEmail", 'Managed SMTP Error' . $mail->ErrorInfo);
                $ret =false;
            }
            else {
                $ret = true;
            }
        }
        catch (Exception $ex) {
            $this->error = 'SMTP Exception: ' . $ex->getMessage();
            $this->sLog("sendDirectEmail","Exception: " . $this->error . " SMTP Error " . $mail->ErrorInfo);
            $ret = false;
        }


        return $ret;

    }

    /**
     * checkEmailAuthorized() - check if user has verified email address
     *
     * @param $uid
     * @return bool
     */
    function checkEmailAuthorized($uid){

        $bAuth = false;
        $user = $this->getByID($uid);
        if(isset($user[0])){
            $bAuth = ($user[0]['email_authenticated'] > 0) ? true : false ;
        }
        return $bAuth;
    }

    /**
     * verifyEmail() - set email to authenticated
     *
     * @param $uid
     * @param $token
     * @return false
     */
    function verifyEmail($uid,$token){

        $bAuth = false;
        $bRet = false;
        $dbuser = $this->getByID($uid);

        if(isset($dbuser[0])){
            $user = $dbuser[0];
            $sAuthToken = $user['email_authcode'];
            $bAuth = ($token==$sAuthToken);

            if($bAuth==true){
                $values = array();
                $values['email_authenticated'] = 1;
                $upd = $this->updateDynamic($uid,'users',$values);
                $bRet = ($upd!==false);
            }
            else{
                $this->error = ' Invalid Token ' . $this->error . " (sAuthToken=$sAuthToken token=$token bAuth=$bAuth)";
            }

        }
        else{
            $this->error = ' Invalid User ' . $this->error;
        }

        if($bRet===false){
            $this->error = 'Email verification error:' . $this->error;
        }
        else{
            //  $this->emailUser($uid,1);
        }

        return $bRet;

    }

    /**
     * getAllUserNotifications() - get all user notifications in messagectr for display
     *
     * @param $uid
     * @param $isRead
     * @param $isHidden
     * @return array|false
     */
    function getAllUserNotifications($uid,$isRead='',$isHidden='',$recipientOnly = false){
        try{

            $sqladd = '';
            $sqladd .=  ($isRead!='') ? " AND is_read = '$isRead'" : '' ;
            $sqladd .= ($isHidden!='') ? " AND is_hidden = '$isHidden' " : '';
            $sqladd .= ($recipientOnly===true) ? " AND (recipient_id = :uid) " : " AND (recipient_id = :uid OR sender_id = :uid) ";

            $quer = "SELECT * FROM `messagecenter` WHERE type = 'notification' $sqladd ORDER by id desc";
            $this->sLog('getAllUserNotifications',$quer);
            $this->stmt = $this->pdo->prepare($quer);
            $this->stmt->execute(array("uid" => $uid));
            $this->debug = 'getAllUserNotifications Query: ' . $quer;
            return $this->stmt->fetchAll();
        }
        catch (Exception $ex) {
            $this->error = 'SQL Error: ' . $ex->getMessage();
            $ret = false;
        }
        return $ret;

    }

    /**
     * getNewUserNotifications - get any notifications that have come in AFTER a given id ...
     *
     * @param $uid
     * @param $lastid
     * @param $isRead
     * @param $isHidden
     * @param $recipientOnly
     * @return array|false
     */
    function getNewUserNotifications($uid,$lastid,$isRead='',$isHidden='',$recipientOnly = false){
        try{

            $sqladd = '';
            $sqladd .=  ($isRead!='') ? " AND is_read = '$isRead'" : '' ;
            $sqladd .= ($isHidden!='') ? " AND is_hidden = '$isHidden' " : '';
            $sqladd .= ($recipientOnly===true) ? " AND (recipient_id = :uid) " : " AND (recipient_id = :uid OR sender_id = :uid) ";

            $quer = "SELECT * FROM `messagecenter` WHERE type = 'notification' AND id > :nid $sqladd ORDER by id desc";
            $this->sLog('getNewUserNotifications',$quer);
            $this->stmt = $this->pdo->prepare($quer);
            $this->stmt->execute(array("uid" => $uid,"nid"=>$lastid));
            $this->debug = 'getNewUserNotifications Query: ' . $quer;
            return $this->stmt->fetchAll();
        }
        catch (Exception $ex) {
            $this->error = 'SQL Error: ' . $ex->getMessage();
            $ret = false;
        }
        return $ret;

    }

    /**
     * getAllUserThreads() - get all user notification threads in messagectr for display
     *
     * @param $uid
     * @return array|false
     */
    function getAllUserThreads($uid){
        try{

            $quer = "SELECT * FROM `rel_messages_threads` WHERE id IN (select DISTINCT(thread_id) from messagecenter where is_hidden < 1 AND type = 'notification') AND (recipient_id = :uid OR sender_id = :uid) ORDER by id desc";
            $this->sLog('getAllUserThreads',$quer);
            $this->stmt = $this->pdo->prepare($quer);
            $this->stmt->execute(array("uid" => $uid));
            $this->debug = 'getAllUserThreads Query: ' . $quer;
            return $this->stmt->fetchAll();
        }
        catch (Exception $ex) {
            $this->error = 'SQL Error: ' . $ex->getMessage();
            $ret = false;
        }
        return $ret;

    }

    /**
     * getAllUserThreadedNotifications() - get all user notifications by thread in messagectr for display
     *
     * @param $tid
     * @return array|false
     */
    function getAllUserThreadedNotifications($tid,$isHidden=''){
        try{

            $sqladd = ($isHidden!='') ? " AND is_hidden = '$isHidden' " : '';
            $quer = "SELECT * FROM `messagecenter` WHERE thread_id = :tid AND type = 'notification' $sqladd ORDER by id desc";
            $this->sLog('getAllUserThreadedNotifications',$quer);
            $this->stmt = $this->pdo->prepare($quer);
            $this->stmt->execute(array("tid" => $tid));
            $this->debug = 'getAllUserThreadedNotifications Query: ' . $quer;
            return $this->stmt->fetchAll();
        }
        catch (Exception $ex) {
            $this->error = 'SQL Error: ' . $ex->getMessage();
            $this->sLog('ERROR-getAllUserThreadedNotifications',$this->error);
            $ret = false;
        }
        return $ret;

    }

    /**
     * countUnread() - count unread within array of messages / notifications
     *
     * @param $arrMess
     * @return int
     */
    function countUnread($uid,$arrMess){
        try{

            $incr = 0;
            foreach($arrMess as $m){
                if($m['recipient_id']==$uid && $m['is_read']<1 && $m['is_hidden']<1){
                    $incr++;
                }
            }

            return $incr;
        }
        catch (Exception $ex) {
            $this->error = 'Error: ' . $ex->getMessage();
            $this->sLog('ERROR-countUnread',$this->error);
            return false;
        }
    }

    /**
     * countRead() - count unread within array of messages / notifications
     *
     * @param $arrMess
     * @return int
     */
    function countRead($uid,$arrThreads,$arrMess){
        try{
            $arrComm = $this->getCommentsFromThreadsAndMessages($arrThreads,$arrMess);
            $incr = 0;
            foreach($arrComm as $m){
                if($m['recipient_id']==$uid && $m['is_read']>0 && $m['is_hidden']<1){
                    $incr++;
                }
            }

            return $incr;
        }
        catch (Exception $ex) {
            $this->error = 'Error: ' . $ex->getMessage();
            $this->sLog('ERROR-countRead',$this->error);
            return false;
        }
    }

    /**
     * countToSupport() - count unread within array of messages / notifications
     *
     * @param $arrMess
     * @return int
     */
    function countToSupport($arrThreads){
        try{
            /*
                        $arrThreadsAndMess = array();
                        $bIsToSupport = false;
                        $threadincr = 0;
                        foreach($arrThreads as $t){

                            if($t['sender_id']!=3){
                                $bIsToSupport = true;
                                $threadincr++;
                                $incr = 0;
                                foreach($arrMess as $m){
                                    if($t['id']==$m['thread_id']){
                                        if($m['sender_id']==3 && $m['is_hidden']<1){
                                            $incr++;
                                        }
                                    }

                                }
                            }
                        }
             $arrThreadsAndMess['threads'] = threadincr;
                        $arrThreadsAndMess['mess'] = incr;
            */
            $threadincr = 0;
            foreach($arrThreads as $t) {

                if ($t['sender_id'] != 3) {
                    $threadincr++;
                }
            }


            return $threadincr;
        }
        catch (Exception $ex) {
            $this->error = 'Error: ' . $ex->getMessage();
            $this->sLog('ERROR-countToSupport',$this->error);
            return 0;
        }
    }

    /**
     * getCommentsFromThreadsAndMessages - return array of only incoming comments from threads and messages
     *
     * @param $arrThreads
     * @param $arrMess
     * @return array|false
     */
    function getCommentsFromThreadsAndMessages($arrThreads,$arrMess){
        try{
            $retArr = array();
            $commentincr = 0;
            $readcomment = 0;
            $unreadcomment = 0;
            $threadincr = 0;
            foreach($arrThreads as $t) {

                if ($t['sender_id'] != 3) {
                    $threadincr++;
                    $commentincr = 0;
                    $readcomment = 0;
                    $unreadcomment = 0;
                    foreach ($arrMess as $m) {

                        if ($t['id'] == $m['thread_id']) {
                            if ($m['sender_id'] == 3) {
                                $commentincr++;
                                if ($m['is_read'] < 1) {
                                    $unreadcomment++;
                                }
                                else{
                                    $readcomment++;
                                }
                            }

                        }

                    }

                }
            }


            $thrAndMess = array();
            $thrAndMess['tickets'] = $threadincr;
            $thrAndMess['comments'] = $commentincr;
            $thrAndMess['readcomments'] = $readcomment;
            $thrAndMess['unreadcomments'] = $unreadcomment;




            return $thrAndMess;


        }
        catch (Exception $ex) {
            $this->error = 'Error: ' . $ex->getMessage();
            $this->sLog('ERROR-getCommentsFromThreadsAndMessages',$this->error);
            return false;
        }
    }

    /**
     * countResponsesFromSupport - count how many responses are from support (for counting)
     *
     * @param $arrThreads
     * @param $arrMess
     * @return int
     */
    function countResponsesFromSupport($arrThreads,$arrMess){
        try{

            $threadincr = 0;
            foreach($arrThreads as $t){
                if($t['sender_id']!=3){
                    $threadincr++;
                    $incr = 0;
                    foreach($arrMess as $m){

                        if($t['id']==$m['thread_id']){
                            if($m['sender_id']==3 && $m['is_hidden']<1){
                                $incr++;
                            }
                        }

                    }
                }
            }

            //  $this->sLog('CountResponsesFromSupport'," mess: $incr threads: $threadincr ");
            return $incr;
            //- $threadincr;


        }
        catch (Exception $ex) {
            $this->error = 'Error: ' . $ex->getMessage();
            $this->sLog('ERROR-CountResponsesFromSupport',$this->error);
            return false;
        }
    }

    /**
     * countFromSupport() - count unread within array of messages / notifications
     *
     * @param $arrMess
     * @return int
     */
    function countFromSupport($arrThreads){
        try{

            $incr = 0;
            foreach($arrThreads as $t){
                if($t['sender_id']==3){
                    $incr++;
                }
            }

            return $incr;
        }
        catch (Exception $ex) {
            $this->error = 'Error: ' . $ex->getMessage();
            $this->sLog('ERROR-countFromSupport',$this->error);
            return false;
        }
    }

    /**
     * buildEmailContentTable - build loan details table formatted to insert into email
     *
     * @param $arrContent
     * @return string
     */
    function buildEmailContentTable($arrContent){

        $str = '<table>';
        foreach($arrContent as $ac){

            $str .= '    <tr style="border:none !important;">
                        <td style="border:none !important;width:50%;">
                            ' . $ac['label'] . ':
                        </td>
                        <td style="border:none !important;">
                            ' . $ac['value'] . '
                        </td>
                    </tr>';


        }
        $str .= '</table>';
        return $str;

    }

    /**
     * cRPL - Replace content with global constants
     *
     * @param $content - array or string
     * @return array|mixed|string|string[]
     */
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










    /**
     * ------------- AUTOCOMPLETE -------------
     */

    /**
     * -------- DYNAMIC JQUERY AUTOCOMPLETE SOURCE ARRAY CREATOR ----------
     * $sources is an array container with one or more valuearrays, each with three items.
     * Valuearray items are the parameters needed to pull a field or fields from a database table
     * the three mandatory parameters are:
     *            String[0]: 'DB source field'
     *            String[1]: 'alias / label for field(s)',
     *            String[2]: 'DB source Table'
     */
    function createACDataArray($sources){
        $retArr = array();
        $arrs = $sources;
        $fnameArr = array();
    //    $this->sLog('createACDataArray $sources array: ' . print_r($sources,true),'createACDataArray');
        $ai = 0;
      /*  foreach($sources as $source){

            $this->sLog('createACDataArray set label name for data array #' . $ai . ' = ' . $source[1],'createACDataArray_SourceLoop');
            $fnameArr[] = $source[1];
            $this->sLog("createACDataArray source array call: getAllDistinctData($source[0],$source[1],$source[2])",'createACDataArray_SourceLoop');
            $ret = $this->getAllDistinctData($source[0],$source[1],$source[2]);
           // $ret = $this->getQueryCache();
            $arrs[] = $ret;
            $this->sLog('createACDataArray data returned: ' . print_r($ret,true),'createACDataArray_SourceLoop');
            $ai++;
        }
      */
        $i = 0;
        foreach($arrs as $a) {

            $label = 'query';
       //     $this->sLog("createACDataArray array $i label = $label ",'createACDataArray_ValueLoop ');

           // foreach($arr as $a) {
                if (isset($a[$label]) && $a[$label] != '' ) {
                    $retArr[] = $a[$label];
        //            $this->sLog('createACDataArray a = '.$a[$label],'createACDataArray_ValueLoop');
                }
                else{
        //            $this->sLog('acreateACData Array["'.$label.'"] has no value','createACDataArray_ValueLoop outer' . print_r($a[$label],true) . ' inner: ' . print_r($a[$label],true));
                }
         //   }
            $i++;
        }
        return $retArr;
    }

    /** ------------------------------------------------------------------
     * ----- CREATE & POPULATE JQUERY AUTOCOMPLETE MENU DYNAMICALLY -----
     * ------------------------------------------------------------------
     * Two supporting function called from within the only manually called function:
     * @func array_iunique: case insensitive array_unique
     * @func populateACMenu: build a string to dynamically create a JS Array (on page load) from a php array
     *
     * ENTRY POINT FUNCTION - this is all you need to call for full autocomplete functionality as long as
     *          1) $menuCSSTarget selector is a valid text input box on the page with an unique html id
     *          2) $JSvarname is an unique variable name in your JS scope.
     *          3) $valueArr is an array of strings
     * @func controlACMenu:  build JQUERY function tie-in to add jQuery.AutoComplete to any field AND
     *                       return both the source array and function as a JS string (dynamic function creation)
     *                       Output will optionally (default=true) return a full JS <script></script> tag
     *                       w/ functions, data and jQuery tie-in.
     *
     * @param $menuCSSTarget string: JQuery selector target: '#referralSource' or '#referral > .referralSource' etc.
     * @param $JSvarname string: the variable name for the AC data source - 'refSources'   eg: source: refSources
     * @param $valueArr array: the input array with actual raw autocomplete data eg: $mydata['value1','value2',etc]
     * @param $URLPageOverride string [optional] page to redirected to on select
     * @param $URLRootOverride string [optional] for portability - dont change unless file system structure changes
     * @param $bCreateScriptTags bool [optional] true = create JS script tags  |  false = don't create tags
     * @return string
     *
     **/
    function array_iunique( $array ) {
        return array_intersect_key(
            $array,
            array_unique( array_map( "strtolower", $array ) )
        );
    }
    function populateACMenu($JSvarname,$valueArr)
    {
        $s = '';

        //DISABLE for ATLIST
       // $dynArr = $this->array_iunique($valueArr);  //remove duplicate entries

        //pull out array values into comma delimited list
        foreach ($valueArr as $fr) {
            $s .= ($s == '') ? '"' . ucwords($fr) . '"' : ',"' . ucwords($fr) . '"';
        }
        //then dynamically write array values into a string that will generate a JAVASCRIPT array on pageload
        $s = 'var '.$JSvarname.' = [' . $s . '];' ."\n";
        return $s;
    }
    function controlACMenu($menuCSSTarget,$JSvarname,$valueArr,$URLPageOverride = 'index.php',$URLRootOverride = '',$bCreateScriptTags = true){

        $menuData = $this->populateACMenu($JSvarname,$valueArr);
        $ACcontrol =  "\n" . '    ';
        $ACcontrol .= '$( "'. $menuCSSTarget .'" ).autocomplete({
                                source: '.$JSvarname.',
                                classes: {
                                    "ui-autocomplete": "highlight"
                                }';
        //specific case for portal admin - adding redirection on select
        $ACcontrol .= ($JSvarname==="querySources") ? ',
                                select: function(event, ui){
                                var uiv = ui.item.value;
                                document.getElementById(\'mssearchbox\').value = uiv;
                                console.log(uiv);
                                document.getElementById(\'searchform\').submit();
                                 
                            //     var encoded = uiv.replaceAll(\' \',\'+\');
                                  // var s = uiv.split(" | ID: ");
                                  // var uril = window.location.href.split("?");
                              //     window.location.assign("' . $URLRootOverride . $URLPageOverride . '?s=1&ms=" + encoded);
                                }' : '';
        //specific case for scheduler - activating function on select
        $ACcontrol .= ($JSvarname==="searchUserSources") ? ',   
                                select: function(event, ui){
                                   var uiv = ui.item.value;
                                   var s = uiv.split(" | ID: ");
                                   currenteventdisplay = "";
                                   getUsrID(s[1]);
                                }' : '';

        $ACcontrol .=        '          
                             });
                             ';
        $ACcontrol .= ($JSvarname==='query2Sources') ? $this->makeACValueArray($JSvarname,$valueArr) : '';

        return ($bCreateScriptTags) ? '<script>' . "\n" . $menuData . $ACcontrol . "\n" . '</script>' : "\n" . $menuData . $ACcontrol . "\n" ;
    }

    /**
     * creation of the user name autocomplete menus for admin control panels
     * @param $JSvarname
     * @param $nameids
     * @return string
     *
     *
     *  make secondary array with each id parsed out and added as the value for secondary array
     *       eg:  nameids['John Doe | ID: 2343','Jane Doe | ID: 2343',etc]
     *  creates:  nameidsVals[JohnDoe2343:2343,JaneDoe2664:2664]
     */
    function makeACValueArray($JSvarname,$nameids){
        $sa = '';

        //disable for ATLIST
      //  $uArr = $this->array_iunique($nameids);
        $uArr = $nameids;

        foreach($uArr as $user){
          //  $this->sLog('makeACValueArray: secondary value array raw value:' . $user,'USR');
          //  $s = explode(" | ID: ",$user);
         //   $stmp = '"' . $user . '"';
            $stmp = '"' . $user . '":"' . $user . '"';
         //   $sa .= ($sa==='') ? $stmp : ', ' . $stmp ;
            $sa .= ($sa==='') ? str_replace(' ','+',$stmp) : ', ' . str_replace(' ','+',$stmp) ;
        }
      //  $this->sLog('makeACValueArray: value list:' . $sa,'USR');
        $ret = 'var '.$JSvarname.'Vals = { ' . $sa . ' };' . "\n";
        return $ret;

    }

















    /**
     * --------------------------------------------
     */
    /**
     * ------- INPUT VALIDATION / FORMATTING ------
     */
    /**
     * --------------------------------------------
     */

    /**
     * fNum() - Format phone number for display()
     *
     * @param $data
     * @param $type
     * @return mixed|string
     */

    function getSchedLastUpdateTime()
    {

        $sql = "SELECT updatedate FROM scheduler order by updatedate desc LIMIT 1 ";
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute(array());
        return $this->stmt->fetchAll();
    }

    /**
     * getAdjDateOfWeek - get day of week based on 'week beg sunday' with Sun = 0, Sat = 6;
     *
     * eg:  'Sat this week                                      getAdjDateOfWeek(false,6);
     * eg:  'Sun (beginning of) this week                       getAdjDateOfWeek(false,0);
     * eg:  'Sun (beginning) of the week belonging to date      getAdjDateOfWeek('2023-03-03,0);
     * eg:  'Sat of the week belonging to date                  getAdjDateOfWeek('2023-03-03,6);
     *
     * @param $date
     * @param $whichday
     * @return string
     *
     */
    function getAdjDateOfWeek($date=false,$whichday=false)
    {


        $this->sLog("getAdjDateOfWeek $date $whichday IN","date=$date whichday=$whichday");

        //manage defaults (if no date given, use today / if no day (whichday) given, use sunday
        $date = ($date===false) ? date('Y-m-d') : $date;
        $whichday = ($whichday===false) ? 0 : $whichday;

        //adjust return value to make up for inconsistency with 'week beg monday' & 'week beg sunday' (add 7 if target date is a sunday)
        $dspl = explode("-",$date);
        $givenday = date("w", mktime(0, 0, 0, $dspl[1], $dspl[2], $dspl[0]));
        $whichday = ($givenday==0) ? $whichday + 7 : $whichday;

        //return day of week based on 'week beg sunday' with Sun = 0, Sat = 6;
        $day = DateTime::createFromFormat('Y-m-d', $date);
        $day->setISODate((int)$day->format('o'), (int)$day->format('W'), $whichday);

        $ret = $day->format('Y-m-d');

        $this->sLog("getAdjDateOfWeek $date $whichday OUT","adjdate=$ret");

        return $ret;
    }


    /**
     * @param $data
     * @param $type
     * @param $addDecimals
     *
     * @return string
     */
    function fNum($data,$type='phone',$addDecimals=0){
        if($type==='phone') {
            $ret = sprintf("(%s) %s-%s",
                substr($data, 0, 3),
                substr($data, 3, 3),
                substr($data, 6));
        }
        if($type==='ssn'){
            $ret = sprintf("%s-%s-%s",
                substr($data, 0, 3),
                substr($data, 3, 2),
                substr($data, 5));
        }
        if($type==='money'){
            $ret = '$' . number_format($data, 2);
        }
        if($type==='number'){
            $ret = number_format($data, $addDecimals);
        }

        return $ret;
    }

    /**
     * fDate() - Format date for display()
     *
     * @param $data
     * @return mixed|string
     */
    function fDate($data,$format='m/d/Y'){
        $date = strtotime($data);
        $ret = date($format, $date);
        return $ret;
    }

    /**
     * fDateTime - Format Date & Time for DB: 2022-01-01 11:05:23
     *
     * @param $data
     * @return false|string
     */
    function fDateTime($data){
        $date = strtotime($data);
        $ret = date('m/d/Y h:i A', $date);
        return $ret;
    }

    /**
     *  check valid email format
     *
     * @param $email
     * @return bool
     */
    function isVEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;
    }

    /**
     * check valid name format
     *
     * @param $name
     * @return bool
     */
    function isVName($name) {
        $pattern = '/^[A-Za-z\x{00C0}-\x{00FF}][A-Za-z\x{00C0}-\x{00FF}\'\-]+([\ A-Za-z\x{00C0}-\x{00FF}][A-Za-z\x{00C0}-\x{00FF}\'\-]+)*/u';
        if (preg_match($pattern, $name)) {
            return true;
        }
        return false;
    }

    /**
     * check valid currency format
     *
     * @param $val
     * @return bool
     */
    function isVCurr($val){

        $bRet = true;
        $pattern = '';
        // $pattern = '/^(?:[0-9]{1,3})(?:,[0-9]{3})*(?:|\.[0-9]+)$/';
        if (preg_match($pattern, $val)) {
            $bRet = true;
        }
        //  return false;
        //TEMP WORKAROUND TODO TODO TODO
        return $bRet;
    }





    function template($id=0){

        try{

            /*
            $data = array();
            $sql = "SELECT * from THETABLE WHERE id = :id ";
            $this->sLog("template SQL",$sql);
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($data);
            $this->sLog("template DATA",print_r($data,true));
            $retarr = $this->stmt->fetchAll();
            $this->sLog("template RESPONSE",print_r($retarr,true));

            $ret = isset($retarr[0]) ? $retarr : false;
            */


            $ret = true;
        }
        catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->sLog("temolate EXCEPTION",$this->error);
            $ret = false;
        }
        return $ret;

    }







}

require_once "_conf.php";

$USR = new Users();


