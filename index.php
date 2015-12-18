<?php
// Misthodosia ESPA
// ESPA CSV parser and displayer
//
// 1. Authenticates user with surname & afm.
// 2. Scans a folder for csv files (from http://bglossa.ypepth.gr)
// 3. Parses each file, finding the requested user
// 4. Displays results on a user friendly table
//
// by Vangelis Zacharioudakis (http://github.com/sugarv)

// For debugging...
//error_reporting(E_ALL);
//ini_set('error_reporting', E_ALL);

// Imports
require './vendor/autoload.php';
require 'config.php';
require 'functions.php';
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Μισθοδοσία Αναπληρωτών ΕΣΠΑ</title>
  </head>
  <body>

    <!-- Include essentials -->
		<script src="lib/jquery-1.11.3.min.js"></script>
		<script src="lib/bootstrap.min.js"></script>
		<link rel="stylesheet" href="lib/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="lib/espa-csv.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a class="navbar-brand" href="#">Μισθοδοσία ΕΣΠΑ</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a class="navbar-brand pull-right" href="<?= $dnsiLink ?>" target="_blank"><small><?= $dnsiStrShort ?></small></a></li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <div class="jumbotron">
      <div class="container">
        <h2><i class="fa fa-money fa-4x"></i>&nbsp;&nbsp;Μισθοδοσία Αναπληρωτών ΕΣΠΑ</h2>
				<h3><?= $dnsiStr?></h3>
        <p>Ενημέρωση μισθοδοσίας αναπληρωτών ΕΣΠΑ</p>
      </div>
    </div>

<?php

// if not logged-in
if (isset($_GET['logout'])) {
  $_POST['inputAfm'] = $_POST['inputSurname'] = NULL;
}
if (!isset($_POST['inputAfm']) || !isset($_POST['inputSurname']) || !strlen($_POST['inputAfm']) || !strlen($_POST['inputSurname']))
{
 ?>
	<div class="container">
      <div class="row">
					<div class="col-md-8">
	          <h2>Είσοδος στο σύστημα</h2>
					<form action='index.php' class="form-horizontal" method="post" role="form">
			        <h4 class="form-signin-heading">Παρακαλώ εισάγετε τα στοιχεία σας:</h4>
			        <label for="inputSurname">Επώνυμο</label>
			        <input type="text" name="inputSurname" class="form-control" placeholder="Επώνυμο" required autofocus>
			        <label for="inputAfm" >ΑΦΜ</label>
			        <input type="password" name="inputAfm" class="form-control" placeholder="ΑΦΜ" required>
                 <br>
			        <input id="submit" class="btn btn-lg btn-primary btn-block" type="submit" value="Είσοδος">
			      </form>

	        </div>
	        <div class="col-md-4">
	          <h2>Βοήθεια</h2>
	          <p>Εισάγετε ΜΟΝΟ το <strong>ΕΠΩΝΥΜΟ</strong> σας (με ελληνικούς χαρακτήρες) και το <strong>Α.Φ.Μ.</strong> σας.</p>
	       </div>
		</div>
   </div>

<?php
}

