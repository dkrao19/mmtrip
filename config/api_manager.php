<?php
require_once __DIR__."/database.php";

/* Check enabled */
function apiEnabled($key){
  global $pdo;
  $s=$pdo->prepare("SELECT is_enabled FROM api_settings WHERE api_key=?");
  $s->execute([$key]);
  return (bool)$s->fetchColumn();
}

/* Circuit breaker check */
function circuitOpen($key){
  global $pdo;
  $s=$pdo->prepare("
    SELECT state FROM api_circuit_breaker WHERE api_key=?
  ");
  $s->execute([$key]);
  return $s->fetchColumn()==='OPEN';
}

/* Log usage */
function logApiUsage($key,$endpoint,$ms,$success,$error=null){
  global $pdo;
  $pdo->prepare("
    INSERT INTO api_usage_logs
    (api_key,endpoint,response_time_ms,success,error_message)
    VALUES (?,?,?,?,?)
  ")->execute([$key,$endpoint,$ms,$success,$error]);
}

/* Failure handler */
function registerFailure($key){
  global $pdo;
  $pdo->prepare("
    INSERT INTO api_circuit_breaker (api_key,failure_count,state,last_failure)
    VALUES (?,1,'OPEN',NOW())
    ON DUPLICATE KEY UPDATE
      failure_count=failure_count+1,
      state=IF(failure_count>=3,'OPEN','HALF_OPEN'),
      last_failure=NOW()
  ")->execute([$key]);
}

/* Success handler */
function registerSuccess($key){
  global $pdo;
  $pdo->prepare("
    UPDATE api_circuit_breaker
    SET failure_count=0,state='CLOSED'
    WHERE api_key=?
  ")->execute([$key]);
}
