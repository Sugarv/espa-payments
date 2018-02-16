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
		<script src="vendor/components/jquery/jquery.min.js"></script>
		<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
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
            <li><a class="navbar-brand" href="index.php">Μισθοδοσία ΕΣΠΑ/ΠΔΕ</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a class="navbar-brand pull-right" href="<?= $dnsiLink ?>" target="_blank"><small><?= $dnsiStrShort ?></small></a></li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <div class="jumbotron">
      <div class="container">
        <h2><i class="fa fa-money fa-4x"></i>&nbsp;&nbsp;Μισθοδοσία Αναπληρωτών ΕΣΠΑ/ΠΔΕ</h2>
				<h3><?= $dnsiStr?></h3>
        <p>Ενημέρωση μισθοδοσίας αναπληρωτών ΕΣΠΑ/ΠΔΕ</p>
      </div>
    </div>

<?php

// if not logged-in
if (isset($_GET['logout'])) {
  $_POST['inputAfm'] = $_POST['inputSurname'] = NULL;
}
if (!isset($_POST['inputAfm']) || strlen($_POST['inputAfm']) != 9 || !isset($_POST['inputSurname']) || !strlen($_POST['inputAfm']) || !strlen($_POST['inputSurname']))
{
  $wrongAfm = isset($_POST['inputAfm']) && strlen($_POST['inputAfm']) != 9;
 ?>
	<div class="container">
    <div class="row">
        <div class="col-md-8">
          <h2>Είσοδος στο σύστημα</h2>
        <form id='login-form' class="form-horizontal" method="post" role="form">
            <h4 class="form-signin-heading">Παρακαλώ εισάγετε τα στοιχεία σας:</h4>
            <label for="inputSurname">Επώνυμο</label>
            <input type="text" name="inputSurname" class="form-control" placeholder="Επώνυμο" required autofocus>
            <div class="form-group <?= $wrongAfm ? 'has-error' : '';?>" style="margin:0px;">
              <label for="inputAfm" >ΑΦΜ</label>
              <input id="afm" type="password" name="inputAfm" class="form-control" placeholder="ΑΦΜ" required>
              <?= $wrongAfm ? '<span id="helpBlock2" class="help-block">Λανθασμένο ΑΦΜ (πρέπει να έχει 9 ψηφία)</span>' : '';?>
            </div>
                <br>
            <input id="submit" class="btn btn-lg btn-primary btn-block" type="submit" value="Είσοδος">
          </form>
              <small><?= getLatestMonth(); ?></small>

        </div>
        <div class="col-md-4">
          <h2>Βοήθεια</h2>
          <p>Εισάγετε ΜΟΝΟ το <strong>ΕΠΩΝΥΜΟ</strong> σας (με ελληνικούς χαρακτήρες) και το <strong>Α.Φ.Μ.</strong> σας.</p>
          <br>
          <p><?= $customMessage; ?></p>
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
    $yperStr = 'ΥΠΕΡΩΡΙΕΣ';
    $afStr = 'ΑΦΑΙΡΟΥΜΕΝΟ ΠΟΣΟ';
    
    // init pdf output
    $pdfOutput = "<h4>$dnsiStr</h4><h3>Ενημέρωση μισθοδοσίας αναπληρωτών ΕΣΠΑ/ΠΔΕ</h3>";

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
             $tm = $synolo = $anadromika = $yperwries = $afair = [];
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
                   // find T.M. or ADEIA
                   if (array_key_exists($hdr['ΕΙΔ. ΑΠ.'], $rec)) {
                     $eidap = $rec[$hdr['ΕΙΔ. ΑΠ.']];
                     if ($eidap == $tmStr)
                      $tm = $rec;
                     elseif ($eidap == $adStr)
                      $adeies[] = $rec;
                     elseif ($eidap == $anadrStr)
                      $anadromika = $rec;
                   }
                   // find YPERWRIA or ANADROMIKA or AFAIROYMENO POSO
                   if (array_key_exists($hdr['Μ.Κ.(BM)'], $rec)) {
                     $comp = $rec[$hdr['Μ.Κ.(BM)']];
                     if ($comp == $anadrStr)
                      $anadromika = $rec;
                     elseif ($comp == $yperStr)
                      $yperwries = $rec;
                     elseif ($comp == $afStr)
                      $afair = $rec;
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
                  $ret = renderSpecial($anadromika,$hdr);
                  $outPut .= $ret['out'];
                  $synolo_ap += $ret['apod'];
                  $synolo_asf += $ret['asfal'];
                  $synolo_for += $ret['foros'];
                  $synolo_kath += $ret['kath'];
               }
               if ($afair) {
                  $outPut .= "<h3>ΑΦΑΙΡΟΥΜΕΝΟ ΠΟΣΟ</h3>";
                  $ret = renderSpecial($afair,$hdr);
                  $outPut .= $ret['out'];
                  $synolo_ap -= $ret['apod'];
                  $synolo_asf -= $ret['asfal'];
                  $synolo_for -= $ret['foros'];
                  $synolo_kath -= $ret['kath'];
               }
               if ($yperwries) {
                  $outPut .= "<h3>ΥΠΕΡΩΡΙΕΣ</h3>";
                  $ret = renderSpecial($yperwries,$hdr);
                  $outPut .= $ret['out'];
                  $synolo_ap += $ret['apod'];
                  $synolo_asf += $ret['asfal'];
                  $synolo_for += $ret['foros'];
                  $synolo_kath += $ret['kath'];
               }
               // if anadromika or adeies, print totals
               if ($anadromika || $adeies || $yperwries) {
                 $outPut .= renderSynola($synolo_ap, $synolo_asf, $synolo_for, $synolo_kath);
               }
               $pdfOutput .= '<h3>' . $month .'</h3><br>' . $outPut . '<pagebreak />';
               echo $outPut;
            ?>
             </div>
             <?php
             $first = 0;
           } // of foreach
           // Remove last pagebreak
           $pdfOutput = substr($pdfOutput, 0, -13);
          ?>
        </div> <!-- of tab-content-->
      </div> <!-- of panel-body -->
	  <div class="panel-footer">
    <?php if ($exportPdf){ ?>
		<div class="row">
			<div class="col-md-3 col-sm-3">
        <button id="pdfButton" type="button" name="button" class="btn btn-sm btn-success btn-block">Εξαγωγή όλων σε PDF</button>
			</div>
      <div id="postData" style="display: none;">
        <?= htmlspecialchars($pdfOutput)?>
      </div>
      <div id="pdfLink"></div>
		</div>
    <?php } ?>
	  </div>
    </div> <!-- of panel -->
      <div class="row">
        <div class="col-md-2 col-sm-2">
            <a href="index.php?logout=1" class="btn btn-lg btn-danger btn-block" >Έξοδος</a>
        </div>
      </div>
   </div> <!-- of container -->
<?php
} // of else user filled form
?>
   <footer>
     <div class="container">
       <div class="row">
         <div class="col-md-11 col-sm-11">
           <br>
           <p><small>&copy; B.Ζαχαριουδάκης<br><a href="mailto:it@dipe.ira.sch.gr">Τμ. Μηχανογράφησης ΔΙ.Π.Ε. Ηρακλείου</a>, 2015-17</small></p>
         </div>
         <div class="col-md-1 col-sm-1">
           <br>
           <a href="https://github.com/dipeira/espa-payments" target="_blank" title="Github"><i class="fa fa-2x fa-github" aria-hidden="true"></i></a>
         </div>
       </div>
     </div>
   </footer>
<?php
  // Clean up old pdf files
  clean_up($cleanUpAfter);
?>
</body>
<script type = "text/javascript">
     $(document).ready(function() {
       var div = document.getElementById("postData");
       var myData = div.textContent;
       var userAfm = <?= $inpAfm ?>;
        $("#pdfButton").click(function(event){
            $.post(
              "pdf.php",
              { afm: userAfm, data: myData },
              function(data) {
               $('#pdfLink').html(data);
              }
           );
        });
     });      
  </script>
</html>
