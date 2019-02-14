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

require_once('head.php');
?>
<body>
<?php
require_once('menu.php');
?>
	<div class="container">
    <div class="row">
        <div class="col-md-8">
          <h2>Είσοδος στο σύστημα</h2>
        <form id='login-form' class="form-horizontal" method="post" role="form" action='main.php'>
            <h4 class="form-signin-heading">Παρακαλώ εισάγετε τα στοιχεία σας:</h4>
            <label for="inputSurname">Επώνυμο</label>
            <input value="" type="text" name="inputSurname" class="form-control" placeholder="Επώνυμο" required autofocus>
            <div class="form-group <?= $wrongAfm ? 'has-error' : '';?>" style="margin:0px;">
              <label for="inputAfm" >ΑΦΜ</label>
              <input id="afm" type="password" name="inputAfm" class="form-control" placeholder="ΑΦΜ" required>
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
</body>
<?php
  require_once('footer.html');
  // Clean up old pdf files
  clean_up($cleanUpAfter);
?>

<?php if (strlen($gAnalytics) > 0): ?>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', '<?= $gAnalytics ?>', 'auto');
    ga('send', 'pageview');
  </script>
<?php endif; ?>
</html>
