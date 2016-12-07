<?php
// Parameters
$dnsiStr = 'Δ/νση Π.Ε. Ηρακλείου';
$dnsiStrShort = 'ΔΙ.Π.Ε. Ηρακλείου';
$dnsiLink = 'http://dipe.ira.sch.gr/site';
// set canLog to 1 to enable logging to file
// file must be .txt to be protected from access
$canLog = 1;
$logFile = 'login_log.txt';
// clean up pdf files after XX minutes
$cleanUpAfter = 10;

// Espa column headers
// ESPA csv is a mess, with duplicate column names, that's why we keep field numbers in this array...
$hdr = Array (
   'Α/Α' => 0, 'Ονοματεπώνυμο' => 1,'ΕΙΔΟΣ ΑΠΑΣΧ.' => 2, 'ΥΠΟΧ. ΩΡΑΡ.' => 3, 'ΔΕ' => 4, 'ΩΡΕΣ ΑΝΑ ΕΒΔ.' => 5, 'ΗΜΕΡΕΣ' => 7, 'ΚΩΔΙΚ.' => 8, 'ΑΦΜ' => 9, 'Μ.Κ.(BM)' => 10,
   'ΕΙΔ. ΑΠ.' => 11, 'ΗΜ.ΑΣΦ.' => 12, 'Μ.Κ.(ΠΟΣΟ)' => 13, 'Ο.Ε.' => 14, 'ΕΠΠ' => 15, 'ΣΥΝΟΛΟ(ΑΠ)' => 16, 'ΦΟΡΟΣ' => 17, 'ΕΚΤΑΚΤΗ ΕΙΣΦΟΡΑ' => 18,
   'ΟΑΕΔ' => 19, 'ΙΚΑ' => 20, 'ΕΡΓΑΖΟΜ.' => 21, 'ΕΡΓΟΔΟΤΗ' => 22, 'ΣΥΝΟΛΟ(ΙΚΑ)' => 23, 'ΤΑΜΕΙΟ' => 24, 'ΕΡΓΑΖΟΜ.(ΤΑΜ)' => 25, 'ΕΡΓΟΔΟΤΗ(ΤΑΜ)' => 26, 'ΣΥΝΟΛΟ(ΤΑΜ)' => 27,
   'ΑΧΡΕΩΣΤ.' => 28, 'ΑΦ.ΠΟΣΟ' => 29, 'ΕΠΙΔ.ΕΡΓ.' => 30, 'ΑΠΟΖ.ΙΚΑ' => 31, 'ΚΑΘΑΡΑ' => 32, 'ΜΕ ΑΠ.ΤΑΜ.' => 33, 'ΜΕ ΑΠ.ΤΑΜ. 2' => 34, 'ΕΝΑΝΤΙ' => 35, 'ΔΙΑΦΟΡΑ' => 36, 'ΜΗΝΑΣ' => 19
);

// custom message to display @ help column
$customMessage = "Η Μηχανογράφηση Π.Ε. Ηρακλείου παρέχει συμπληρωματικά τη δυνατότητα προστασίας του ΑΦΜ με κωδικό, δηλ. την είσοδο στο σύστημα μισθοδοσίας με ΑΦΜ & κωδικό (αντί του επωνύμου).<br>
Περισσότερες πληροφορίες στο τηλ. 2810529301";

// set custom employee codes, to be used instead of surnames
// sample: $customCodes = array('afm1' => 'code1', 'afm2' => 'code2')
$customCodes = array();

?>
