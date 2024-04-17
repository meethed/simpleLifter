<?php
include_once("../includes/config.inc");
$cL=$_SESSION["compLetters"];
$cL="AAB";
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);
//var_dump($data);

$statement="UPDATE ".$cL. " set gp=?,lot=?,name=?,team=?,year=?,agediv=?,gender=?,gear=?,lifts=?,bw=?,wc=?,division=?,sr=?,br=?,sq1=?,sq2=?,sq3=?,bsq=?,bp1=?,bp2=?,bp3=?,bbp=?,st=?,dl1=?,dl2=?,dl3=?,bdl=?,total=?,formula=?,teampoints=?,session=?,pt=?,sa1=?,sa2=?,sa3=?,ba1=?,ba2=?,ba3=?,da1=?,da2=?,da3=?,lighthistory=?,pbb=?,pbs=?,pbd=?,pbt=?,isActive=?,liftidx=?,place=? where idx=?";

$sql=$conn->prepare($statement);
$sql->bind_param("sisssssssdssssdddddddddddddddisdiiiiiiiiisddddiiii",
$data->gp,
$data->lot,
$data->name,
$data->team,
$data->year,
$data->agediv,
$data->gender,
$data->gear,
$data->lifts,
$data->bw,
$data->wc,
$data->division,
$data->sr,
$data->br,
$data->sq1,
$data->sq2,
$data->sq3,
$data->bsq,
$data->bp1,
$data->bp2,
$data->bp3,
$data->bbp,
$data->st,
$data->dl1,
$data->dl2,
$data->dl3,
$data->bdl,
$data->total,
$data->formula,
$data->teampoints,
$data->session,
$data->pt,
$data->sa1,
$data->sa2,
$data->sa3,
$data->ba1,
$data->ba2,
$data->ba3,
$data->da1,
$data->da2,
$data->da3,
$data->lighthistory,
$data->pbb,
$data->pbs,
$data->pbd,
$data->pbt,
$data->isActive,
$data->liftidx,
$data->place,
$data->idx);

$sql->execute();
print_r($data);
echo $conn->error;
 ?>

