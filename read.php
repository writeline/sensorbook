﻿<?php
// The MIT License (MIT) 
 
// Copyright (c) 2014 Microsoft DX  
 
// Permission is hereby granted, free of charge, to any person obtaining a copy 
// of this software and associated documentation files (the "Software"), to deal 
// in the Software without restriction, including without limitation the rights 
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
// copies of the Software, and to permit persons to whom the Software is 
// furnished to do so, subject to the following conditions: 

// The above copyright notice and this permission notice shall be included in all 
// copies or substantial portions of the Software. 
 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
// SOFTWARE. 
require_once('clientconfig.php'); 

try{
	  $timestamp = time();
    
    $scon_pipename = $_GET["p"];
    if(!$scon_pipename) {
      echoresult("ERROR", $timestamp, "no identifier");
      die(); 
    }
    
    $scon_clientkey = $_GET["c"];
    $scon_clientname = $_GET["cn"];
     	
    $conn = new PDO( "sqlsrv:Server= $server ; Database = $db ", $user, $pwd);
    if($conn == null) 
    {
        echoresult("ERROR",$timestamp,"db error");
        die();  
    }
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $scon = CheckPermission($conn, $scon_pipename, $scon_clientkey);
    
    if($scon==null) {
      echoresult("ERROR", $timestamp, "db error");
      die(); 
    }
    
    if(!$scon) {
      $sql = $conn->prepare("SELECT * FROM serialconnections WHERE pipename = ?");
      $sql->bindValue(1,$scon_pipename); 
      $sql->execute();
      $scon = $sql->fetchAll();
    } 
    if(count($scon)!=1) {
      echoresult("ERROR", $timestamp, "not found");
      die(); 
    }
    else {
      $m = $scon[0]['messageout'];
      if($scon[0]['clientreadmode']=="FOR") {
        $mo = ""; 
        $qry=$conn->prepare("UPDATE serialconnections SET date=?, messageout=? WHERE pipename=?");
        $qry->bindValue(1,$timestamp); 
        $qry->bindValue(2,$mo); 
        $qry->bindValue(3,$scon_pipename);
        $result = $qry->execute();
      }
    }
}
catch(Exception $e){
    die(print_r($e));
}
echoresult("OK", $timestamp, $m);
?>