// if user has filled form...
else {
    // initialize vars
    $inpAfm = $_POST['inputAfm'];
    $inpSurname = $_POST['inputSurname'];
    $found = $single = $hasAnadr = 0;
    $allRecords = [];
    $tmStr = 'Τ.Μ.';
    $adStr = 'ΑΔ.ΑΣΘ.';
    $synStr = 'ΣΥΝΟΛΟ';
    $anadrStr = 'ΑΝΑΔΡΟΜΙΚΑ';

    // find all csv files in csv directory
    $csvFiles = glob("csv/*.csv");

    // find employee in files
    foreach ($csvFiles as $csvFile) {
        // call parseFind to parse csv file, search,find & get results
        $data = parseFind($csvFile, $inpAfm, $inpSurname);
        $parsed = $data['parsed'];
        $month = $data['month'];
        if (count($parsed)>0){
          $found = 1;
          $allRecords[$month] = $parsed;
          $months[] = $month;
        }
    } //of foreach

    // if not found, display error
    if (!$found){
      echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-4\">";
      echo "<p>Ο υπάλληλος δε βρέθηκε ή έχετε καταχωρήσει λανθασμένα στοιχεία...</p><br><br></div></div>";
      echo "<div class=\"row\"><div class=\"col-md-2\"><a href=\"index.php\" class=\"btn btn-lg btn-primary btn-block\" >Επιστροφή</a>";
      echo "</div></div></div>";
      exit();
    }
    // log to file
    if ($canLog)
      logToFile($inpAfm, $logFile);
?>
   <div class="container">
      <!-- Create nav-tabs -->
      <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
          <ul class="nav nav-tabs">
            <?php
            $first = 1;
            foreach ($months as $mon) {
              echo "<li ";
              echo $first ? "class=\"active\"" : '';
              echo "><a data-toggle=\"tab\" href=\"#$mon\">$mon</a></li>";
              $first = 0;
            }
            ?>
          </ul>
        </div>
      <!-- Create tab-content -->
      <div class="panel-body">
      <div class="tab-content">
      <?php
       $first = 1;
       // for each month
       foreach ($allRecords as $month => $recordSet) {
             $tm = $synolo = $anadromika = [];
             $synolo_ap = $synolo_asf = $synolo_for = $synolo_kath = 0;
             $adeies = [];
             // check if multi records
             if (count($recordSet) == 1) {
               $single = 1;
               $tm = $recordSet[0];
             }
             else {
               // find T.M., adeia etc
               foreach ($recordSet as $rec) {
                   // find record types
                   //print_r($rec);
                   if (array_key_exists($hdr['ΕΙΔ. ΑΠ.'], $rec)) {
                     $eidap = $rec[$hdr['ΕΙΔ. ΑΠ.']];
                     if ($eidap == $tmStr)
                      $tm = $rec;
                     elseif ($eidap == $adStr)
                      $adeies[] = $rec;
                     elseif ($eidap == $anadrStr)
                      $anadromika = $rec;
                   }
                 }
             } // of else multi
             if (!$tm)
              continue;
         // start building table @ month tab
         ?>
         <div id=<?= $month?> class="tab-pane fade in <?= $first ? 'active' : '' ?> ">
            <?php
               $ret = renderTable($tm, $hdr);
               $outPut = $ret['out'];
               $synolo_ap += $ret['apod'];
               $synolo_asf += $ret['asfal'];
               $synolo_for += $ret['foros'];
               $synolo_kath += $ret['kath'];

               if ($adeies) {
                  $outPut .= "<h3>ΑΔΕΙΕΣ</h3>";
                  foreach ($adeies as $ad){
                     $ret = renderTable($ad,$hdr);
                     $outPut .= $ret['out'];
                     $synolo_ap += $ret['apod'];
                     $synolo_asf += $ret['asfal'];
                     $synolo_for += $ret['foros'];
                     $synolo_kath += $ret['kath'];
                  }
               }
               if ($anadromika) {
                  $outPut .= "<h3>ΑΝΑΔΡΟΜΙΚΑ</h3>";
                  $ret = renderTable($anadromika,$hdr,1);
                  $outPut .= $ret['out'];
                  $synolo_ap += $ret['apod'];
                  $synolo_asf += $ret['asfal'];
                  $synolo_for += $ret['foros'];
                  $synolo_kath += $ret['kath'];
               }
               // if anadromika or adeies, print totals
               if ($anadromika || $adeies) {
                 $outPut .= renderSynola($synolo_ap, $synolo_asf, $synolo_for, $synolo_kath);
               }
               echo $outPut;
            ?>
             </div>
             <?php
             $first = 0;
           } // of foreach
          ?>
        </div> <!-- of tab-content-->
      </div> <!-- of panel-body -->
    </div> <!-- of panel -->
      <div class="row">
        <div class="col-md-2">
            <a href="index.php?logout=1" class="btn btn-lg btn-primary btn-block" >Έξοδος</a>
        </div>
      </div>
   </div> <!-- of container -->
<?php
} // of else user filled form
?>
   <footer>
     <div class="container">
       <div class="row">
         <div class="col-md-12">
           <br>
           <p><small>&copy; B.Ζαχαριουδάκης<br><a href="mailto:it@dipe.ira.sch.gr">Τμ. Μηχανογράφησης ΔΙ.Π.Ε. Ηρακλείου</a>, 2015</small></p>
         </div>
       </div>
     </div>
   </footer>

</body>
</html>
