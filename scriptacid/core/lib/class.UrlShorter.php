<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

/**
 * Укорачивет URL
 *
 * @author r3c130n
 */
class URLShorter extends GetSet{
    public $sourceURL;
        public $hashID;
        public $hashStr;

        //$letters = '0123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
        private $letters = '0123456789abcdefghijklmnopqrstuvwxyz';
        private $defaultURL = 'http://www.scriptacid.ru/';

        private $URLShorterTable = 'urls';

        function __construct($sourceURL) {
                $this->sourceURL = $sourceURL;
                // Зарезервировано
        }

        function Execute($sourceURL = false) {
                if ($sourceURL !== false)
                        $this->sourceURL = $sourceURL;
                if (isset($this->sourceURL) && !empty($this->sourceURL)) {
                        $arURL = $this->checkHash($this->sourceURL);
                        if (!empty($arURL)) {
                                $url = urldecode($arURL['URL']);
                                $this->updHashCnt($arURL['ID']);
                                header('Location: ' . $url);
                                exit;
                        }
                } else {
                        header('Location: ' . $this->defaultURL);
                        exit;
                }
        }

        public function HashMe($hashID) {
                if ($hashID !== false)
                        $this->hashID = $hashID;
                
                $sys = strlen($this->letters);
                $sys2 = pow($sys, 2);
                $sys3 = pow($sys, 3);

                $qbig = floor($id / $sys3);
                $dbig = floor(($id - $qbig*$sys3) / $sys2);
                $big = floor(($id - $qbig*$sys3 - $dbig*$sys2) / $sys);
                $small = $id - $qbig * $sys3 - $dbig * $sys2 - $big * $sys;

                $a = $qbig > 0 ? substr($this->letters, $qbig - 1, 1) : '';
                $b = $dbig > 0 ? substr($this->letters, $dbig - 1, 1) : '';
                $c = $big > 0 ? substr($this->letters, $big - 1, 1) : '';
                $d = $small > 0 ? substr($this->letters, $small - 1, 1) : '';

                return $a . $b . $c . $d;
        }

        public function DeHashMe($hashStr = false) {
                if ($hashStr !== false)
                        $this->hashStr = $hashStr;
                
                $sys = strlen($this->letters);
                $sys2 = pow($sys, 2);
                $sys3 = pow($sys, 3);
                $ar = Array();

                for ($c = 0; $c < strlen($this->hashStr); $c++) {
                        $str = substr($this->hashStr, $c, 1);
                        $ar[] = (strpos($this->letters, $str) + 1);
                }

                switch (count($ar)) {
                        case 1:
                                return $ar[0];
                        case 2:
                                return $ar[0] * $sys + $ar[1];
                        case 3:
                                return $ar[0] * $sys2 + $ar[1] * $sys + $ar[2];
                        case 4:
                                return $ar[0] * $sys3 + $ar[1] * $sys2 + $ar[2] * $sys + $ar[3];
                        case 0:
                        default:
                                return;
                }
        }

        public function saveURL($sourceURL) {
                $this->sourceURL = $sourceURL;
                $src = $this->getByURL();
                if (!empty($src)) {
                        return $this->HashMe($src['ID']);
                }
                return $this->addURLToDB();
        }

        public function checkHash($hashID = false) {
                if ($hashID !== false)
                        $this->hashID = $hashID;
                $arHash = $this->getByHash();
                if (!empty($arHash))
                        return $arHash;
                return false;
        }

        public function addURLToDB() {
                $DB = App::DB();
                $sql = "INSERT INTO `{$this->URLShorterTable}` (`URL`) VALUES ('{$this->sourceURL}');";
                $DB->Query($sql);
                $this->hashID = $DB->LastID();
                return $this->HashMe($this->hashID);
        }

        public function getByHash($hashStr = false) {
                $DB = App::DB();

                if ($hashStr !== false)
                        $this->hashStr = $hashStr;
                
                $this->hashID = $this->DeHashMe($this->hashStr);
                $sql = "SELECT * FROM `{$this->URLShorterTable}` WHERE `ID` = '{$this->hashID}';";
                $DB->Query($sql);
                return $DB->Fetch();
        }

        

        public function getByURL($sourceURL = false) {
                $DB = App::DB();
                if ($sourceURL !== false)
                        $this->sourceURL = $sourceURL;
                $this->sourceURL = urlencode($this->sourceURL);
                $sql = "SELECT * FROM `{$this->URLShorterTable}` WHERE `URL` = '{$this->sourceURL}';";
                $DB->Query($sql);
                return $DB->Fetch();
        }

        public function updHashCnt($hashID = false) {
                $DB = App::DB();
                
                if ($hashID !== false)
                        $this->hashID = $hashID;
                
                $sql = "UPDATE `{$this->URLShorterTable}` SET `CNT` = CNT + 1 WHERE `ID` = '{$this->hashID}';";
                $DB->Query($sql);
        }
}
//(isset($_GET['add']) && !empty($_GET['add'])) {
//              echo 'Короткая ссылка: ' . $sURL->saveURL($_GET['add']);


//$sURL = new URLShorter();
//$sURL->Execute();
?>